# 100 Kotlin Tricks for Android

Each trick includes a short why, code, and an Android-flavored use case.

## 1. Safe Trim Extension
- Explanation: Avoids NPE while cleaning text input.
- Code:
```kotlin
fun String?.safeTrim() = this?.trim().orEmpty()
```
- Use case: Sanitizing EditText input before validation.
- Why it matters: Removes boilerplate null checks and improves readability.

## 2. Int.dp to Px
- Explanation: Quick density conversion.
- Code:
```kotlin
val Int.dp: Int get() = (this * Resources.getSystem().displayMetrics.density).roundToInt()
```
- Use case: Building custom views without Context.
- Why it matters: Keeps layout math concise.

## 3. Px to Dp Extension
- Explanation: Reverse conversion for measuring runtime sizes.
- Code:
```kotlin
val Int.pxToDp: Float get() = this / Resources.getSystem().displayMetrics.density
```
- Use case: Logging sizes for UI debugging.
- Why it matters: Prevents magic numbers and mis-scaled UI.

## 4. Nullable Boolean orFalse
- Explanation: Collapse nullable booleans safely.
- Code:
```kotlin
fun Boolean?.orFalse() = this ?: false
```
- Use case: Feature flags fetched from remote config.
- Why it matters: Avoids double-bang (!!) crashes.

## 5. ifTrue Inline
- Explanation: Execute block only when condition true.
- Code:
```kotlin
inline fun Boolean.ifTrue(block: () -> Unit) { if (this) block() }
```
- Use case: Toggle debug overlays.
- Why it matters: Improves intent and reduces nesting.

## 6. String.takeIfNotBlank
- Explanation: Keep non-blank strings else null.
- Code:
```kotlin
fun String.takeIfNotBlank(): String? = takeIf { it.isNotBlank() }
```
- Use case: Optional form fields.
- Why it matters: Cleaner nullable handling.

## 7. requireNotNullWithMessage
- Explanation: Inline require with lazy message.
- Code:
```kotlin
inline fun <T> requireNotNullLazy(value: T?, lazyMsg: () -> String): T =
    requireNotNull(value, lazyMsg)
```
- Use case: Constructor preconditions.
- Why it matters: Lazy messages avoid string building cost.

## 8. checkNotNullLog
- Explanation: Check and log before throwing.
- Code:
```kotlin
inline fun <T> checkNotNullLog(value: T?, tag: String, msg: () -> String): T {
    if (value == null) Log.e(tag, msg())
    return checkNotNull(value, msg)
}
```
- Use case: Defensive coding in repository layer.
- Why it matters: Keeps crash clues.

## 9. buildString DSL
- Explanation: Idiomatic string builder.
- Code:
```kotlin
fun userDisplay(name: String, age: Int) = buildString {
    appendLine(name)
    append("Age: $age")
}
```
- Use case: Composing snack bar messages.
- Why it matters: Faster than string concatenation in loops.

## 10. runCatchingMap
- Explanation: Map Result success without re-wrapping.
- Code:
```kotlin
inline fun <T, R> Result<T>.mapCatching(transform: (T) -> R): Result<R> =
    fold(onSuccess = { runCatching { transform(it) } }, onFailure = { Result.failure(it) })
```
- Use case: Repository transformations.
- Why it matters: Keeps Result chain pure.

## 11. retrySuspend
- Explanation: Suspends with retries and delay.
- Code:
```kotlin
suspend fun <T> retry(times: Int, delayMs: Long, block: suspend () -> T): T {
    repeat(times - 1) { i ->
        runCatching { return block() }.onFailure { delay(delayMs * (i + 1)) }
    }
    return block()
}
```
- Use case: Retrying network calls.
- Why it matters: Simple exponential-ish backoff.

## 12. coroutineLaunchIO
- Explanation: Shortcut for IO dispatcher launch.
- Code:
```kotlin
fun CoroutineScope.launchIO(block: suspend CoroutineScope.() -> Unit) =
    launch(Dispatchers.IO, block = block)
```
- Use case: Repository disk operations.
- Why it matters: Reduces verbosity and mistakes.

