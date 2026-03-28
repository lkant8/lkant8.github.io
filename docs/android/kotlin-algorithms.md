# 50 Kotlin Algorithm Problems

Each problem has a statement, approach, complexity, optimized idea, Kotlin code, and edge cases. Difficulties: 15 Easy, 20 Medium, 15 Hard.

## Easy (15)

### 1. Two Sum (Hashing)
- Problem: Find indices of two numbers adding to target.
- Approach: One-pass hash map value -> index.
- Complexity: Time O(n), Space O(n).
- Optimized: Check complement before inserting.
- Kotlin:
```kotlin
fun twoSum(nums: IntArray, target: Int): IntArray {
    val map = hashMapOf<Int, Int>()
    nums.forEachIndexed { i, v ->
        map[target - v]?.let { return intArrayOf(it, i) }
        map[v] = i
    }
    return intArrayOf()
}
```
- Edge cases: Duplicates; no solution -> empty array.

### 2. Reverse String (Two Pointers)
- Problem: Reverse char array in place.
- Approach: Swap from both ends.
- Complexity: Time O(n), Space O(1).
- Optimized: Stop when l>=r.
- Kotlin:
```kotlin
fun reverse(s: CharArray) {
    var l = 0; var r = s.lastIndex
    while (l < r) { s[l] = s[r].also { s[r] = s[l] }; l++; r-- }
}
```
- Edge cases: Empty/one char.

### 3. Palindrome String
- Problem: Check if string reads same both ways.
- Approach: Two-pointer compare.
- Complexity: Time O(n), Space O(1).
- Optimized: Early exit on mismatch.
- Kotlin:
```kotlin
fun isPalindrome(s: String): Boolean {
    var i = 0; var j = s.lastIndex
    while (i < j) if (s[i++] != s[j--]) return false
    return true
}
```
- Edge cases: Mixed case -> normalize; empty true.

### 4. Merge Two Sorted Arrays
- Problem: Merge nums2 into nums1 sorted.
- Approach: Three pointers from end.
- Complexity: Time O(m+n), Space O(1).
- Optimized: Write largest to tail to avoid shifting.
- Kotlin:
```kotlin
fun merge(nums1: IntArray, m: Int, nums2: IntArray, n: Int) {
    var i=m-1; var j=n-1; var k=m+n-1
    while (j>=0) nums1[k--] = if (i>=0 && nums1[i] > nums2[j]) nums1[i--] else nums2[j--]
}
```
- Edge cases: nums2 empty; nums1 has padding zeros.

### 5. Move Zeroes
- Problem: Shift zeroes to end keeping order.
- Approach: Write pointer for non-zeros then fill zeros.
- Complexity: Time O(n), Space O(1).
- Optimized: Single pass.
- Kotlin:
```kotlin
fun moveZeroes(nums: IntArray) {
    var w = 0
    for (v in nums) if (v != 0) nums[w++] = v
    while (w < nums.size) nums[w++] = 0
}
```
- Edge cases: All zeros; no zeros.

### 6. Binary Search
- Problem: Find target in sorted array.
- Approach: Classic mid compare.
- Complexity: Time O(log n), Space O(1).
- Optimized: Use unsigned shift to avoid overflow.
- Kotlin:
```kotlin
fun binarySearch(nums: IntArray, target: Int): Int {
    var l=0; var r=nums.lastIndex
    while (l<=r) {
        val m = (l + r) ushr 1
        when {
            nums[m] == target -> return m
            nums[m] < target -> l = m+1
            else -> r = m-1
        }
    }
    return -1
}
```
- Edge cases: Duplicates; target absent.

### 7. Valid Anagram (Hashing)
- Problem: Check if two strings are anagrams.
- Approach: Count chars with fixed array.
- Complexity: Time O(n), Space O(1).
- Optimized: Early stop when count negative.
- Kotlin:
```kotlin
fun isAnagram(a: String, b: String): Boolean {
    if (a.length != b.length) return false
    val cnt = IntArray(26)
    for (c in a) cnt[c-'a']++
    for (c in b) if (--cnt[c-'a'] < 0) return false
    return true
}
```
- Edge cases: Unicode -> use map instead.

### 8. Majority Element (Boyer-Moore)
- Problem: Element appearing > n/2 times.
- Approach: Voting to find candidate.
- Complexity: Time O(n), Space O(1).
- Optimized: Single pass.
- Kotlin:
```kotlin
fun majority(nums: IntArray): Int {
    var cand = 0; var count = 0
    for (v in nums) {
        if (count == 0) cand = v
        count += if (v == cand) 1 else -1
    }
    return cand
}
```
- Edge cases: Assumes majority exists.

### 9. Best Time to Buy/Sell Stock I
- Problem: Max profit with one transaction.
- Approach: Track min price, max diff.
- Complexity: Time O(n), Space O(1).
- Optimized: Update min and profit in one pass.
- Kotlin:
```kotlin
fun maxProfit(prices: IntArray): Int {
    var minP = Int.MAX_VALUE; var best = 0
    for (p in prices) { minP = min(minP, p); best = max(best, p - minP) }
    return best
}
```
- Edge cases: Monotone decreasing -> 0.

