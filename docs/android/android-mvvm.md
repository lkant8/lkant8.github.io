# Android MVVM + Clean Architecture Guide (Kotlin · Compose · Hilt · Retrofit · Room)

> Production-ready reference for building scalable Android apps with MVVM, Clean Architecture, Jetpack Compose UI, Retrofit networking, Room caching, Hilt DI, Coroutines & Flow, and Navigation Compose.

---

## 1. Introduction
**MVVM (Model–View–ViewModel)** separates UI from business logic:
- **Model**: Data sources (remote API, database, preferences) and business rules.
- **View**: UI layer (Jetpack Compose screens) that displays state and forwards user intents.
- **ViewModel**: Holds UI state, coordinates use-cases/repositories, survives configuration changes.

**Why MVVM on Android?**
- Improves **testability** (ViewModel has no Android UI deps).
- Enhances **scalability/maintainability** (clear boundaries, single-responsibility).
- Plays well with **Jetpack** (ViewModel, Flow, Navigation) and **coroutines**.
- Widely recommended for Android (e.g., GeeksforGeeks MVVM article). citeturn0search0

Clean Architecture adds concentric layers so domain stays independent from data/UI, enabling easy swapping of implementations.

---

## 2. Project Structure
```
com.example.app
│
├── data/                     # Data layer (remote, local, repository implementations)
│   ├── remote/               # Retrofit API, DTOs, network mappers
│   ├── local/                # Room entities, DAO, database, converters
│   ├── repository/           # Repository impls combining remote+local
│
├── di/                       # Hilt modules (network, db, repo, dispatcher)
├── domain/                   # (Optional) Use cases, domain models, repository interfaces
├── ui/                       # Compose UI layer
│   ├── navigation/           # NavHost, routes, NavGraph
│   ├── screens/              # Feature screens (splash, auth, dashboard tabs)
│   ├── components/           # Reusable Compose components (buttons, text fields, bars)
│
├── viewmodel/                # ViewModels (per screen/feature)
├── utils/                    # Validators, Result wrappers, extensions
```
- **data**: Single source of truth; merges network + cache.
- **di**: Central place to wire dependencies with Hilt.
- **domain**: Pure-Kotlin rules and interfaces to decouple UI/data.
- **ui**: Pure Compose; consumes immutable state from ViewModels.
- **viewmodel**: State holder + side-effect orchestrator using Flow/Coroutines.
- **utils**: Small helpers (validation, dispatchers, error wrappers).

---

## 3. Network Layer (Retrofit)
### Dependencies (Gradle excerpt)
```kotlin
implementation("com.squareup.retrofit2:retrofit:2.11.0")
implementation("com.squareup.retrofit2:converter-moshi:2.11.0")
implementation("com.squareup.okhttp3:logging-interceptor:5.0.0-alpha.11")
implementation("com.squareup.moshi:moshi-kotlin:1.15.0")
```

### API Service
```kotlin
// data/remote/ApiService.kt
interface ApiService {
    @GET("/users")
    suspend fun getUsers(): List<UserDto>

    @GET("/posts")
    suspend fun getPosts(): List<PostDto>
}
```

### DTOs & Mappers
```kotlin
// data/remote/dto/UserDto.kt
data class UserDto(val id: Int, val name: String, val email: String)

// domain/model/User.kt
data class User(val id: Int, val name: String, val email: String)

// utils/Mappers.kt
fun UserDto.toDomain() = User(id, name, email)
fun UserDto.toEntity() = UserEntity(id, name, email)
```

### Retrofit Provider with Logging & Timeouts
```kotlin
// di/NetworkModule.kt
@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {
    private const val BASE_URL = "https://jsonplaceholder.typicode.com"

    @Provides @Singleton
    fun provideLogging(): HttpLoggingInterceptor =
        HttpLoggingInterceptor().apply { level = HttpLoggingInterceptor.Level.BODY }

    @Provides @Singleton
    fun provideOkHttp(logging: HttpLoggingInterceptor): OkHttpClient =
        OkHttpClient.Builder()
            .connectTimeout(15, TimeUnit.SECONDS)
            .readTimeout(20, TimeUnit.SECONDS)
            .addInterceptor(logging) // remove in prod if noisy
            .build()

    @Provides @Singleton
    fun provideRetrofit(client: OkHttpClient): Retrofit =
        Retrofit.Builder()
            .baseUrl(BASE_URL)
            .client(client)
            .addConverterFactory(MoshiConverterFactory.create())
            .build()

    @Provides @Singleton
    fun provideApi(retrofit: Retrofit): ApiService = retrofit.create(ApiService::class.java)
}
```