## 13. withMain
- Explanation: Switch to Main dispatcher inline.
- Code:
```kotlin
suspend fun <T> withMain(block: suspend () -> T) = withContext(Dispatchers.Main, block)
```
- Use case: Updating UI after background work.
- Why it matters: Prevents thread violations.

## 14. parallelMap
- Explanation: Run collection operations concurrently.
- Code:
```kotlin
suspend fun <T, R> Iterable<T>.parallelMap(block: suspend (T) -> R): List<R> = coroutineScope {
    map { async { block(it) } }.awaitAll()
}
```
- Use case: Fetching multiple endpoints.
- Why it matters: Utilizes concurrency safely.

## 15. debounce Flow Extension
- Explanation: Debounce with default dispatcher.
- Code:
```kotlin
fun <T> Flow<T>.debounceInput(ms: Long = 300) = debounce(ms)
```
- Use case: Search box requests.
- Why it matters: Saves bandwidth and CPU.

## 16. throttleFirst Flow
- Explanation: Emit first then silence window.
- Code:
```kotlin
fun <T> Flow<T>.throttleFirst(windowMs: Long) =
    transformLatest { value -> emit(value); delay(windowMs) }
```
- Use case: Button spam prevention.
- Why it matters: Prevents duplicate actions.

## 17. distinctUntilChangedByKey
- Explanation: Skip repeats by selector.
- Code:
```kotlin
fun <T, K> Flow<T>.distinctBy(key: (T) -> K) = distinctUntilChanged { a, b -> key(a) == key(b) }
```
- Use case: UI state updates by id.
- Why it matters: Cuts recompositions.

## 18. shareWhileSubscribed
- Explanation: Share flow only when active.
- Code:
```kotlin
fun <T> Flow<T>.shareHot(scope: CoroutineScope) =
    shareIn(scope, SharingStarted.WhileSubscribed(5000), replay = 1)
```
- Use case: ViewModel data streams.
- Why it matters: Saves work when no observers.

## 19. stateInDefault
- Explanation: Convert to StateFlow with default.
- Code:
```kotlin
fun <T> Flow<T>.toState(scope: CoroutineScope, initial: T) =
    stateIn(scope, SharingStarted.WhileSubscribed(5000), initial)
```
- Use case: Exposing UI state.
- Why it matters: Ensures initial value and cancellation awareness.

## 20. Flow retryWithBackoff
- Explanation: Operator with exponential backoff.
- Code:
```kotlin
fun <T> Flow<T>.retryBackoff(times: Long = 3) = retry(times) { cause, attempt ->
    delay((attempt + 1) * 300)
    cause is IOException
}
```
- Use case: Streaming network data.
- Why it matters: More resilient flows.

## 21. Sealed UiState
- Explanation: Single source for UI render states.
- Code:
```kotlin
sealed interface UiState<out T> {
    object Loading: UiState<Nothing>
    data class Success<T>(val data: T): UiState<T>
    data class Error(val message: String): UiState<Nothing>
}
```
- Use case: ViewModel exposures.
- Why it matters: Exhaustive when rendering.

## 22. Sealed Intent
- Explanation: Model user intents as sealed types.
- Code:
```kotlin
sealed interface Intent { data class Click(val id: Long): Intent; object Refresh: Intent }
```
- Use case: MVI architectures.
- Why it matters: Compiler checks for new actions.

## 23. Data class copy with defaults helper
- Explanation: Provide safe copy with optional params.
- Code:
```kotlin
data class User(val name: String, val age: Int)
fun User.update(name: String = this.name, age: Int = this.age) = copy(name = name, age = age)
```
- Use case: Reducers.
- Why it matters: Avoids repeating property list.

## 24. Inline class for ids
- Explanation: Type-safe ids without runtime cost.
- Code:
```kotlin
@JvmInline value class UserId(val value: Long)
```
- Use case: Distinguish different Long ids.
- Why it matters: Prevents parameter mixups.