### 10. Climbing Stairs (DP)
- Problem: Ways to climb n steps (1/2 steps).
- Approach: Fibonacci DP.
- Complexity: Time O(n), Space O(1).
- Optimized: Rolling two variables.
- Kotlin:
```kotlin
fun climb(n: Int): Int {
    var a=1; var b=1; repeat(n){ val c=a+b; a=b; b=c }
    return a
}
```
- Edge cases: n=0 or 1 -> 1.

### 11. Maximum Subarray (Kadane)
- Problem: Largest sum contiguous subarray.
- Approach: Kadane accumulate max ending here.
- Complexity: Time O(n), Space O(1).
- Optimized: Track best and current.
- Kotlin:
```kotlin
fun maxSubArray(nums: IntArray): Int {
    var best = nums[0]; var cur = nums[0]
    for (i in 1 until nums.size) {
        cur = max(nums[i], cur + nums[i]); best = max(best, cur)
    }
    return best
}
```
- Edge cases: All negatives -> pick largest element.

### 12. Remove Duplicates (Sorted Array)
- Problem: In-place unique compaction.
- Approach: Slow/fast pointers overwrite uniques.
- Complexity: Time O(n), Space O(1).
- Optimized: Only write when new value seen.
- Kotlin:
```kotlin
fun removeDup(nums: IntArray): Int {
    if (nums.isEmpty()) return 0
    var w = 1
    for (i in 1 until nums.size) if (nums[i] != nums[i-1]) nums[w++] = nums[i]
    return w
}
```
- Edge cases: All duplicates; single element.

### 13. Valid Parentheses (Stack)
- Problem: Check if brackets close correctly.
- Approach: Push opens; pop and match closes.
- Complexity: Time O(n), Space O(n).
- Optimized: Use array-as-stack for speed.
- Kotlin:
```kotlin
fun isValid(s: String): Boolean {
    val stack = CharArray(s.length); var top = -1
    fun match(open: Char, close: Char) = (open=='('&&close==')')||(open=='{'&&close=='}')||(open=='['&&close==']')
    for (c in s) when (c) {
        '(', '{', '[' -> stack[++top] = c
        ')','}',']' -> if (top<0 || !match(stack[top--], c)) return false
    }
    return top == -1
}
```
- Edge cases: Starts with closing; odd length.

### 14. Flood Fill (BFS)
- Problem: Recolor connected pixels from seed.
- Approach: BFS/DFS replace old color.
- Complexity: Time O(mn), Space O(mn).
- Optimized: Early exit if color unchanged.
- Kotlin:
```kotlin
fun floodFill(img: Array<IntArray>, sr: Int, sc: Int, new: Int): Array<IntArray> {
    val old = img[sr][sc]; if (old == new) return img
    val q: ArrayDeque<Pair<Int,Int>> = ArrayDeque(); q.add(sr to sc)
    val dirs = intArrayOf(1,0,-1,0,1)
    while(q.isNotEmpty()){
        val (r,c)=q.removeFirst(); img[r][c]=new
        for(i in 0 until 4){ val nr=r+dirs[i]; val nc=c+dirs[i+1];
            if(nr in img.indices && nc in img[0].indices && img[nr][nc]==old) q.add(nr to nc)
        }
    }
    return img
}
```
- Edge cases: 1x1 image; already desired color.

### 15. Binary Tree Level Order (Queue)
- Problem: Return values level by level.
- Approach: BFS with queue sized per level.
- Complexity: Time O(n), Space O(n).
- Optimized: Pre-size loop to avoid extra list.
- Kotlin:
```kotlin
data class TreeNode(var `val`: Int, var left: TreeNode?=null, var right: TreeNode?=null)
fun levelOrder(root: TreeNode?): List<List<Int>> {
    if (root==null) return emptyList()
    val res = mutableListOf<List<Int>>(); val q: ArrayDeque<TreeNode> = ArrayDeque(); q.add(root)
    while(q.isNotEmpty()){
        val level = MutableList(q.size){0}
        for(i in level.indices){
            val n=q.removeFirst(); level[i]=n.`val`
            n.left?.let(q::add); n.right?.let(q::add)
        }
        res += level
    }
    return res
}
```
- Edge cases: Empty tree.

## Medium (20)

### 16. Three Sum (Two Pointers)
- Problem: Unique triplets summing to zero.
- Approach: Sort; fix i; two-pointer search.
- Complexity: Time O(n^2), Space O(1) extra.
- Optimized: Skip duplicates while iterating.
- Kotlin:
```kotlin
fun threeSum(nums: IntArray): List<List<Int>> {
    nums.sort(); val res=mutableListOf<List<Int>>()
    for(i in 0 until nums.size-2){ if(i>0 && nums[i]==nums[i-1]) continue
        var l=i+1; var r=nums.lastIndex
        while(l<r){
            val sum=nums[i]+nums[l]+nums[r]
            when {
                sum==0 -> { res+=listOf(nums[i],nums[l],nums[r]); while(l<r && nums[l]==nums[l+1]) l++; while(l<r && nums[r]==nums[r-1]) r--; l++; r-- }
                sum<0 -> l++
                else -> r--
            }
        }
    }
    return res
}
```
- Edge cases: Many zeros; duplicates.