### Network Result Wrapper
```kotlin
// utils/Result.kt
sealed class Result<out T> {
    data class Success<T>(val data: T): Result<T>()
    data class Error(val message: String, val throwable: Throwable? = null): Result<Nothing>()
    object Loading: Result<Nothing>()
}
```

### Safe API Call Helper
```kotlin
// utils/SafeCall.kt
suspend fun <T> safeApiCall(block: suspend () -> T): Result<T> =
    try { Result.Success(block()) }
    catch (e: Exception) { Result.Error(e.message ?: "Unknown error", e) }
```

---

## 4. Local Database (Room)
### Dependencies
```kotlin
implementation("androidx.room:room-runtime:2.6.1")
kapt("androidx.room:room-compiler:2.6.1")
implementation("androidx.room:room-ktx:2.6.1")
```

### Entity & DAO
```kotlin
// data/local/entity/UserEntity.kt
@Entity(tableName = "users")
data class UserEntity(
    @PrimaryKey val id: Int,
    val name: String,
    val email: String,
)

// data/local/dao/UserDao.kt
@Dao
interface UserDao {
    @Query("SELECT * FROM users")
    fun observeUsers(): Flow<List<UserEntity>> // cold Flow, emits on DB change

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsertAll(users: List<UserEntity>)
}
```

### Database
```kotlin
// data/local/AppDatabase.kt
@Database(entities = [UserEntity::class], version = 1, exportSchema = false)
abstract class AppDatabase : RoomDatabase() {
    abstract fun userDao(): UserDao
}
```

### DB Provider
```kotlin
// di/DatabaseModule.kt
@Module
@InstallIn(SingletonComponent::class)
object DatabaseModule {
    @Provides @Singleton
    fun provideDb(@ApplicationContext ctx: Context): AppDatabase =
        Room.databaseBuilder(ctx, AppDatabase::class.java, "app.db").build()

    @Provides fun provideUserDao(db: AppDatabase): UserDao = db.userDao()
}
```

### Offline-first Strategy
1. Emit cached data immediately (`UserDao.observeUsers()` Flow).
2. Refresh from network -> write to Room (`upsertAll`) -> Flow auto-emits updated data.
3. UI always observes **single source of truth** (Room).

Room + Flow gives automatic change notifications and backpressure-aware streams.

---

## 5. Repository Pattern (Single Source of Truth)
```kotlin
// data/repository/UserRepository.kt
interface UserRepository {
    fun observeUsers(): Flow<Result<List<User>>>
    suspend fun refreshUsers(): Result<Unit>
}

// data/repository/UserRepositoryImpl.kt
@Singleton
class UserRepositoryImpl @Inject constructor(
    private val api: ApiService,
    private val userDao: UserDao,
    @IoDispatcher private val io: CoroutineDispatcher
) : UserRepository {

    override fun observeUsers(): Flow<Result<List<User>>> = flow {
        emit(Result.Loading)
        userDao.observeUsers()
            .map { list -> list.map { it.toDomain() } }
            .collect { emit(Result.Success(it)) }
    }.catch { emit(Result.Error(it.message ?: "DB error", it)) }
     .flowOn(io)

    override suspend fun refreshUsers(): Result<Unit> = withContext(io) {
        safeApiCall {
            val remote = api.getUsers()
            userDao.upsertAll(remote.map { it.toEntity() })
        }.mapToUnit()
    }
}

// utils/Extensions.kt
fun <T> Result<T>.mapToUnit() = when (this) {
    is Result.Success -> Result.Success(Unit)
    is Result.Error -> this
    Result.Loading -> Result.Loading
}
```
- Repository mediates between API and DB.
- ViewModel triggers `refreshUsers()`; UI collects `observeUsers()` for live updates.

---