## 25. Delegated preference
- Explanation: Custom delegate to SharedPreferences.
- Code:
```kotlin
class PrefDelegate<T>(private val prefs: SharedPreferences, private val key: String, private val default: T) {
    operator fun getValue(thisRef: Any?, property: KProperty<*>) = when(default){
        is Int -> prefs.getInt(key, default) as T
        is Boolean -> prefs.getBoolean(key, default) as T
        else -> prefs.getString(key, default as? String) as T
    }
    operator fun setValue(thisRef: Any?, property: KProperty<*>, value: T) {
        with(prefs.edit()) {
            when(value){
                is Int -> putInt(key, value)
                is Boolean -> putBoolean(key, value)
                is String -> putString(key, value)
            }
        }.apply()
    }
}
```
- Use case: ViewModel config flags.
- Why it matters: Removes preference boilerplate.

## 26. lazyNonThreadSafe
- Explanation: Faster lazy when single-threaded.
- Code:
```kotlin
val vm by lazy(LazyThreadSafetyMode.NONE) { createVm() }
```
- Use case: Fragment properties.
- Why it matters: Avoids synchronization overhead.

## 27. observableState
- Explanation: Observe property changes.
- Code:
```kotlin
var query: String by Delegates.observable("") { _, _, new -> onQueryChange(new) }
```
- Use case: Search UI updates.
- Why it matters: Simplifies listener wiring.

## 28. vetoableRange
- Explanation: Prevent invalid assignment.
- Code:
```kotlin
var volume: Int by Delegates.vetoable(5) { _, _, new -> new in 0..10 }
```
- Use case: Settings sliders.
- Why it matters: Guards invariants.

## 29. by map delegate
- Explanation: Map-backed model initialization.
- Code:
```kotlin
class User(map: Map<String, Any?>) {
    val name: String by map
    val age: Int by map
}
```
- Use case: Parsing JSON to model quickly.
- Why it matters: Removes manual assignments.

## 30. Kotlin DSL builder
- Explanation: Simple DSL via lambdas with receiver.
- Code:
```kotlin
class RequestBuilder { var url=""; val headers=mutableMapOf<String,String>() }
fun request(block: RequestBuilder.() -> Unit) = RequestBuilder().apply(block)
```
- Use case: Building Retrofit requests in tests.
- Why it matters: Declarative and readable.

## 31. inline measureDuration
- Explanation: Time any block.
- Code:
```kotlin
inline fun <T> measureMs(block: () -> T): Pair<T, Long> {
    val start = SystemClock.elapsedRealtime()
    val result = block()
    return result to (SystemClock.elapsedRealtime() - start)
}
```
- Use case: Profiling slow UI code.
- Why it matters: Lightweight instrumentation.

## 32. nonEmptyListOf
- Explanation: Enforce non-empty collections at creation.
- Code:
```kotlin
fun <T> nonEmptyListOf(first: T, vararg rest: T) = listOf(first, *rest)
```
- Use case: Adapter data needing at least one item.
- Why it matters: Avoids empty state crashes.

## 33. runIf
- Explanation: Inline conditional execution with return value.
- Code:
```kotlin
inline fun <T> T.runIf(condition: Boolean, block: T.() -> T) = if (condition) block() else this
```
- Use case: Add headers only when logged in.
- Why it matters: Keeps pipelines fluent.

## 34. takeUnlessNullOrEmpty
- Explanation: Keep list unless empty.
- Code:
```kotlin
fun <T> List<T>.takeUnlessEmpty(): List<T>? = takeIf { it.isNotEmpty() }
```
- Use case: Optional sections in Compose.
- Why it matters: Avoids redundant branches.

## 35. stringBuilderUse
- Explanation: Use `use` with StringWriter for IO safety.
- Code:
```kotlin
fun InputStream.readText(): String = bufferedReader().use { it.readText() }
```
- Use case: Asset file loading.
- Why it matters: Closes streams automatically.