### 17. Longest Substring Without Repeat (Sliding Window)
- Problem: Max length substring with unique chars.
- Approach: Window with last seen index.
- Complexity: Time O(n), Space O(charset).
- Optimized: IntArray[128] faster for ASCII.
- Kotlin:
```kotlin
fun lengthOfLongestSubstring(s: String): Int {
    val last = IntArray(128) { -1 }
    var start = 0; var best = 0
    s.forEachIndexed { i, c ->
        start = max(start, last[c.code] + 1)
        best = max(best, i - start + 1)
        last[c.code] = i
    }
    return best
}
```
- Edge cases: Empty string; all same char.

### 18. Group Anagrams (Hashing)
- Problem: Group words sharing letters.
- Approach: Count signature as key.
- Complexity: Time O(n k), Space O(nk).
- Optimized: Avoid sorting by using 26 counts.
- Kotlin:
```kotlin
fun groupAnagrams(strs: Array<String>): List<List<String>> {
    val map = hashMapOf<String, MutableList<String>>()
    for (s in strs) {
        val count = IntArray(26)
        for (c in s) count[c-'a']++
        val key = count.joinToString(",")
        map.getOrPut(key){ mutableListOf() }.add(s)
    }
    return map.values.toList()
}
```
- Edge cases: Single-letter words; uppercase -> normalize.

### 19. Subarray Sum Equals K (Prefix Hash)
- Problem: Count subarrays summing to k.
- Approach: Prefix sum + hash of counts.
- Complexity: Time O(n), Space O(n).
- Optimized: Seed map with 0 -> 1.
- Kotlin:
```kotlin
fun subarraySum(nums: IntArray, k: Int): Int {
    val map = hashMapOf(0 to 1)
    var sum=0; var ans=0
    for (v in nums) {
        sum += v
        ans += map[sum - k] ?: 0
        map[sum] = (map[sum] ?: 0) + 1
    }
    return ans
}
```
- Edge cases: Negative numbers; k=0.

### 20. Number of Islands (DFS)
- Problem: Count connected land components.
- Approach: DFS marking visited by mutating grid.
- Complexity: Time O(mn), Space O(mn).
- Optimized: Reuse dirs array; in-place marking.
- Kotlin:
```kotlin
fun numIslands(g: Array<CharArray>): Int {
    val dirs=intArrayOf(1,0,-1,0,1)
    fun dfs(r:Int,c:Int){ if(r !in g.indices || c !in g[0].indices || g[r][c] != '1') return
        g[r][c]='0'; for(i in 0 until 4) dfs(r+dirs[i], c+dirs[i+1]) }
    var count=0
    for(r in g.indices) for(c in g[0].indices) if(g[r][c]=='1'){ count++; dfs(r,c) }
    return count
}
```
- Edge cases: All water; all land.

### 21. Course Schedule (Topological)
- Problem: Can all courses be finished given prerequisites.
- Approach: Kahn BFS with indegree.
- Complexity: Time O(V+E), Space O(V+E).
- Optimized: Use array for indegree.
- Kotlin:
```kotlin
fun canFinish(n: Int, prereq: Array<IntArray>): Boolean {
    val indeg = IntArray(n)
    val adj = Array(n){ mutableListOf<Int>() }
    for (p in prereq){ indeg[p[0]]++; adj[p[1]].add(p[0]) }
    val q: ArrayDeque<Int> = ArrayDeque((0 until n).filter { indeg[it]==0 })
    var done=0
    while(q.isNotEmpty()){
        val v=q.removeFirst(); done++
        for (nxt in adj[v]) if(--indeg[nxt]==0) q.add(nxt)
    }
    return done==n
}
```
- Edge cases: Self dependency; disconnected graph.

### 22. Merge Intervals (Greedy)
- Problem: Merge overlapping intervals.
- Approach: Sort by start; fold merging.
- Complexity: Time O(n log n), Space O(n).
- Optimized: Modify last interval in-place.
- Kotlin:
```kotlin
fun mergeIntervals(intervals: Array<IntArray>): Array<IntArray> {
    intervals.sortBy { it[0] }
    val res = mutableListOf<IntArray>()
    for (int in intervals) {
        if (res.isEmpty() || res.last()[1] < int[0]) res += int
        else res.last()[1] = max(res.last()[1], int[1])
    }
    return res.toTypedArray()
}
```
- Edge cases: Single interval; fully nested.