## 6. Hilt Dependency Injection
### Setup
1. Gradle:
```kotlin
implementation("com.google.dagger:hilt-android:2.51")
kapt("com.google.dagger:hilt-android-compiler:2.51")
implementation("androidx.hilt:hilt-navigation-compose:1.2.0")
```
2. Application:
```kotlin
@HiltAndroidApp
class App : Application()
```
3. Dispatchers:
```kotlin
@Module @InstallIn(SingletonComponent::class)
object DispatcherModule {
    @IoDispatcher @Provides fun providesIo(): CoroutineDispatcher = Dispatchers.IO
}
@Qualifier annotation class IoDispatcher
```
4. Repository binding:
```kotlin
@Module @InstallIn(SingletonComponent::class)
abstract class RepositoryModule {
    @Binds @Singleton
    abstract fun bindUserRepo(impl: UserRepositoryImpl): UserRepository
}
```
5. ViewModel usage:
```kotlin
@HiltViewModel
class UserViewModel @Inject constructor(
    private val repo: UserRepository
) : ViewModel() { /* ... */ }
```

---

## 7. Flow Deep Dive
### Cold Flow (Flow)
- Starts when collected; work re-executes per collector.
- Use for one-shot tasks (API, DB query).
```kotlin
fun fetchUsersOnce(): Flow<Result<List<User>>> = flow {
    emit(Result.Loading)
    emit(safeApiCall { api.getUsers().map { it.toDomain() } })
}
```

### Hot Flow (StateFlow / SharedFlow)
- Active regardless of collectors; keeps latest state.
- Use for UI state or events.
```kotlin
private val _uiState = MutableStateFlow<UserUiState>(UserUiState.Idle)
val uiState: StateFlow<UserUiState> = _uiState
```

### Cold vs Hot
| Aspect              | Cold Flow            | StateFlow/SharedFlow       |
|---------------------|----------------------|----------------------------|
| Start               | On collect           | Immediately (needs value)  |
| Re-exec per collect | Yes                  | No (shares emissions)      |
| Keeps latest value  | No                   | StateFlow yes, SharedFlow configurable |
| Typical usage       | API/DB operations    | UI state, events, timers   |

Use cold Flow inside repositories; expose StateFlow from ViewModels for Compose.

---

## 8. ViewModel (State, Loading, Error)
```kotlin
@HiltViewModel
class UserViewModel @Inject constructor(
    private val repo: UserRepository
) : ViewModel() {

    sealed interface UserUiState {
        object Idle : UserUiState
        object Loading : UserUiState
        data class Success(val users: List<User>) : UserUiState
        data class Error(val message: String) : UserUiState
    }

    private val _state = MutableStateFlow<UserUiState>(UserUiState.Idle)
    val state: StateFlow<UserUiState> = _state

    init { observeUsers() }

    fun refresh() = viewModelScope.launch {
        when (val res = repo.refreshUsers()) {
            is Result.Error -> _state.value = UserUiState.Error(res.message)
            else -> Unit
        }
    }

    private fun observeUsers() {
        viewModelScope.launch {
            repo.observeUsers().collect { result ->
                _state.value = when (result) {
                    is Result.Loading -> UserUiState.Loading
                    is Result.Error -> UserUiState.Error(result.message)
                    is Result.Success -> UserUiState.Success(result.data)
                }
            }
        }
    }
}
```
- Uses **StateFlow** for Compose `collectAsState()`.
- Handles loading/error centrally.

---

## 9. Navigation (Compose)
### Routes
```kotlin
object Routes {
    const val Splash = "splash"
    const val SignIn = "signin"
    const val SignUp = "signup"
    const val Dashboard = "dashboard"
    const val Home = "home"
    const val Explore = "explore"
    const val Search = "search"
    const val Settings = "settings"
}
```

### NavGraph
```kotlin
@Composable
fun AppNavGraph(startDestination: String = Routes.Splash) {
    val navController = rememberNavController()
    NavHost(navController, startDestination) {
        composable(Routes.Splash) {
            SplashScreen(onFinished = {
                navController.navigate(Routes.SignIn) {
                    popUpTo(Routes.Splash) { inclusive = true }
                }
            })
        }
        composable(Routes.SignIn) {
            SignInScreen(
                onSignIn = {
                    navController.navigate(Routes.Dashboard) {
                        popUpTo(Routes.SignIn) { inclusive = true }
                    }
                },
                onSignUp = { navController.navigate(Routes.SignUp) }
            )
        }
        composable(Routes.SignUp) { SignUpScreen(onSignedUp = { navController.popBackStack() }) }
        composable(Routes.Dashboard) { DashboardScreen(navController) }
    }
}
```

