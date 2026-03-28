# Kotlin Annotation Classes: The Ultimate Guide for Android Developers

This document provides a structured breakdown of **Annotation Classes** in Kotlin, specifically tailored for Android development. It covers everything from basic syntax to advanced Jetpack Compose use cases.

---

## YouTube

<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
  <iframe
    src="https://www.youtube.com/embed/2KxeH9H4fBo"
    title="YouTube video"
    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
    loading="lazy"
    allowfullscreen>
  </iframe>
</div>

---


##  1. What is an Annotation Class?

An **Annotation** is a form of metadata that provides data about a program but is not part of the program itself. They do not directly affect the operation of the code they annotate.

### Key Characteristics:
* **Metadata:** They provide extra information to the compiler or frameworks.
* **Non-Executable:** They don't change the execution logic of the code directly.
* **Consumers:** They are "read" by the compiler, Lint tools, or libraries (like Hilt, Room, or Retrofit).

### Common Android Examples:
* `@Composable`: Tells the compiler this function generates UI.
* `@Preview`: Tells Android Studio to render a composable in the design tab.
* `@Inject`: Tells Hilt to provide a dependency here.

---

##  2. Why Use Annotations?

| Benefit | Description | Example |
| :--- | :--- | :--- |
| **Code Generation** | Automatically creates boilerplate code. | Hilt/Dagger, Room |
| **Validation** | Ensures code is used correctly at compile-time. | `@NonNull`, `@Size` |
| **Configuration** | Replaces complex XML setup with simple tags. | Retrofit `@GET("users")` |
| **Tooling Support** | Enables IDE features like UI previews or Lint checks. | `@ColorInt`, `@Preview` |

> **The Amazon Analogy:** Think of annotations as shipping labels. A "Fragile" sticker doesn't change the item inside the box, but it changes how the delivery person (the compiler/framework) handles it.

---

##  3. Creating Annotation Classes

### Basic Syntax
In Kotlin, you define an annotation using the `annotation class` keyword.

```kotlin
annotation class MySimpleAnnotation
```

### Adding Parameters
Annotations can have constructors that take parameters. Only specific types are allowed: integers, strings, enums, other annotations, and arrays of these.

```kotlin
annotation class Author(val name: String, val year: Int = 2026)

// Usage
@Author(name = "Laxmi Kant")
class MyDatabaseHelper
```

---

##  4. Meta-Annotations (Advanced Concepts)

To control how your annotation behaves, you use **Meta-Annotations** (annotations on annotations).

### 1. `@Target`
Defines **where** the annotation can be used (classes, functions, properties, etc.).
```kotlin
@Target(AnnotationTarget.CLASS, AnnotationTarget.FUNCTION)
annotation class OnlyForClassesAndFunctions
```

### 2. `@Retention`
Defines **how long** the annotation lives.
* `SOURCE`: Dropped after compilation (used by Lint).
* `BINARY`: Stored in `.class` files but not visible via reflection.
* `RUNTIME`: Available at runtime via reflection (used by frameworks like Retrofit).

```kotlin
@Retention(AnnotationRetention.RUNTIME)
annotation class RuntimeAnnotation
```

### 3. `@Repeatable`
Allows the same annotation to be applied multiple times to a single element.

---

##  5. Pro Tip: Custom Multi-Preview Annotations

In Jetpack Compose, instead of writing five `@Preview` tags for every screen (Dark Mode, Light Mode, Font scaling), you can create a **Multipreview Annotation**.

### Step 1: Create the Custom Annotation
```kotlin
@Preview(
    name = "Light Mode",
    group = "Themes",
    showBackground = true
)
@Preview(
    name = "Dark Mode",
    group = "Themes",
    uiMode = android.content.res.Configuration.UI_MODE_NIGHT_YES,
    showBackground = true
)
@Preview(
    name = "Large Font",
    fontScale = 1.5f,
    showBackground = true
)
annotation class DevicePreviews
```