### 23. Word Break (DP)
- Problem: Can string be segmented using dictionary.
- Approach: dp[i] true if suffix ending at i forms word.
- Complexity: Time O(n^2), Space O(n).
- Optimized: Limit inner loop by max word length.
- Kotlin:
```kotlin
fun wordBreak(s: String, wordDict: Set<String>): Boolean {
    val n=s.length; val dp=BooleanArray(n+1); dp[0]=true
    val maxLen = wordDict.maxOfOrNull { it.length } ?: 0
    for(i in 1..n){
        for(l in 1..min(i, maxLen)) if(dp[i-l] && s.substring(i-l, i) in wordDict){ dp[i]=true; break }
    }
    return dp[n]
}
```
- Edge cases: Empty string; no words.

### 24. Coin Change (Min Coins)
- Problem: Minimum coins to make amount.
- Approach: Bottom-up DP over amount.
- Complexity: Time O(n*amount), Space O(amount).
- Optimized: Initialize dp with amount+1 sentinel.
- Kotlin:
```kotlin
fun coinChange(coins: IntArray, amount: Int): Int {
    val dp = IntArray(amount+1) { amount+1 }; dp[0]=0
    for(a in 1..amount) for(c in coins) if(c<=a) dp[a]=min(dp[a], 1+dp[a-c])
    return if (dp[amount] > amount) -1 else dp[amount]
}
```
- Edge cases: amount=0; impossible -> -1.

### 25. House Robber (DP)
- Problem: Max sum without robbing adjacent houses.
- Approach: Rolling two-state DP.
- Complexity: Time O(n), Space O(1).
- Optimized: Two vars prev1/prev2.
- Kotlin:
```kotlin
fun rob(nums: IntArray): Int {
    var prev2=0; var prev1=0
    for(v in nums){ val cur=max(prev1, prev2+v); prev2=prev1; prev1=cur }
    return prev1
}
```
- Edge cases: Single house.

### 26. Sliding Window Maximum (Deque)
- Problem: Max in each window of size k.
- Approach: Monotonic deque of indices.
- Complexity: Time O(n), Space O(k).
- Optimized: Drop smaller elements from back.
- Kotlin:
```kotlin
fun maxSlidingWindow(nums: IntArray, k: Int): IntArray {
    val dq = ArrayDeque<Int>()
    val res = IntArray(nums.size - k + 1); var idx=0
    nums.forEachIndexed { i, v ->
        while (dq.isNotEmpty() && dq.first() <= i - k) dq.removeFirst()
        while (dq.isNotEmpty() && nums[dq.last()] <= v) dq.removeLast()
        dq.addLast(i)
        if (i >= k-1) res[idx++] = nums[dq.first()]
    }
    return res
}
```
- Edge cases: k=1; k=len.

### 27. Min Stack
- Problem: Stack supporting getMin O(1).
- Approach: Stack of (value, currentMin).
- Complexity: Time O(1) ops, Space O(n).
- Optimized: Store min per node.
- Kotlin:
```kotlin
class MinStack {
    private val s = ArrayDeque<Pair<Int,Int>>()
    fun push(x:Int){ val minVal = if(s.isEmpty()) x else min(x, s.last().second); s.addLast(x to minVal) }
    fun pop(){ s.removeLast() }
    fun top() = s.last().first
    fun getMin() = s.last().second
}
```
- Edge cases: Pop on empty -> guard in prod.

### 28. LRU Cache
- Problem: O(1) get/put with eviction.
- Approach: LinkedHashMap in access-order.
- Complexity: Time O(1), Space O(capacity).
- Optimized: Override removeEldestEntry.
- Kotlin:
```kotlin
class LRUCache(private val cap: Int): LinkedHashMap<Int,Int>(cap, 0.75f, true) {
    fun getValue(key: Int) = super.getOrDefault(key, -1)
    fun putValue(key: Int, value: Int) { super.put(key, value) }
    override fun removeEldestEntry(eldest: MutableMap.MutableEntry<Int, Int>?) = size > cap
}
```
- Edge cases: cap=1; repeated puts.

### 29. Permutations (Backtracking)
- Problem: All permutations of array.
- Approach: Swap/backtrack.
- Complexity: Time O(n·n!), Space O(n).
- Optimized: In-place swapping.
- Kotlin:
```kotlin
fun permute(nums: IntArray): List<List<Int>> {
    val res=mutableListOf<List<Int>>()
    fun backtrack(start:Int){ if(start==nums.size){ res+=nums.toList(); return }
        for(i in start until nums.size){ nums[start]=nums[i].also{nums[i]=nums[start]}; backtrack(start+1); nums[start]=nums[i].also{nums[i]=nums[start]} }
    }
    backtrack(0); return res
}
```
- Edge cases: Duplicates -> add set to avoid repeats.