## 36. suspendRunCatching
- Explanation: Suspend-aware Result wrapper.
- Code:
```kotlin
suspend inline fun <T> runSuspendCatching(block: suspend () -> T) =
    try { Result.success(block()) } catch (t: Throwable) { Result.failure(t) }
```
- Use case: Repository network calls.
- Why it matters: Consistent error pipeline.

## 37. combineLatest3
- Explanation: Combine three flows succinctly.
- Code:
```kotlin
fun <A,B,C,R> combine3(f1: Flow<A>, f2: Flow<B>, f3: Flow<C>, transform: suspend (A,B,C)->R) =
    combine(f1, f2, f3, transform)
```
- Use case: UI state from multiple sources.
- Why it matters: Avoid nested combines.

## 38. MutableStateFlow.update
- Explanation: Atomic update helper.
- Code:
```kotlin
inline fun <T> MutableStateFlow<T>.updateState(transform: (T) -> T) { value = transform(value) }
```
- Use case: Reducers in ViewModel.
- Why it matters: Less ceremony than emit.

## 39. SharedFlow emitLatest
- Explanation: Drop buffer and emit latest.
- Code:
```kotlin
suspend fun <T> MutableSharedFlow<T>.emitLatest(value: T) {
    tryEmit(value) || emit(value)
}
```
- Use case: Navigation events.
- Why it matters: Avoids lost emissions.

## 40. inline crossinline builder
- Explanation: Prevent non-local returns in DSL.
- Code:
```kotlin
inline fun <T> withSafety(crossinline block: () -> T): T = block()
```
- Use case: Callback-based APIs.
- Why it matters: Safer control flow.

## 41. reified cast
- Explanation: Type-safe cast without class token.
- Code:
```kotlin
inline fun <reified T> Any?.cast(): T? = this as? T
```
- Use case: Parsing bundle extras.
- Why it matters: Eliminates Class<T> plumbing.

## 42. reified Gson
- Explanation: Generic JSON decode.
- Code:
```kotlin
inline fun <reified T> Gson.fromJson(json: String): T = fromJson(json, object: TypeToken<T>(){}.type)
```
- Use case: Cache decoding.
- Why it matters: One-liner decode.

## 43. reified ViewModel lookup
- Explanation: Simplify ViewModel retrieval.
- Code:
```kotlin
inline fun <reified VM: ViewModel> Fragment.viewModel(): VM = viewModels<VM>().value
```
- Use case: Fragment scoped VMs.
- Why it matters: Removes factory boilerplate.

## 44. fragmentArgs delegate
- Explanation: Property delegate for fragment args.
- Code:
```kotlin
class FragmentArgs<T: Serializable>(private val key: String) {
    operator fun getValue(thisRef: Fragment, property: KProperty<*>) =
        thisRef.arguments?.getSerializable(key) as T
}
```
- Use case: Passing small payloads.
- Why it matters: Safer than manual casts.

## 45. weak reference callback
- Explanation: Prevent leaks for listeners.
- Code:
```kotlin
class WeakCallback<T: Any>(target: T) {
    private val ref = WeakReference(target)
    fun invoke(block: (T) -> Unit) { ref.get()?.let(block) }
}
```
- Use case: Long-lived managers holding Activity callbacks.
- Why it matters: Avoids memory leaks.

## 46. inline onApi
- Explanation: Run code only on min API.
- Code:
```kotlin
inline fun onApi(api: Int, block: () -> Unit) { if (Build.VERSION.SDK_INT >= api) block() }
```
- Use case: Feature gating.
- Why it matters: Keeps compatibility checks tidy.

## 47. enumSafeValueOf
- Explanation: Safe enum parsing.
- Code:
```kotlin
inline fun <reified T: Enum<T>> enumOrNull(name: String) = enumValues<T>().firstOrNull { it.name == name }
```
- Use case: Parsing remote enum strings.
- Why it matters: Avoids exceptions.

## 48. BigDecimal.exactOrNull
- Explanation: Convert string to BigDecimal safely.
- Code:
```kotlin
fun String.toBigDecimalOrNull() = runCatching { toBigDecimal() }.getOrNull()
```
- Use case: Price inputs.
- Why it matters: Prevents crashes on invalid numbers.