---

## 10. UI Screens (Compose)
### Common TextField Component
```kotlin
@Composable
fun AppTextField(value: String, onValueChange: (String) -> Unit, label: String, error: String?) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        isError = error != null,
        modifier = Modifier.fillMaxWidth(),
        supportingText = { error?.let { Text(it, color = MaterialTheme.colorScheme.error, fontSize = 12.sp) } }
    )
}
```

### 10.1 Splash Screen
```kotlin
@Composable
fun SplashScreen(onFinished: () -> Unit) {
    LaunchedEffect(Unit) {
        delay(1200)
        onFinished()
    }
    Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
        Text("MyApp", style = MaterialTheme.typography.headlineMedium)
    }
}
```

### 10.2 Sign In Screen with Validation
```kotlin
@Composable
fun SignInScreen(onSignIn: () -> Unit, onSignUp: () -> Unit) {
    var email by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }
    var errors by remember { mutableStateOf(listOf<String>()) }

    fun validate(): Boolean {
        val errs = buildList {
            if (!email.contains("@")) add("Invalid email")
            if (password.length < 6) add("Password too short")
        }
        errors = errs; return errs.isEmpty()
    }

    Column(
        modifier = Modifier.padding(16.dp).fillMaxSize(),
        verticalArrangement = Arrangement.Center
    ) {
        Text("Sign In", style = MaterialTheme.typography.headlineMedium)
        Spacer(Modifier.height(16.dp))
        AppTextField(email, { email = it }, "Email", errors.find { it.contains("email", true) })
        Spacer(Modifier.height(8.dp))
        AppTextField(password, { password = it }, "Password", errors.find { it.contains("Password") })
        Spacer(Modifier.height(16.dp))
        Button(onClick = { if (validate()) onSignIn() }, modifier = Modifier.fillMaxWidth()) {
            Text("Sign In")
        }
        TextButton(onClick = onSignUp, modifier = Modifier.align(Alignment.End)) { Text("Create account") }
        if (errors.isNotEmpty()) Text(errors.joinToString(), color = MaterialTheme.colorScheme.error)
    }
}
```

### 10.3 Sign Up Screen
```kotlin
@Composable
fun SignUpScreen(onSignedUp: () -> Unit) {
    var email by rememberSaveable { mutableStateOf("") }
    var password by rememberSaveable { mutableStateOf("") }
    var confirm by rememberSaveable { mutableStateOf("") }
    var errors by remember { mutableStateOf(listOf<String>()) }

    fun validate(): Boolean {
        val errs = buildList {
            if (!email.contains("@")) add("Invalid email")
            if (password.length < 6) add("Password too short")
            if (password != confirm) add("Passwords do not match")
        }
        errors = errs; return errs.isEmpty()
    }

    Column(Modifier.padding(16.dp).fillMaxSize(), verticalArrangement = Arrangement.Center) {
        Text("Sign Up", style = MaterialTheme.typography.headlineMedium)
        Spacer(Modifier.height(16.dp))
        AppTextField(email, { email = it }, "Email", errors.find { it.contains("email", true) })
        Spacer(Modifier.height(8.dp))
        AppTextField(password, { password = it }, "Password", errors.find { it.contains("Password too short") })
        Spacer(Modifier.height(8.dp))
        AppTextField(confirm, { confirm = it }, "Confirm Password", errors.find { it.contains("match") })
        Spacer(Modifier.height(16.dp))
        Button(onClick = { if (validate()) onSignedUp() }, modifier = Modifier.fillMaxWidth()) {
            Text("Create Account")
        }
        if (errors.isNotEmpty()) Text(errors.joinToString(), color = MaterialTheme.colorScheme.error)
    }
}
```