### 30. Word Ladder (BFS)
- Problem: Shortest transformation sequence length.
- Approach: BFS changing one char at a time.
- Complexity: Time O(N * L^2), Space O(N).
- Optimized: Remove words when visited.
- Kotlin:
```kotlin
fun ladderLength(begin: String, end: String, words: List<String>): Int {
    val dict = words.toMutableSet(); if(end !in dict) return 0
    val q: ArrayDeque<Pair<String,Int>> = ArrayDeque(); q += begin to 1
    while(q.isNotEmpty()){
        val (w, d)=q.removeFirst(); if(w==end) return d
        val chars=w.toCharArray()
        for(i in chars.indices){ val old=chars[i]
            for(c in 'a'..'z') if(c!=old){ chars[i]=c; val nw=String(chars); if(nw in dict){ dict.remove(nw); q+=nw to d+1 } }
            chars[i]=old
        }
    }
    return 0
}
```
- Edge cases: begin==end ->1; unreachable ->0.

### 31. Search Rotated Sorted Array
- Problem: Find target in rotated array.
- Approach: Modified binary search using sorted half.
- Complexity: Time O(log n), Space O(1).
- Optimized: Compare with mid to choose side.
- Kotlin:
```kotlin
fun searchRotated(nums: IntArray, target: Int): Int {
    var l=0; var r=nums.lastIndex
    while(l<=r){
        val m=(l+r) ushr 1
        if(nums[m]==target) return m
        if(nums[l] <= nums[m]){
            if(target in nums[l]..nums[m]) r=m-1 else l=m+1
        } else {
            if(target in nums[m]..nums[r]) l=m+1 else r=m-1
        }
    }
    return -1
}
```
- Edge cases: Duplicates -> adjust comparisons.

### 32. Longest Palindromic Substring (Expand Center)
- Problem: Longest palindromic substring.
- Approach: Expand around each center.
- Complexity: Time O(n^2), Space O(1).
- Optimized: Track best bounds only.
- Kotlin:
```kotlin
fun longestPal(s: String): String {
    var start=0; var end=0
    fun expand(l0:Int,r0:Int){ var l=l0; var r=r0
        while(l>=0 && r<s.length && s[l]==s[r]){ if(r-l > end-start){ start=l; end=r }; l--; r++ }
    }
    for(i in s.indices){ expand(i,i); expand(i,i+1) }
    return s.substring(start, end+1)
}
```
- Edge cases: Length 1; all same char.

### 33. K Closest Points (Heap)
- Problem: Find k points closest to origin.
- Approach: Max-heap of size k on squared distance.
- Complexity: Time O(n log k), Space O(k).
- Optimized: Pop when heap exceeds k.
- Kotlin:
```kotlin
fun kClosest(points: Array<IntArray>, k: Int): Array<IntArray> {
    val pq = PriorityQueue<IntArray>(compareByDescending { it[0]*it[0]+it[1]*it[1] })
    for(p in points){ pq.add(p); if(pq.size>k) pq.poll() }
    return pq.toTypedArray()
}
```
- Edge cases: k == points size.

### 34. Top K Frequent Elements (Heap/Bucket)
- Problem: Return k most frequent numbers.
- Approach: Frequency map + min-heap size k.
- Complexity: Time O(n log k), Space O(n).
- Optimized: Bucket sort for O(n) if needed.
- Kotlin:
```kotlin
fun topKFrequent(nums: IntArray, k: Int): IntArray {
    val freq = nums.groupingBy { it }.eachCount()
    val pq = PriorityQueue<Pair<Int,Int>>(compareBy { it.second })
    for((v,c) in freq){ pq += v to c; if(pq.size>k) pq.poll() }
    return pq.map { it.first }.toIntArray()
}
```
- Edge cases: k equals unique count.

### 35. Longest Increasing Subsequence (n log n)
- Problem: Length of LIS.
- Approach: Patience sorting tails array.
- Complexity: Time O(n log n), Space O(n).
- Optimized: Binary search to replace tail.
- Kotlin:
```kotlin
fun lengthOfLIS(nums: IntArray): Int {
    val tails = IntArray(nums.size); var size=0
    for(v in nums){ var l=0; var r=size
        while(l<r){ val m=(l+r) ushr 1; if(tails[m] < v) l=m+1 else r=m }
        tails[l]=v; if(l==size) size++
    }
    return size
}
```
- Edge cases: All equal ->1; strictly increasing.

## Hard (15)

### 36. Minimum Window Substring
- Problem: Smallest substring containing all chars of t.
- Approach: Sliding window with need/have counts.
- Complexity: Time O(n), Space O(1) for ASCII.
- Optimized: Track formed vs required to shrink early.
- Kotlin:
```kotlin
fun minWindow(s: String, t: String): String {
    if(t.isEmpty()) return ""
    val need = IntArray(128); var required=0
    for(c in t){ if(need[c.code]==0) required++; need[c.code]++ }
    val have = IntArray(128); var formed=0
    var l=0; var bestLen=Int.MAX_VALUE; var bestL=0
    for(r in s.indices){
        val rc=s[r].code; have[rc]++
        if(have[rc]==need[rc]) formed++
        while(l<=r && formed==required){
            if(r-l+1 < bestLen){ bestLen=r-l+1; bestL=l }
            val lc=s[l].code; have[lc]--; if(have[lc] < need[lc]) formed--; l++
        }
    }
    return if(bestLen==Int.MAX_VALUE) "" else s.substring(bestL, bestL+bestLen)
}
```
- Edge cases: t longer than s; repeated chars.