## 49. Guard clause function
- Explanation: Early return helper.
- Code:
```kotlin
inline fun guard(condition: Boolean, block: () -> Unit) { if (condition) block(); else return }
```
- Use case: Validation chains.
- Why it matters: Reduces nesting.

## 50. requireMainThread
- Explanation: Enforce main thread execution.
- Code:
```kotlin
fun requireMainThread() = check(Looper.myLooper() == Looper.getMainLooper()) { "Not main" }
```
- Use case: UI operations.
- Why it matters: Catches threading bugs.

## 51. LazyFlow
- Explanation: Create flow that runs on first collect.
- Code:
```kotlin
fun <T> lazyFlow(block: suspend FlowCollector<T>.() -> Unit) = flow(block)
```
- Use case: Deferred expensive fetch.
- Why it matters: Avoids work until needed.

## 52. Flow catchMap
- Explanation: Map errors to state objects.
- Code:
```kotlin
fun <T> Flow<T>.catchState() = catch { emitAll(flowOf()) }
```
- Use case: Emit empty on error for UI.
- Why it matters: Prevents cancellations.

## 53. flowOnBoundary
- Explanation: Split upstream/downstream dispatchers.
- Code:
```kotlin
flow { emit(fetch()) }.flowOn(Dispatchers.IO).map { transform(it) }
```
- Use case: Network then UI mapping.
- Why it matters: Correct dispatcher usage.

## 54. rememberCoroutineScope in Compose
- Explanation: Launch coroutines scoped to composition.
- Code:
```kotlin
@Composable
fun LoadButton() {
    val scope = rememberCoroutineScope()
    Button({ scope.launch { load() } }) { Text("Load") }
}
```
- Use case: Fire-and-forget actions.
- Why it matters: Avoids leaking jobs.

## 55. derivedStateOf for expensive calcs
- Explanation: Memoize derived state.
- Code:
```kotlin
val filtered by remember(items, query) { derivedStateOf { items.filter { it.contains(query) } } }
```
- Use case: Filtering lists in Compose.
- Why it matters: Cuts recomposition cost.

## 56. snapshotFlow bridging
- Explanation: Convert Compose state to Flow.
- Code:
```kotlin
val scrollFlow = rememberLazyListState().let { state -> snapshotFlow { state.firstVisibleItemIndex } }
```
- Use case: Paging analytics.
- Why it matters: Observes UI state reactively.

## 57. LaunchedEffect with keys
- Explanation: Restart effect when key changes.
- Code:
```kotlin
LaunchedEffect(userId) { loadUser(userId) }
```
- Use case: Detail screens.
- Why it matters: Avoids stale jobs.

## 58. rememberSaveable state holder
- Explanation: Save state across process death.
- Code:
```kotlin
var text by rememberSaveable { mutableStateOf("") }
```
- Use case: Form fields.
- Why it matters: Survives recreation.

## 59. MutableStateFlow.asStateFlow
- Explanation: Expose read-only view.
- Code:
```kotlin
private val _state = MutableStateFlow(UiState.Loading)
val state = _state.asStateFlow()
```
- Use case: ViewModel encapsulation.
- Why it matters: Prevents external mutation.

## 60. combineLatestList
- Explanation: Combine dynamic list of flows.
- Code:
```kotlin
fun <T> combineList(flows: List<Flow<T>>): Flow<List<T>> = combine(flows) { it.toList() }
```
- Use case: Aggregating multiple sources.
- Why it matters: Scales with inputs.

## 61. Mutex protect
- Explanation: Protect critical section in coroutines.
- Code:
```kotlin
val mutex = Mutex()
suspend fun safeWrite(block: suspend () -> Unit) = mutex.withLock { block() }
```
- Use case: Cache writes.
- Why it matters: Avoids races without threads.

## 62. Channel conflated sendLatest
- Explanation: Conflate channel to keep last value.
- Code:
```kotlin
val channel = Channel<Int>(Channel.CONFLATED)
channel.trySend(1)
```
- Use case: Progress updates.
- Why it matters: Drops redundant work.

