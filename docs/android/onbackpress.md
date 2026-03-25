
# How to handle onBackPressed()

As we know onBackPressed()  is deprecated from android, So What is the alternative of onBackpressed lets discover the topic-->


assume that, you have updated any of your application to API LEVEL 33, you have seen this error, onBackPressed is deprecated.

Maybe you have avare from Android 10 or any above API_LEVEL 29, Now days the system provides gesture navigation feature. As ( most of the mobiles which start from API level 29 )the system supports gestures like swiping left to right to navigate back ( just like in IOS devices ). But this has lewd to unexpected behavior when it has been combined with horizontal swipes in applications.

when you uses android studio 4+ above then it will show a warning -->


![mcode hashnode mobile on back pressed](https://cdn.hashnode.com/res/hashnode/image/upload/v1664812360510/zhSd6t8AN.png align="left")

The problem is that the(Android API confused about which kind of action is executed by the user) android system can not differentiate if the gesture is for system back or for application back navigation. Which led to problems like closing an entire application instead of navigating back to the required activity or fragment. In other words, It can't tell if the application handles the gesture. so that is why happen.

This is the problem 
**So what is the actual solution ** 
let's move on solution part-->


![mcode hashnode mobile on back pressed](https://cdn.hashnode.com/res/hashnode/image/upload/v1664812675692/BtTtPaaJE.png align="left")


So I have came across two solutions for achieving this problem, The first is to use the old function of onBackPressed, instead of calling the [super.onBackPressed](https://developer.android.com/guide/navigation/navigation-custom-back#implement) you can simply use isTaskRoot to check this activity is the last on stack or not, and the other solution is provided by the android itself the alternative way of handling backpressed events (which is introduce in API 29).



> Use the isTaskRoot() method in the onCreate method of the SplashActivity of the app (the root Activity of the app) to determine whether it is the root Activity in the task stack, such as If it is, it will not do any processing, if it is not, it will be finished directly


Solution, Alternative Way of Handling Backpressed

## **Step 1 :**

 Adding the required dependency in your project

`implementation ‘androidx.activity:activity-ktx:1.6+’ `

## **Step 2 :**

Add `android:enableOnBackInvokedCallBack="true” ` to your applications manifest `<application> `tag.


## **Step 3 :**

Handling backpressed in YourActivityClass

```
fun AppCompatActivity.onBackPressed(isEnabled: Boolean, callback: () -> Unit) {
    onBackPressedDispatcher.addCallback(this,
        object : OnBackPressedCallback(isEnabled) {
            override fun handleOnBackPressed() {
                callback()
            }
        })
}
// How To Use it : 
class ExampleActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_example)
        onBackPressed(true) {
            // do what do you want when get back 
        }
    }
}

```


Handling backpressed in your **Fragments**

Make sure you have at least
`“androidx.activity:activity:1.+.+” `
at your dependencies, while working with fragments

```
fun FragmentActivity.onBackPressed(callback: () -> Unit) {
    onBackPressedDispatcher.addCallback(this,
        object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                callback()
            }
        }
    )
}
// How To Use it :
class ExampleFragment : Fragment() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        requireActivity().onBackPressed {
            // do what do you want when get back 
        }
    }
}
``` 


## **Get involved**



-  [Check out the Video](https://www.youtube.com/channel/UCdbUomS4yFXN9D3_ZLtJJUg)

-  [Follow on Twitter](https://twitter.com/alwayslaxmikant)