### 37. Median of Two Sorted Arrays
- Problem: Find median of two sorted arrays.
- Approach: Binary search partition on smaller array.
- Complexity: Time O(log(min(m,n))), Space O(1).
- Optimized: Ensure A is smaller to reduce search range.
- Kotlin:
```kotlin
fun findMedianSortedArrays(a: IntArray, b: IntArray): Double {
    var A=a; var B=b; if(A.size>B.size) { val tmp=A; A=B; B=tmp }
    var l=0; var r=A.size; val total=A.size+B.size; val half=total/2
    while(true){
        val i=(l+r) ushr 1; val j=half - i
        val aLeft= if(i==0) Int.MIN_VALUE else A[i-1]
        val aRight= if(i==A.size) Int.MAX_VALUE else A[i]
        val bLeft= if(j==0) Int.MIN_VALUE else B[j-1]
        val bRight= if(j==B.size) Int.MAX_VALUE else B[j]
        if(aLeft<=bRight && bLeft<=aRight){
            return if(total%2==0) (max(aLeft,bLeft)+min(aRight,bRight))/2.0 else min(aRight,bRight).toDouble()
        } else if(aLeft > bRight) r=i-1 else l=i+1
    }
}
```
- Edge cases: One array empty.

### 38. Trapping Rain Water
- Problem: Compute trapped water between bars.
- Approach: Two-pointer tracking max left/right.
- Complexity: Time O(n), Space O(1).
- Optimized: Move pointer with smaller boundary.
- Kotlin:
```kotlin
fun trap(height: IntArray): Int {
    var l=0; var r=height.lastIndex; var leftMax=0; var rightMax=0; var water=0
    while(l<r){
        if(height[l] < height[r]){
            if(height[l] >= leftMax) leftMax=height[l] else water += leftMax - height[l]
            l++
        } else {
            if(height[r] >= rightMax) rightMax=height[r] else water += rightMax - height[r]
            r--
        }
    }
    return water
}
```
- Edge cases: Monotone increasing/decreasing.

### 39. N-Queens (Backtracking)
- Problem: Place n queens without attacks.
- Approach: Backtrack rows with column/diag tracking.
- Complexity: Time O(n!), Space O(n).
- Optimized: Boolean arrays for cols/diags.
- Kotlin:
```kotlin
fun solveNQueens(n: Int): List<List<String>> {
    val cols=BooleanArray(n); val diag=BooleanArray(2*n); val anti=BooleanArray(2*n)
    val board=CharArray(n){'.'}; val cur=Array(n){ board.copyOf() }; val res=mutableListOf<List<String>>()
    fun dfs(r:Int){ if(r==n){ res+=cur.map { String(it) }; return }
        for(c in 0 until n){ if(cols[c]||diag[r+c]||anti[r-c+n]) continue
            cols[c]=diag[r+c]=anti[r-c+n]=true; cur[r][c]='Q'
            dfs(r+1)
            cols[c]=diag[r+c]=anti[r-c+n]=false; cur[r][c]='.'
        }
    }
    dfs(0); return res
}
```
- Edge cases: n=1 success; n=2/3 none.

### 40. Sudoku Solver (Backtracking)
- Problem: Solve 9x9 Sudoku.
- Approach: Backtracking with row/col/box constraints.
- Complexity: Exponential; Space O(1) board.
- Optimized: Precompute candidate sets.
- Kotlin:
```kotlin
fun solveSudoku(board: Array<CharArray>) {
    val rows=Array(9){BooleanArray(10)}; val cols=Array(9){BooleanArray(10)}; val box=Array(9){BooleanArray(10)}
    val blanks=mutableListOf<Pair<Int,Int>>()
    for(r in 0 until 9) for(c in 0 until 9){
        val ch=board[r][c]
        if(ch=='.') blanks+=r to c else {
            val d=ch-'0'; rows[r][d]=true; cols[c][d]=true; box[(r/3)*3 + c/3][d]=true
        }
    }
    fun dfs(idx:Int): Boolean {
        if(idx==blanks.size) return true
        val (r,c)=blanks[idx]; val b=(r/3)*3 + c/3
        for(d in 1..9) if(!rows[r][d] && !cols[c][d] && !box[b][d]){
            rows[r][d]=cols[c][d]=box[b][d]=true; board[r][c]=(d+'0'.code).toChar()
            if(dfs(idx+1)) return true
            rows[r][d]=cols[c][d]=box[b][d]=false; board[r][c]='.'
        }
        return false
    }
    dfs(0)
}
```
- Edge cases: Already solved; invalid puzzle.