## 63. CallbackFlow adapter
- Explanation: Bridge callback APIs to Flow.
- Code:
```kotlin
fun locationFlow() = callbackFlow {
    val cb = LocationCallback { trySend(it) }
    start(cb)
    awaitClose { stop(cb) }
}
```
- Use case: Location updates.
- Why it matters: Structured cancellation.

## 64. suspendCancellableCoroutine
- Explanation: Wrap async callback with cancellation.
- Code:
```kotlin
suspend fun awaitTask(task: Task<Int>) = suspendCancellableCoroutine<Int> { cont ->
    task.addOnSuccessListener { cont.resume(it) }
    task.addOnFailureListener { cont.resumeWithException(it) }
    cont.invokeOnCancellation { task.cancel() }
}
```
- Use case: Firebase Tasks.
- Why it matters: Proper coroutine interop.

## 65. Flow buffer tuning
- Explanation: Add buffer to prevent backpressure.
- Code:
```kotlin
flowProducer.buffer(capacity = Channel.BUFFERED)
```
- Use case: Sensor streams.
- Why it matters: Avoids missed samples.

## 66. sealed Result with domain errors
- Explanation: Domain-specific error channel.
- Code:
```kotlin
sealed interface RepoResult<out T> { data class Ok<T>(val data: T): RepoResult<T>; data class Network(val cause: Throwable): RepoResult<Nothing> }
```
- Use case: UI error handling.
- Why it matters: More expressive than Boolean flags.

## 67. inline contract for returns
- Explanation: Improve smart casting.
- Code:
```kotlin
@OptIn(ExperimentalContracts::class)
inline fun <T> T?.exists(): Boolean {
    contract { returns(true) implies (this@exists != null) }
    return this != null
}
```
- Use case: Validation helpers.
- Why it matters: Cleaner smart casts.

## 68. sequence for laziness
- Explanation: Use Sequence for large data.
- Code:
```kotlin
val lines = File(path).useLines { seq -> seq.filter { it.isNotBlank() }.toList() }
```
- Use case: Large log parsing.
- Why it matters: Lower memory footprint.

## 69. tailrec factorial
- Explanation: Tail recursion optimization.
- Code:
```kotlin
tailrec fun fact(n: Int, acc: Long = 1): Long = if (n <= 1) acc else fact(n-1, acc*n)
```
- Use case: Math utilities.
- Why it matters: Avoids stack overflow.

## 70. Partial function application
- Explanation: Pre-bind parameters.
- Code:
```kotlin
fun <A,B,C> ((A,B)->C).partial(a: A) = { b: B -> this(a,b) }
```
- Use case: Preconfiguring mappers.
- Why it matters: Reusable functions.

## 71. Pipe operator
- Explanation: Function composition helper.
- Code:
```kotlin
infix fun <T,R> T.pipe(f: (T)->R) = f(this)
```
- Use case: Transform chains in pipelines.
- Why it matters: Readable transformations.

## 72. Currying
- Explanation: Convert binary function to unary chain.
- Code:
```kotlin
fun <A,B,C> ((A,B)->C).curry() = { a: A -> { b: B -> this(a,b) } }
```
- Use case: Reusable predicates.
- Why it matters: Encourages FP patterns.

## 73. memoize
- Explanation: Cache pure function outputs.
- Code:
```kotlin
fun <P, R> ((P) -> R).memoize(): (P) -> R {
    val cache = mutableMapOf<P, R>()
    return { p -> cache.getOrPut(p) { this(p) } }
}
```
- Use case: Expensive formatting.
- Why it matters: Performance boost.

## 74. Either type alias
- Explanation: Represent success/failure without exceptions.
- Code:
```kotlin
typealias Either<L, R> = Result<Pair<L?, R?>>
```
- Use case: Domain errors.
- Why it matters: Better than throwing.