### 10.4 Dashboard with Bottom Navigation
```kotlin
@Composable
fun DashboardScreen(navController: NavController) {
    val tabs = listOf(Routes.Home, Routes.Explore, Routes.Search, Routes.Settings)
    var current by remember { mutableStateOf(Routes.Home) }

    Scaffold(
        bottomBar = {
            NavigationBar {
                tabs.forEach { route ->
                    NavigationBarItem(
                        selected = current == route,
                        onClick = { current = route },
                        label = { Text(route) },
                        icon = { Icon(Icons.Default.Home, contentDescription = null) }
                    )
                }
            }
        }
    ) { padding ->
        Box(Modifier.padding(padding)) {
            when (current) {
                Routes.Home -> HomeScreen()
                Routes.Explore -> ExploreScreen()
                Routes.Search -> SearchScreen()
                Routes.Settings -> SettingsScreen()
            }
        }
    }
}
```

#### Tab Composables (stateful where needed)
```kotlin
@Composable fun HomeScreen() { Text("Home") }
@Composable fun ExploreScreen() { Text("Explore") }
@Composable fun SearchScreen() { Text("Search") }
@Composable fun SettingsScreen() { Text("Settings") }
```

---

## 11. End-to-End Data Flow
```
User action -> ViewModel.refresh()
  -> Repository.refreshUsers()
    -> safeApiCall(api.getUsers())
    -> Room.upsertAll()
  -> Room emits new data via UserDao.observeUsers()
  -> ViewModel maps to UiState (StateFlow)
  -> Compose collects StateFlow -> recomposes UI
```

Text-based clean architecture diagram:
```
[UI (Compose)] <--> [ViewModel + StateFlow]
        |                   |
        |            uses Repository (interfaces)
        |                   |
        |           [Data Layer: remote + local]
        |             /                    \
        |      Retrofit(API)         Room(Database)
        |             \                    /
        |                 Single Source of Truth
        v
   Domain models / Use-cases (optional layer)
```

---

## 12. Bonus Features
- **Form validation**: shown in SignIn/SignUp.
- **Loading indicators**: use `UserUiState.Loading` -> `CircularProgressIndicator`.
- **Error UI**: show snackbar/dialog on `UserUiState.Error`.
- **Simple API**: Users/Posts endpoints ready for extension.

Example loading/error handling in Home:
```kotlin
@Composable
fun HomeScreen(viewModel: UserViewModel = hiltViewModel()) {
    val state by viewModel.state.collectAsState()
    when (state) {
        is UserViewModel.UserUiState.Loading -> CircularProgressIndicator()
        is UserViewModel.UserUiState.Error -> Text(
            (state as UserViewModel.UserUiState.Error).message,
            color = Color.Red
        )
        is UserViewModel.UserUiState.Success -> LazyColumn {
            items((state as UserViewModel.UserUiState.Success).users) { Text(it.name) }
        }
        else -> Unit
    }
}
```

---

## 13. Ready-to-Copy Checklist
- [x] Hilt set up (`@HiltAndroidApp`, modules, qualifiers).
- [x] Retrofit with Moshi + logging + base URL.
- [x] Room (Entity, DAO, DB, Flow support).
- [x] Repository merges API + Room (single source of truth).
- [x] ViewModel exposes `StateFlow` for UI.
- [x] Navigation Compose graph with splash/auth/dashboard.
- [x] Bottom navigation tabs with state.
- [x] Validation, loading, error UI patterns.

---

## 14. Copy-Paste Starter MainActivity
```kotlin
@AndroidEntryPoint
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent { MaterialTheme { AppNavGraph() } }
    }
}
```

---

## 15. Key Best Practices
- Keep **ViewModel** free of Android UI types; expose immutable `StateFlow`.
- Repository is the only layer aware of both network and database.
- Prefer **suspend** APIs and **Flow** for streams.
- Use **flowOn(Dispatchers.IO)** for heavy work; UI stays on Main.
- Handle errors centrally via sealed `Result` and surface user-friendly messages.
- Keep navigation routes in a single object to avoid typos.
- Write unit tests for mappers and repository logic; UI tests for navigation flows.

---

## 16. References
- Official Android docs (ViewModel, Hilt, Room, Navigation Compose, Coroutines).
- Community patterns and MVVM rationale (e.g., GeeksforGeeks article). citeturn0search0