### 41. Maximal Rectangle
- Problem: Largest rectangle of 1s in binary matrix.
- Approach: Treat each row as histogram + largest rectangle stack.
- Complexity: Time O(rows*cols), Space O(cols).
- Optimized: Add sentinel zero height.
- Kotlin:
```kotlin
fun maximalRectangle(matrix: Array<CharArray>): Int {
    if(matrix.isEmpty()) return 0
    val n=matrix[0].size; val heights=IntArray(n); var best=0
    fun maxHist(): Int {
        val stack=ArrayDeque<Int>(); var maxA=0
        for(i in 0..n){
            val h = if(i==n) 0 else heights[i]
            while(stack.isNotEmpty() && h < heights[stack.last()]){
                val height=heights[stack.removeLast()]
                val width = if(stack.isEmpty()) i else i-stack.last()-1
                maxA = max(maxA, height*width)
            }
            stack.addLast(i)
        }
        return maxA
    }
    for(row in matrix){
        for(c in 0 until n) heights[c] = if(row[c]=='1') heights[c]+1 else 0
        best = max(best, maxHist())
    }
    return best
}
```
- Edge cases: All zeros; single row.

### 42. Lowest Common Ancestor (Binary Tree)
- Problem: LCA of two nodes.
- Approach: Post-order recursion returning found nodes.
- Complexity: Time O(n), Space O(h).
- Optimized: Early return when node found.
- Kotlin:
```kotlin
fun lca(root: TreeNode?, p: TreeNode?, q: TreeNode?): TreeNode? {
    root ?: return null
    if(root==p || root==q) return root
    val left=lca(root.left,p,q); val right=lca(root.right,p,q)
    return when {
        left!=null && right!=null -> root
        left!=null -> left
        else -> right
    }
}
```
- Edge cases: One node equals root; nodes missing -> return null.

### 43. Dijkstra Shortest Path (Weighted)
- Problem: Shortest paths with non-negative weights.
- Approach: Min-heap relaxation.
- Complexity: Time O((V+E) log V), Space O(V).
- Optimized: Skip stale heap entries.
- Kotlin:
```kotlin
data class Edge(val to:Int,val w:Int)
fun dijkstra(n:Int, adj:Array<List<Edge>>, src:Int): IntArray {
    val dist=IntArray(n){Int.MAX_VALUE}; dist[src]=0
    val pq=PriorityQueue(compareBy<Pair<Int,Int>>{it.second}); pq += src to 0
    while(pq.isNotEmpty()){
        val (v,d)=pq.poll(); if(d!=dist[v]) continue
        for(e in adj[v]) if(d + e.w < dist[e.to]){ dist[e.to]=d+e.w; pq += e.to to dist[e.to] }
    }
    return dist
}
```
- Edge cases: Disconnected nodes -> INF.

### 44. Union-Find Provinces (DSU)
- Problem: Count connected components in adjacency matrix.
- Approach: Disjoint Set Union with path compression.
- Complexity: Time O(n^2 α(n)), Space O(n).
- Optimized: Union only upper triangle.
- Kotlin:
```kotlin
class DSU(n:Int){ private val p=IntArray(n){it}; private val r=IntArray(n)
    fun find(x:Int):Int{ if(p[x]!=x) p[x]=find(p[x]); return p[x] }
    fun union(a:Int,b:Int){ val pa=find(a); val pb=find(b); if(pa==pb) return; if(r[pa]<r[pb]) p[pa]=pb else if(r[pb]<r[pa]) p[pb]=pa else { p[pb]=pa; r[pa]++ } }
}
fun findCircleNum(isConnected: Array<IntArray>): Int {
    val n=isConnected.size; val dsu=DSU(n)
    for(i in 0 until n) for(j in i+1 until n) if(isConnected[i][j]==1) dsu.union(i,j)
    return (0 until n).map(dsu::find).toSet().size
}
```
- Edge cases: Fully connected; isolated nodes.

### 45. Segment Tree Range Sum with Updates
- Problem: Support point update and range sum queries.
- Approach: Iterative segment tree stored in array.
- Complexity: Time O(log n) per op, Space O(n).
- Optimized: Iterative avoids recursion overhead.
- Kotlin:
```kotlin
class NumArray(nums: IntArray) {
    private val n = nums.size
    private val tree = IntArray(2*n)
    init { for(i in 0 until n) tree[n+i]=nums[i]; for(i in n-1 downTo 1) tree[i]=tree[i shl 1]+tree[i shl 1 or 1] }
    fun update(i:Int, v:Int){ var idx=i+n; tree[idx]=v; var p=idx shr 1; while(p>0){ tree[p]=tree[p shl 1]+tree[p shl 1 or 1]; p= p shr 1 } }
    fun sumRange(l:Int, r:Int): Int {
        var L=l+n; var R=r+n; var sum=0
        while(L<=R){ if((L and 1)==1) sum+=tree[L++]; if((R and 1)==0) sum+=tree[R--]; L = L shr 1; R = R shr 1 }
        return sum
    }
}
```
- Edge cases: Empty array; single element.