## 75. Kotlin contracts for callbacks
- Explanation: Non-null guarantees for callbacks.
- Code:
```kotlin
@OptIn(ExperimentalContracts::class)
inline fun <T> withCallback(value: T?, block: (T) -> Unit) {
    contract { callsInPlace(block, InvocationKind.AT_MOST_ONCE) }
    value?.let(block)
}
```
- Use case: Optional listeners.
- Why it matters: Compiler smart casts listener.

## 76. Inline property accessor
- Explanation: Avoids method call overhead.
- Code:
```kotlin
var cache: Int
    inline get() = field
    inline set(value) { field = value }
```
- Use case: Hot getters.
- Why it matters: Micro-optimizations in tight loops.

## 77. lateinit safety check
- Explanation: Verify initialization before use.
- Code:
```kotlin
fun ::someLateinit.isInitializedSafe() = this.isInitialized
```
- Use case: Unit tests checking injection.
- Why it matters: Avoids runtime exceptions.

## 78. object expression for lightweight singletons
- Explanation: Short-lived object instead of class.
- Code:
```kotlin
val comparator = object : Comparator<User> { override fun compare(a: User, b: User) = a.name.compareTo(b.name) }
```
- Use case: Custom sort.
- Why it matters: No extra class files.

## 79. useResource
- Explanation: Generic closeable handler.
- Code:
```kotlin
inline fun <T: Closeable, R> T.useAndClose(block: (T)->R): R = use(block)
```
- Use case: Cursor usage.
- Why it matters: Prevent leaks.

## 80. Parcelable via @Parcelize
- Explanation: Simplify parcelable models.
- Code:
```kotlin
@Parcelize data class User(val id: Long, val name: String): Parcelable
```
- Use case: Navigation arguments.
- Why it matters: Removes boilerplate CREATOR code.

## 81. Result.unwrapOrNull
- Explanation: Get value or null.
- Code:
```kotlin
fun <T> Result<T>.getOrNullLogging(tag: String) = onFailure { Log.e(tag, it.message ?: "") }.getOrNull()
```
- Use case: Optional pipelines.
- Why it matters: Centralized logging.

## 82. combineLatest with debounce
- Explanation: Debounce combined emissions.
- Code:
```kotlin
combine(f1, f2) { a, b -> a to b }.debounce(100)
```
- Use case: Form validation on two fields.
- Why it matters: Reduces validator churn.

## 83. Flow.onStart loading state
- Explanation: Emit loading before work.
- Code:
```kotlin
fun <T> Flow<T>.withLoading(onLoading: () -> Unit) = onStart { onLoading() }
```
- Use case: Showing progress bar.
- Why it matters: Consistent UX.

## 84. Flow.onCompletion hide loading
- Explanation: Hide spinner when complete.
- Code:
```kotlin
onCompletion { onDone() }
```
- Use case: UI progress toggles.
- Why it matters: Prevents stuck loading.

## 85. Lifecycle-aware flow collection
- Explanation: Collect flow tied to LifecycleOwner.
- Code:
```kotlin
fun <T> Flow<T>.collectIn(owner: LifecycleOwner, block: (T)->Unit) {
    owner.lifecycleScope.launchWhenStarted { collect { block(it) } }
}
```
- Use case: Activities and fragments.
- Why it matters: Auto-cancel on stop.

## 86. DiffUtil extension
- Explanation: Quick DiffUtil callback.
- Code:
```kotlin
inline fun <T> diffUtil(crossinline same: (T,T)->Boolean, crossinline content: (T,T)->Boolean) = object: DiffUtil.ItemCallback<T>(){
    override fun areItemsTheSame(o: T, n: T)=same(o,n); override fun areContentsTheSame(o:T,n:T)=content(o,n)
}
```
- Use case: RecyclerView adapters.
- Why it matters: Less ceremony for stable ids.

## 87. lazy fast sequence builder
- Explanation: Create sequences for streams.
- Code:
```kotlin
fun fibonacci() = sequence {
    var a=0; var b=1
    yield(a); yield(b)
    while(true){ val c=a+b; yield(c); a=b; b=c }
}
```
- Use case: Demo data generation.
- Why it matters: Infinite lazy data.

