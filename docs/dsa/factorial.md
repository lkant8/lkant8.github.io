#  Factorial Using Recursion (C)

## 📌 Problem
Find the factorial of a number using recursion.

```
    if (n == 0 || n == 1)
        return 1;

    return n * factorial(n--);
}
```

## YouTube

<div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
  <iframe
    src="https://www.youtube.com/embed/cBCUzADVQTo"
    title="YouTube video"
    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"
    loading="lazy"
    allowfullscreen>
  </iframe>
</div>

---


Wrong Code:
```
return n * factorial(n--);
```
Right Code:
```
return n * factorial(n-1);
```
 Factorial of n:

n! = n × (n-1) × (n-2) × ... × 1

```
    if (n == 0 || n == 1)
        return 1;

    return n * factorial(n-1);
}
```
5! = 5 × 4 × 3 × 2 × 1 = 120

```c

#include <stdio.h>

int factorial(int n) {
    if (n == 0 || n == 1)
        return 1;
    return n * factorial(n-1);
}// post decrease // stack overflow 


int main() {
    int num = 5;
    printf("Factorial of %d is %d\n", num, factorial(num));
    return 0;
}

```

Let’s understand how recursion works for n = 5:

```
factorial(5)
= 5 * factorial(4)
= 5 * 4 * factorial(3)
= 5 * 4 * 3 * factorial(2)
= 5 * 4 * 3 * 2 * factorial(1)
= 5 * 4 * 3 * 2 * 1
= 120

```

Base Case 

```
if (n == 0 || n == 1)
    return 1;
```
stop recorsion

```
return n * factorial(n - 1);
```
Reduces the problem size
Calls function again with smaller value


## Social

- GitHub: [github.com/lkant8](https://github.com/lkant8)
- LinkedIn: [linkedin.com/in/lkant](https://www.linkedin.com/in/lkant/)
- YouTube: [youtube.com/@alwayslaxmikant](https://youtube.com/@alwayslaxmikant)
- X: [@alwayslaxmikant](https://x.com/alwayslaxmikant)