### 46. Edit Distance (Levenshtein)
- Problem: Min edits to convert word1 to word2.
- Approach: DP on prefixes with insert/delete/replace.
- Complexity: Time O(mn), Space O(min(m,n)).
- Optimized: Use rolling array.
- Kotlin:
```kotlin
fun minDistance(a: String, b: String): Int {
    if(a.isEmpty()) return b.length; if(b.isEmpty()) return a.length
    val prev = IntArray(b.length+1) { it }
    val cur = IntArray(b.length+1)
    for(i in 1..a.length){
        cur[0]=i
        for(j in 1..b.length){
            cur[j] = if(a[i-1]==b[j-1]) prev[j-1] else 1 + min(prev[j-1], min(prev[j], cur[j-1]))
        }
        prev.indices.forEach { prev[it]=cur[it] }
    }
    return prev[b.length]
}
```
- Edge cases: Identical strings; one empty.

### 47. Kth Smallest in Sorted Matrix (Heap)
- Problem: Rows/cols sorted ascending; return kth smallest.
- Approach: Min-heap of first element of each row.
- Complexity: Time O(k log n), Space O(n).
- Optimized: Push next element in row after pop.
- Kotlin:
```kotlin
fun kthSmallest(matrix: Array<IntArray>, k: Int): Int {
    val pq = PriorityQueue(compareBy<Triple<Int,Int,Int>> { it.first })
    for(r in matrix.indices) pq += Triple(matrix[r][0], r, 0)
    repeat(k-1){
        val (_, r, c)=pq.poll()
        if(c+1 < matrix[0].size) pq += Triple(matrix[r][c+1], r, c+1)
    }
    return pq.peek().first
}
```
- Edge cases: k=1; k=n^2.

### 48. Max Path Sum (Binary Tree)
- Problem: Max sum of any path in binary tree.
- Approach: DFS returning max gain; track global best.
- Complexity: Time O(n), Space O(h).
- Optimized: Ignore negative gains with max(0,x).
- Kotlin:
```kotlin
fun maxPathSum(root: TreeNode?): Int {
    var best=Int.MIN_VALUE
    fun dfs(n: TreeNode?): Int {
        if(n==null) return 0
        val left=max(0, dfs(n.left)); val right=max(0, dfs(n.right))
        best = max(best, n.`val` + left + right)
        return n.`val` + max(left, right)
    }
    dfs(root); return best
}
```
- Edge cases: All negative values.

### 49. Traveling Salesman (Bitmask DP)
- Problem: Shortest tour visiting all nodes once.
- Approach: DP[mask][i] = best cost ending at i.
- Complexity: Time O(n^2 2^n), Space O(n 2^n).
- Optimized: Use INF/2 to prevent overflow.
- Kotlin:
```kotlin
fun tsp(dist: Array<IntArray>): Int {
    val n=dist.size; val full=1 shl n; val INF=1_000_000_000
    val dp = Array(full){ IntArray(n){INF} }; dp[1][0]=0
    for(mask in 1 until full) for(last in 0 until n) if(dp[mask][last] < INF){
        for(nxt in 0 until n) if(mask and (1 shl nxt)==0){
            val nm = mask or (1 shl nxt)
            dp[nm][nxt] = min(dp[nm][nxt], dp[mask][last] + dist[last][nxt])
        }
    }
    var best=INF
    for(i in 1 until n) best = min(best, dp[full-1][i] + dist[i][0])
    return best
}
```
- Edge cases: n<=1; unreachable edges -> large weight.

### 50. Max Flow (Edmonds-Karp)
- Problem: Compute max flow in directed graph.
- Approach: BFS augmenting paths on residual graph.
- Complexity: Time O(VE^2), Space O(V^2).
- Optimized: Store residual capacities in adjacency lists.
- Kotlin:
```kotlin
data class EdgeFlow(var to:Int, var cap:Int, var rev:Int)
class MaxFlow(private val n:Int){
    private val g = Array(n){ mutableListOf<EdgeFlow>() }
    fun addEdge(u:Int,v:Int,c:Int){ val a=EdgeFlow(v,c,g[v].size); val b=EdgeFlow(u,0,g[u].size); g[u].add(a); g[v].add(b) }
    fun maxFlow(s:Int,t:Int): Int {
        var flow=0
        while(true){
            val parent=IntArray(n){-1}; val edgeIdx=IntArray(n)
            val q:ArrayDeque<Int> = ArrayDeque(); q+=s; parent[s]=s
            while(q.isNotEmpty() && parent[t]==-1){
                val v=q.removeFirst()
                g[v].forEachIndexed { idx, e -> if(parent[e.to]==-1 && e.cap>0){ parent[e.to]=v; edgeIdx[e.to]=idx; q+=e.to } }
            }
            if(parent[t]==-1) break
            var add=Int.MAX_VALUE; var v=t
            while(v!=s){ val u=parent[v]; add=min(add, g[u][edgeIdx[v]].cap); v=u }
            v=t; while(v!=s){ val u=parent[v]; val e=g[u][edgeIdx[v]]; e.cap -= add; g[v][e.rev].cap += add; v=u }
            flow += add
        }
        return flow
    }
}
```
- Edge cases: No path -> flow 0; self loops ignored.