### Step 2: Use It Anywhere
```kotlin
@DevicePreviews
@Composable
fun LoginScreenPreview() {
    LoginScreen()
}
```
*Now, one annotation generates three different previews instantly!*

---


[View On Github](https://github.com/lkant8/compose-mvvm/blob/706800a8e03ef90c0dd47a88852f5c1275bbd429/app/src/main/java/com/example/mycomposable/utils/UiRenderView.kt#L8-L104)

```kotlin
@Preview(
    name = "Pixel 4 - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 0.75f,
    device = "id:pixel_4"
)
@Preview(
    name = "Pixel 4 - Dark Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_YES,
    fontScale = 0.75f,
    device = "id:pixel_4"
)
@Preview(
    name = "Pixel 5 - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 0.75f,
    device = "id:pixel_5"
)
@Preview(
    name = "Pixel 5 - Dark Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_YES,
    fontScale = 0.75f,
    device = "id:pixel_5"
)
@Preview(
    name = "Foldable - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 1.5f,
    device = "id:pixel_fold"
)
@Preview(
    name = "Foldable - Dark Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_YES,
    fontScale = 1.5f,
    device = "id:pixel_fold"
)
@Preview(
    name = "7in Foldable - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 1.5f,
    device = "id:6.7in Foldable"
)
@Preview(
    name = "7in Foldable - Dark Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_YES,
    fontScale = 1.5f,
    device = "id:6.7in Foldable"
)
@Preview(
    name = "Tablet - Portrait - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 1.0f,
    device = "id:10.1in WXGA (Tablet)"
)
@Preview(
    name = "Tablet - Portrait - Dark Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_YES,
    fontScale = 1.0f,
    device = "id:10.1in WXGA (Tablet)"
)
@Preview(
    name = "Tablet - Landscape - Light Mode",
    showSystemUi = true,
    showBackground = true,
    uiMode = UI_MODE_NIGHT_NO,
    fontScale = 1.0f,
    device= "id:7in WSVGA (Tablet)"
)
@Preview(
    name= "Tablet - Landscape - Dark Mode",
    showSystemUi= true,
    showBackground= true,
    uiMode= UI_MODE_NIGHT_YES,
    fontScale= 1.0f,
    device= "id:7in WSVGA (Tablet)"
)
annotation class AppPreviewsMultiDevice

// Basic preview annotation for quick access to various devices
@Preview(uiMode= UI_MODE_NIGHT_NO, showSystemUi= true, name= "Pixel 4", device= "id:pixel_4")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "Pixel 4 - Dark", device= "id:pixel_4")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "Pixel 5", device= "id:pixel_5")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "Foldable", device= "id:pixel_fold")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "7in Foldable", device= "id:6.7in Foldable")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "Tablet - Portrait", device= "id:medium_tablet")
@Preview(uiMode= UI_MODE_NIGHT_YES, showSystemUi= true, name= "Tablet - Landscape", device= "id:10.1in WXGA (Tablet)")
annotation class AppPreviews

```




## Summary Checklist

* [ ] Use **Annotation Classes** for metadata, not logic.
* [ ] Use `@Target` to restrict where your annotation is placed.
* [ ] Use `@Retention(AnnotationRetention.RUNTIME)` if you need to check the annotation while the app is running.
* [ ] Leverage **Multipreview** to speed up UI development in Compose.

---

## 🚀 Connect & Support

If you found this guide helpful and want to master Android development, Kotlin, and AI integration, let's connect\!

  * **Subscribe for more Dev Content:** [YouTube @alwayslaxmikant](https://www.google.com/search?q=https://youtube.com/alwayslaxmikant) 🔔
  * **Get real-time updates:** [X (Twitter) @alwayslaxmikant](https://x.com/alwayslaxmikant) 🐦
  * **Follow on Instagram:** [Instagram @alwayslaxmikant](https://www.google.com/search?q=https://instagram.com/alwayslaxmikant) 📸