## 88. when expression exhaustive check
- Explanation: Enforce exhaustiveness.
- Code:
```kotlin
val result = when(state){ is UiState.Loading -> ...; is UiState.Success -> ...; is UiState.Error -> ... }.exhaustive
val <T> T.exhaustive: T get() = this
```
- Use case: Sealed UI rendering.
- Why it matters: Prevents missing branches.

## 89. Inline lambda label return
- Explanation: Use labels to exit correct scope.
- Code:
```kotlin
list.forEach label@{ if (it < 0) return@label }
```
- Use case: Early skip in iterations.
- Why it matters: Avoids accidental function return.

## 90. Sequence windowed
- Explanation: Sliding window utility.
- Code:
```kotlin
list.windowed(size = 3, step = 1)
```
- Use case: Trend calculation.
- Why it matters: Less manual indexing.

## 91. chunked processing
- Explanation: Process large lists in batches.
- Code:
```kotlin
items.chunked(50).forEach { batch -> send(batch) }
```
- Use case: API batch uploads.
- Why it matters: Avoids payload limits.

## 92. mapNotNullTo
- Explanation: Transform and collect non-null.
- Code:
```kotlin
val ids = list.mapNotNull { it.id }
```
- Use case: Filtering API responses.
- Why it matters: Fewer explicit null checks.

## 93. flattenMerge flows
- Explanation: Merge flow of flows.
- Code:
```kotlin
flowOf(flowA, flowB).flattenMerge().collect { }
```
- Use case: Combine multiple event sources.
- Why it matters: Concurrent emission handling.

## 94. flatMapLatest search
- Explanation: Cancel previous job on new input.
- Code:
```kotlin
queryFlow.flatMapLatest { query -> repo.search(query) }
```
- Use case: Search suggestions.
- Why it matters: Avoids outdated results.

## 95. Channel actor pattern
- Explanation: Serialize events through actor.
- Code:
```kotlin
val actor = scope.actor<Action> { for(action in channel) reduce(action) }
```
- Use case: MVI reducers.
- Why it matters: Thread-safe reducers without locks.

## 96. lateinit resettable
- Explanation: Clearable lateinit for tests.
- Code:
```kotlin
class Resettable<T> { private var v: T? = null; var value: T
    get() = v ?: error("Not set"); set(new){ v = new };
    fun reset(){ v=null }
}
```
- Use case: Test hooks.
- Why it matters: Control lifecycle manually.

## 97. KClass.safeObjectInstance
- Explanation: Get object singleton if exists.
- Code:
```kotlin
val instance = MySingleton::class.objectInstance
```
- Use case: Accessing objects reflectively.
- Why it matters: Avoids reflective creation.

## 98. property reference reuse
- Explanation: Use property refs for keys.
- Code:
```kotlin
val key = User::name.name
```
- Use case: Bundle keys aligned to properties.
- Why it matters: Prevents typos.

## 99. Inline expect/actual for multiplatform
- Explanation: Platform specific implementations.
- Code:
```kotlin
expect fun platformName(): String
```
- Use case: Shared KMP modules.
- Why it matters: Cleaner platform branching.

## 100. typealias for readability
- Explanation: Shorten complex types.
- Code:
```kotlin
typealias OnClick = (Long) -> Unit
```
- Use case: Adapter callbacks.
- Why it matters: Improves readability of signatures.



## 🚀 Connect & Support

If you found this guide helpful and want to master Android development, Kotlin, and AI integration, let's connect\!

  * **Subscribe for more Dev Content:** [YouTube @alwayslaxmikant](https://www.google.com/search?q=https://youtube.com/alwayslaxmikant) 🔔
  * **Get real-time updates:** [X (Twitter) @alwayslaxmikant](https://x.com/alwayslaxmikant) 🐦
  * **Follow on Instagram:** [Instagram @alwayslaxmikant](https://www.google.com/search?q=https://instagram.com/alwayslaxmikant) 📸