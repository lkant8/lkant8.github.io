# 50 Core Data Structures & Algorithms in Kotlin

Each item lists definition, intuition, steps, complexity, Kotlin snippet, when to use, and common mistakes.

### 1. Quick Sort
- Definition: Divide-and-conquer sort partitioning around a pivot.
- Intuition: Place pivot in correct spot; recursively sort sides.
- Steps: pick pivot → partition smaller/larger → recurse.
- Complexity: Avg O(n log n), worst O(n^2); Space O(log n) recursion.
- Kotlin:
```kotlin
fun quickSort(a: IntArray, l: Int = 0, r: Int = a.lastIndex) {
    if (l >= r) return
    var i=l; var j=r; val p=a[(l+r) ushr 1]
    while(i<=j){ while(a[i] < p) i++; while(a[j] > p) j--; if(i<=j){ a[i]=a[j].also{a[j]=a[i]}; i++; j-- } }
    quickSort(a, l, j); quickSort(a, i, r)
}
```
- When to use: In-memory sort with good cache behavior.
- Common mistakes: Not handling equal elements → infinite recursion.

### 2. Merge Sort
- Definition: Stable divide-and-conquer sort merging two halves.
- Intuition: Sort halves then merge sorted streams.
- Steps: split → sort left/right → merge with temp buffer.
- Complexity: Time O(n log n); Space O(n).
- Kotlin:
```kotlin
fun mergeSort(a: IntArray, tmp: IntArray = IntArray(a.size), l:Int=0, r:Int=a.lastIndex){
    if(l>=r) return; val m=(l+r) ushr 1
    mergeSort(a,tmp,l,m); mergeSort(a,tmp,m+1,r)
    var i=l; var j=m+1; var k=l
    while(i<=m || j<=r){
        tmp[k++] = when {
            j>r -> a[i++]
            i>m -> a[j++]
            a[i] <= a[j] -> a[i++]
            else -> a[j++]
        }
    }
    for(p in l..r) a[p]=tmp[p]
}
```
- When to use: Need stability or guaranteed O(n log n).
- Common mistakes: Forgetting to copy back merged array.

### 3. Heap Sort
- Definition: In-place sort using max-heap.
- Intuition: Build heap then repeatedly extract max to end.
- Steps: heapify → swap root with end → sift-down.
- Complexity: Time O(n log n); Space O(1).
- Kotlin:
```kotlin
fun heapSort(a:IntArray){
    fun sift(n:Int, i:Int){ var largest=i; val l=2*i+1; val r=2*i+2
        if(l<n && a[l]>a[largest]) largest=l
        if(r<n && a[r]>a[largest]) largest=r
        if(largest!=i){ a[i]=a[largest].also{a[largest]=a[i]}; sift(n,largest)}
    }
    for(i in a.size/2-1 downTo 0) sift(a.size,i)
    for(end in a.lastIndex downTo 1){ a[0]=a[end].also{a[end]=a[0]}; sift(end,0) }
}
```
- When to use: Predictable O(1) memory, no recursion allowed.
- Common mistakes: Off-by-one in heap indices.

### 4. Counting Sort
- Definition: Frequency-based stable sort for small integer range.
- Intuition: Count occurrences then prefix-sum to positions.
- Steps: count -> prefix -> place into output.
- Complexity: Time O(n + k), Space O(n + k) where k=range.
- Kotlin:
```kotlin
fun countingSort(a:IntArray, maxVal:Int): IntArray {
    val cnt=IntArray(maxVal+1)
    for(v in a) cnt[v]++
    for(i in 1..maxVal) cnt[i]+=cnt[i-1]
    val out=IntArray(a.size)
    for(i in a.lastIndex downTo 0){ val v=a[i]; out[--cnt[v]]=v }
    return out
}
```
- When to use: Large n, small value range (e.g., grades, ages).
- Common mistakes: Forget stability by iterating from end.

### 5. Radix Sort (LSD)
- Definition: Sort integers digit by digit using counting sort per digit.
- Intuition: Stable sort on least significant digit upwards.
- Steps: for each digit place (1,10,100...) apply counting sort base 10.
- Complexity: Time O(d*(n+k)); Space O(n+k).
- Kotlin:
```kotlin
fun radixSort(nums:IntArray){
    var exp=1; val out=IntArray(nums.size); val base=10
    val maxVal=nums.maxOrNull() ?: 0
    while(maxVal/exp>0){
        val cnt=IntArray(base)
        for(v in nums) cnt[(v/exp)%base]++
        for(i in 1 until base) cnt[i]+=cnt[i-1]
        for(i in nums.lastIndex downTo 0){ val v=nums[i]; val d=(v/exp)%base; out[--cnt[d]]=v }
        System.arraycopy(out,0,nums,0,nums.size); exp*=base
    }
}
```
- When to use: Large lists of non-negative ints with bounded digits.
- Common mistakes: Not using stable counting sort per digit.

### 6. Bucket Sort
- Definition: Distribute elements into buckets then sort each bucket.
- Intuition: Good when data uniformly distributed over range.
- Steps: choose bucket size → scatter → sort buckets (e.g., insertion) → concat.
- Complexity: Avg O(n+k), worst O(n^2); Space O(n+k).
- Kotlin:
```kotlin
fun bucketSort(arr: DoubleArray, bucketSize:Int = 5): DoubleArray {
    if(arr.isEmpty()) return arr
    val min=arr.minOrNull()!!; val max=arr.maxOrNull()!!
    val bucketCount=((max-min)/bucketSize+1).toInt()
    val buckets = List(bucketCount){ mutableListOf<Double>() }
    for(v in arr){ val idx=((v-min)/bucketSize).toInt(); buckets[idx].add(v) }
    val result=mutableListOf<Double>()
    for(b in buckets){ b.sort(); result.addAll(b) }
    return result.toDoubleArray()
}
```
- When to use: Floating-point values in known small range.
- Common mistakes: Poor bucket size causing imbalance.

### 7. Binary Search (Exact)
- Definition: Search sorted array by halving.
- Intuition: Keep invariant [l,r] contains target.
- Steps: compute mid → compare → shrink range.
- Complexity: Time O(log n), Space O(1).
- Kotlin:
```kotlin
fun binSearch(a:IntArray, target:Int): Int {
    var l=0; var r=a.lastIndex
    while(l<=r){ val m=(l+r) ushr 1; when {
        a[m]==target -> return m
        a[m]<target -> l=m+1
        else -> r=m-1
    }}
    return -1
}
```
- When to use: Sorted data lookups.
- Common mistakes: Infinite loop from mid calc/updates.

### 8. Binary Search (Lower/Upper Bound)
- Definition: Find first >= x or first > x position.
- Intuition: Bias mid and store answer as you go.
- Steps: while l<r with mid=(l+r)/2, move left/right based on condition.
- Complexity: Time O(log n); Space O(1).
- Kotlin:
```kotlin
fun lowerBound(a:IntArray, x:Int): Int {
    var l=0; var r=a.size
    while(l<r){ val m=(l+r) ushr 1; if(a[m] < x) l=m+1 else r=m }
    return l
}
```
- When to use: Insert position, range counts.
- Common mistakes: Using <= instead of < for lower bound.

### 9. Binary Search on Answer
- Definition: Search minimal feasible value over monotonic predicate.
- Intuition: Treat answer space as sorted by feasibility.
- Steps: set low/high → mid → check predicate → move bound.
- Complexity: Time O(log R * check), Space O(1).
- Kotlin:
```kotlin
fun minCapacity(weights:IntArray, days:Int): Int {
    var l=weights.maxOrNull()!!; var r=weights.sum()
    fun can(cap:Int): Boolean { var need=1; var cur=0; for(w in weights){ if(cur + w > cap){ need++; cur=0 }; cur+=w }; return need<=days }
    while(l<r){ val m=(l+r) ushr 1; if(can(m)) r=m else l=m+1 }
    return l
}
```
- When to use: Minimize max load, speed, capacity problems.
- Common mistakes: Predicate not monotonic.

### 10. Two-Pointer Two-Sum (Sorted)
- Definition: Find pair summing to target in sorted array.
- Intuition: Move inward based on sum comparison.
- Steps: l=0, r=n-1; if sum>target move r-- else l++.
- Complexity: Time O(n), Space O(1).
- Kotlin:
```kotlin
fun twoSumSorted(a:IntArray, target:Int): Pair<Int,Int>? {
    var l=0; var r=a.lastIndex
    while(l<r){ val s=a[l]+a[r]; when {
        s==target -> return l to r
        s>target -> r--
        else -> l++
    }}
    return null
}
```
- When to use: Sorted arrays or after sort.
- Common mistakes: Forget duplicates if unique indices needed.

### 11. Dutch National Flag
- Definition: Partition array into three regions (<,=,> pivot).
- Intuition: Single pass with three pointers (low, mid, high).
- Steps: mid scans; swap with low/high accordingly.
- Complexity: Time O(n); Space O(1).
- Kotlin:
```kotlin
fun dutchFlag(a:IntArray, pivot:Int){
    var low=0; var mid=0; var high=a.lastIndex
    while(mid<=high){
        when {
            a[mid] < pivot -> { a[low]=a[mid].also{a[mid]=a[low]}; low++; mid++ }
            a[mid] > pivot -> { a[mid]=a[high].also{a[high]=a[mid]}; high-- }
            else -> mid++
        }
    }
}
```
- When to use: 0/1/2 sort, color grouping.
- Common mistakes: Not moving mid when swapping with high.

### 12. Breadth-First Search
- Definition: Layered traversal of graph/ tree using queue.
- Intuition: Explore neighbors before next depth.
- Steps: enqueue source → pop → enqueue unvisited neighbors.
- Complexity: Time O(V+E); Space O(V).
- Kotlin:
```kotlin
fun bfs(adj: List<List<Int>>, start:Int): IntArray {
    val dist=IntArray(adj.size){-1}; val q:ArrayDeque<Int> = ArrayDeque()
    dist[start]=0; q+=start
    while(q.isNotEmpty()){
        val v=q.removeFirst()
        for(n in adj[v]) if(dist[n]==-1){ dist[n]=dist[v]+1; q+=n }
    }
    return dist
}
```
- When to use: Shortest path on unweighted graphs.
- Common mistakes: Forget to mark visited when enqueuing.

### 13. Depth-First Search
- Definition: Traverse as deep as possible before backtracking.
- Intuition: Recursively walk neighbors.
- Steps: visit → mark → recurse neighbors.
- Complexity: Time O(V+E); Space O(V) recursion.
- Kotlin:
```kotlin
fun dfs(adj: List<List<Int>>, start:Int, seen:BooleanArray = BooleanArray(adj.size)){
    if(seen[start]) return; seen[start]=true
    for(n in adj[start]) dfs(adj, n, seen)
}
```
- When to use: Connectivity, topological ordering, component count.
- Common mistakes: Stack overflow on very deep graphs → use iterative.

### 14. Topological Sort (Kahn)
- Definition: Linear order of DAG respecting edges.
- Intuition: Remove nodes with indegree 0 iteratively.
- Steps: compute indegree → queue zeros → pop/update neighbors.
- Complexity: Time O(V+E); Space O(V).
- Kotlin:
```kotlin
fun topoSort(adj: List<List<Int>>): List<Int> {
    val indeg=IntArray(adj.size); for(v in adj.indices) for(n in adj[v]) indeg[n]++
    val q:ArrayDeque<Int> = ArrayDeque((adj.indices).filter{ indeg[it]==0 })
    val order=mutableListOf<Int>()
    while(q.isNotEmpty()){
        val v=q.removeFirst(); order+=v
        for(n in adj[v]) if(--indeg[n]==0) q+=n
    }
    return if(order.size==adj.size) order else emptyList() // empty indicates cycle
}
```
- When to use: Task scheduling, dependency resolution.
- Common mistakes: Not checking for cycles (order size < V).

### 15. Strongly Connected Components (Tarjan)
- Definition: Maximal sets where every node reachable from every other.
- Intuition: DFS timestamps with low-link to find roots.
- Steps: DFS push stack → update low-link → when node is root, pop stack to form SCC.
- Complexity: Time O(V+E); Space O(V).
- Kotlin:
```kotlin
fun tarjan(adj: List<List<Int>>): List<List<Int>> {
    val n=adj.size; val disc=IntArray(n){-1}; val low=IntArray(n); val stack=ArrayDeque<Int>(); val inStack=BooleanArray(n)
    var time=0; val res=mutableListOf<List<Int>>()
    fun dfs(v:Int){ disc[v]=time; low[v]=time; time++; stack.addLast(v); inStack[v]=true
        for(nxt in adj[v]){
            if(disc[nxt]==-1){ dfs(nxt); low[v]=min(low[v], low[nxt]) }
            else if(inStack[nxt]) low[v]=min(low[v], disc[nxt])
        }
        if(low[v]==disc[v]){
            val comp=mutableListOf<Int>()
            while(true){ val w=stack.removeLast(); inStack[w]=false; comp+=w; if(w==v) break }
            res+=comp
        }
    }
    for(v in 0 until n) if(disc[v]==-1) dfs(v)
    return res
}
```
- When to use: Condensation of directed graphs, cycle detection.
- Common mistakes: Forget to mark `inStack` false on pop.

### 16. Dijkstra
- Definition: Shortest path from source with non-negative weights.
- Intuition: Greedily expand node with smallest tentative distance.
- Steps: dist init INF; push source; pop min; relax edges; skip stale entries.
- Complexity: Time O((V+E) log V); Space O(V).
- Kotlin:
```kotlin
data class Edge(val to:Int,val w:Int)
fun dijkstra(n:Int, adj: Array<List<Edge>>, src:Int): IntArray {
    val dist=IntArray(n){Int.MAX_VALUE}; dist[src]=0
    val pq=PriorityQueue(compareBy<Pair<Int,Int>>{it.second}); pq += src to 0
    while(pq.isNotEmpty()){
        val (v,d)=pq.poll(); if(d!=dist[v]) continue
        for(e in adj[v]) if(d + e.w < dist[e.to]){ dist[e.to]=d+e.w; pq += e.to to dist[e.to] }
    }
    return dist
}
```
- When to use: Routes, maps, weighted graphs without negatives.
- Common mistakes: Not skipping stale heap entries.

### 17. Bellman-Ford
- Definition: Shortest paths with possible negative edges (no negative cycles).
- Intuition: Relax all edges V-1 times; one more pass detects cycles.
- Steps: dist init INF; repeat V-1 relax; check for improvement.
- Complexity: Time O(VE); Space O(V).
- Kotlin:
```kotlin
fun bellmanFord(n:Int, edges: List<Triple<Int,Int,Int>>, src:Int): Pair<IntArray, Boolean> {
    val dist=IntArray(n){Int.MAX_VALUE}; dist[src]=0
    repeat(n-1){
        for((u,v,w) in edges) if(dist[u]!=Int.MAX_VALUE && dist[u]+w < dist[v]) dist[v]=dist[u]+w
    }
    var negCycle=false
    for((u,v,w) in edges) if(dist[u]!=Int.MAX_VALUE && dist[u]+w < dist[v]) negCycle=true
    return dist to negCycle
}
```
- When to use: Graphs with negative weights, detecting cycles.
- Common mistakes: Integer overflow when dist is INF.

### 18. Floyd-Warshall
- Definition: All-pairs shortest paths dynamic programming.
- Intuition: Consider intermediate vertices gradually.
- Steps: dist[i][j]=min(dist[i][j], dist[i][k]+dist[k][j]) for all k.
- Complexity: Time O(n^3); Space O(n^2).
- Kotlin:
```kotlin
fun floyd(dist: Array<IntArray>) {
    val n=dist.size; val INF=1_000_000_000
    for(k in 0 until n) for(i in 0 until n) if(dist[i][k]<INF)
        for(j in 0 until n) if(dist[k][j]<INF && dist[i][j] > dist[i][k]+dist[k][j]) dist[i][j]=dist[i][k]+dist[k][j]
}
```
- When to use: Dense graphs, multiple queries.
- Common mistakes: Forget to initialize INF for absent edges.

### 19. Kruskal Minimum Spanning Tree
- Definition: Build MST by adding lowest weight edges without cycles.
- Intuition: Greedy with Union-Find to detect cycles.
- Steps: sort edges → add if endpoints are in different sets.
- Complexity: Time O(E log E); Space O(V).
- Kotlin:
```kotlin
data class E(val u:Int,val v:Int,val w:Int)
class DSU(n:Int){ private val p=IntArray(n){it}; private val r=IntArray(n)
    fun find(x:Int):Int{ if(p[x]!=x) p[x]=find(p[x]); return p[x] }
    fun union(a:Int,b:Int): Boolean { val pa=find(a); val pb=find(b); if(pa==pb) return false; if(r[pa]<r[pb]) p[pa]=pb else if(r[pb]<r[pa]) p[pb]=pa else { p[pb]=pa; r[pa]++ }; return true }
}
fun kruskal(n:Int, edges: List<E>): Int {
    val dsu=DSU(n); val sorted=edges.sortedBy { it.w }; var cost=0
    for(e in sorted) if(dsu.union(e.u,e.v)) cost+=e.w
    return cost
}
```
- When to use: Sparse graphs MST.
- Common mistakes: Not handling disconnected graph (forest).

### 20. Prim Minimum Spanning Tree
- Definition: Grow MST from a start vertex using priority queue.
- Intuition: Always pick lightest edge to unvisited node.
- Steps: push (0,start); pop min; add edges of new node.
- Complexity: Time O(E log V); Space O(V).
- Kotlin:
```kotlin
fun prim(n:Int, adj: Array<List<Edge>>, start:Int=0): Int {
    val seen=BooleanArray(n); val pq=PriorityQueue(compareBy<Pair<Int,Int>>{it.second})
    pq += start to 0; var cost=0
    while(pq.isNotEmpty()){
        val (v,w)=pq.poll(); if(seen[v]) continue
        seen[v]=true; cost+=w
        for(e in adj[v]) if(!seen[e.to]) pq += e.to to e.w
    }
    return cost
}
```
- When to use: Dense graphs, need MST weight quickly.
- Common mistakes: Forget to skip visited nodes -> duplicates counted.

### 21. Union-Find (Disjoint Set)
- Definition: Data structure to track disjoint sets with union & find.
- Intuition: Parent pointers with path compression and rank.
- Steps: find with compression → union by rank/size.
- Complexity: Amortized O(alpha(n)); Space O(n).
- Kotlin:
```kotlin
class UF(n:Int){ private val p=IntArray(n){it}; private val sz=IntArray(n){1}
    fun find(x:Int):Int{ if(p[x]!=x) p[x]=find(p[x]); return p[x] }
    fun union(a:Int,b:Int): Boolean { var pa=find(a); var pb=find(b); if(pa==pb) return false; if(sz[pa]<sz[pb]) pa=pb.also{pb=pa}; p[pb]=pa; sz[pa]+=sz[pb]; return true }
}
```
- When to use: Connectivity queries, Kruskal, dynamic components.
- Common mistakes: Not compressing path -> slow.

### 22. Prefix Sum / Difference Array
- Definition: Precompute cumulative sums to answer range queries fast.
- Intuition: Sum[l..r] = pref[r]-pref[l-1].
- Steps: build pref; answer queries; for updates use difference array.
- Complexity: Build O(n); query O(1); Space O(n).
- Kotlin:
```kotlin
fun prefixSum(a:IntArray): IntArray { val pref=IntArray(a.size+1); for(i in a.indices) pref[i+1]=pref[i]+a[i]; return pref }
fun range(pref:IntArray,l:Int,r:Int) = pref[r+1]-pref[l]
```
- When to use: Many range sum queries, image integral.
- Common mistakes: Off-by-one indexing.

### 23. Sliding Window (Fixed Size)
- Definition: Maintain aggregate over window of length k.
- Intuition: Add new element, remove outgoing.
- Steps: initialize first window → slide updating sum/state.
- Complexity: Time O(n); Space O(1).
- Kotlin:
```kotlin
fun maxAvg(a:IntArray, k:Int): Double {
    var sum=a.take(k).sum(); var best=sum
    for(i in k until a.size){ sum += a[i]-a[i-k]; best=max(best, sum) }
    return best.toDouble()/k
}
```
- When to use: Streaming metrics, averages.
- Common mistakes: Not subtracting outgoing element.

### 24. Sliding Window (Variable Size)
- Definition: Expand/contract window to satisfy condition.
- Intuition: Grow until valid, then shrink to minimal.
- Steps: while expand pointer; while valid shrink left.
- Complexity: Time O(n) for many problems; Space O(k).
- Kotlin:
```kotlin
fun minSubArrayLen(target:Int, nums:IntArray): Int {
    var l=0; var sum=0; var best=Int.MAX_VALUE
    for(r in nums.indices){ sum+=nums[r]; while(sum>=target){ best=min(best, r-l+1); sum-=nums[l++] } }
    return if(best==Int.MAX_VALUE) 0 else best
}
```
- When to use: Smallest window satisfying sum/count constraint.
- Common mistakes: Forget to update best after shrinking.

### 25. Monotonic Queue (Sliding Max)
- Definition: Queue maintaining decreasing order to query max in window.
- Intuition: Drop smaller elements from back.
- Steps: push with popping back; remove front when out of window.
- Complexity: Time O(n); Space O(k).
- Kotlin:
```kotlin
fun slidingMax(a:IntArray, k:Int): IntArray {
    val dq=ArrayDeque<Int>(); val res=IntArray(a.size-k+1); var idx=0
    for(i in a.indices){
        while(dq.isNotEmpty() && dq.first() <= i-k) dq.removeFirst()
        while(dq.isNotEmpty() && a[dq.last()] <= a[i]) dq.removeLast()
        dq.addLast(i); if(i>=k-1) res[idx++]=a[dq.first()]
    }
    return res
}
```
- When to use: Window max/min, queue-based DP.
- Common mistakes: Using values instead of indices → cannot drop expired.

### 26. Kadane Maximum Subarray
- Definition: Max sum contiguous subarray.
- Intuition: Either extend previous sum or start new.
- Steps: cur=max(a[i], cur+a[i]); best=max(best,cur).
- Complexity: Time O(n); Space O(1).
- Kotlin:
```kotlin
fun kadane(a:IntArray): Int {
    var cur=a[0]; var best=a[0]
    for(i in 1 until a.size){ cur=max(a[i], cur+a[i]); best=max(best, cur) }
    return best
}
```
- When to use: Profit calculations, DP base.
- Common mistakes: Initialize with 0 instead of first element (fails all-negative).

### 27. KMP String Search
- Definition: Linear-time substring search using prefix function.
- Intuition: Reuse matched prefix when mismatch occurs.
- Steps: build lps array → scan text with pattern pointer.
- Complexity: Time O(n+m); Space O(m).
- Kotlin:
```kotlin
fun kmp(text:String, pat:String): List<Int> {
    val lps=IntArray(pat.length); var len=0
    for(i in 1 until pat.length){ while(len>0 && pat[i]!=pat[len]) len=lps[len-1]; if(pat[i]==pat[len]) len++; lps[i]=len }
    val res=mutableListOf<Int>(); var j=0
    for(i in text.indices){ while(j>0 && text[i]!=pat[j]) j=lps[j-1]; if(text[i]==pat[j]) j++; if(j==pat.length){ res+=i-j+1; j=lps[j-1] } }
    return res
}
```
- When to use: Repeated substring searches, editors.
- Common mistakes: Wrong lps computation boundaries.

### 28. Rabin-Karp Rolling Hash
- Definition: Hash-based pattern search.
- Intuition: Compare hashes, verify on match to avoid collisions.
- Steps: precompute power; slide window updating hash.
- Complexity: Avg O(n+m); Space O(1).
- Kotlin:
```kotlin
fun rabinKarp(text:String, pat:String, base:Int=256, mod:Int=1_000_000_007): Boolean {
    val n=text.length; val m=pat.length; if(m>n) return false
    var hp=0L; var ht=0L; var power=1L
    repeat(m-1){ power = power * base % mod }
    for(i in 0 until m){ hp=(hp*base + pat[i].code)%mod; ht=(ht*base + text[i].code)%mod }
    if(hp==ht && text.startsWith(pat)) return true
    for(i in m until n){ ht = (ht - text[i-m].code*power)%mod; if(ht<0) ht+=mod; ht=(ht*base + text[i].code)%mod; if(ht==hp && text.substring(i-m+1, i+1)==pat) return true }
    return false
}
```
- When to use: Multiple pattern checks, plagiarism detection.
- Common mistakes: Not handling negative hash after subtraction.

### 29. Trie (Prefix Tree)
- Definition: Tree where edges labeled by chars to store words/prefixes.
- Intuition: Shared prefixes reduce duplication.
- Steps: insert by creating child nodes; search by traversal; mark end flags.
- Complexity: Time O(L) per op; Space O(chars).
- Kotlin:
```kotlin
class TrieNode { val child = arrayOfNulls<TrieNode>(26); var end=false }
class Trie { private val root=TrieNode()
    fun insert(w:String){ var n=root; for(c in w){ val i=c-'a'; if(n.child[i]==null) n.child[i]=TrieNode(); n=n.child[i]!! }; n.end=true }
    fun search(w:String):Boolean{ var n=root; for(c in w){ val i=c-'a'; n=n.child[i]?:return false }; return n.end }
    fun startsWith(p:String):Boolean{ var n=root; for(c in p){ val i=c-'a'; n=n.child[i]?:return false }; return true }
}
```
- When to use: Autocomplete, dictionary, prefix queries.
- Common mistakes: Forget to mark end-of-word flag.

### 30. Segment Tree
- Definition: Binary tree over array supporting range queries/updates.
- Intuition: Each node covers interval; combine children.
- Steps: build tree; query recursively; update leaf then recompute.
- Complexity: Time O(log n) per op; Space O(4n).
- Kotlin:
```kotlin
class SegTree(private val arr:IntArray){
    private val n=arr.size; private val tree=IntArray(4*n)
    init { build(1,0,n-1) }
    private fun build(node:Int,l:Int,r:Int){ if(l==r) tree[node]=arr[l] else { val m=(l+r)/2; build(node*2,l,m); build(node*2+1,m+1,r); tree[node]=tree[node*2]+tree[node*2+1] } }
    fun update(idx:Int,value:Int)=update(1,0,n-1,idx,value)
    private fun update(node:Int,l:Int,r:Int,idx:Int,value:Int){ if(l==r){ tree[node]=value; return }; val m=(l+r)/2; if(idx<=m) update(node*2,l,m,idx,value) else update(node*2+1,m+1,r,idx,value); tree[node]=tree[node*2]+tree[node*2+1] }
    fun query(L:Int,R:Int):Int = query(1,0,n-1,L,R)
    private fun query(node:Int,l:Int,r:Int,L:Int,R:Int):Int { if(R<l || r<L) return 0; if(L<=l && r<=R) return tree[node]; val m=(l+r)/2; return query(node*2,l,m,L,R)+query(node*2+1,m+1,r,L,R) }
}
```
- When to use: Frequent range sum/min updates.
- Common mistakes: Wrong base index ranges.

### 31. Fenwick Tree (BIT)
- Definition: Array-based prefix-sum tree.
- Intuition: Use lowbit to jump through ancestors.
- Steps: add(idx,val); sum(idx) accumulates via idx -= idx&-idx.
- Complexity: Time O(log n); Space O(n).
- Kotlin:
```kotlin
class BIT(n:Int){ private val bit=IntArray(n+1)
    fun add(idx:Int, delta:Int){ var i=idx; while(i<bit.size){ bit[i]+=delta; i+= i and -i } }
    fun sum(idx:Int):Int { var i=idx; var s=0; while(i>0){ s+=bit[i]; i-= i and -i }; return s }
    fun range(l:Int,r:Int)=sum(r)-sum(l-1)
}
```
- When to use: Inversion count, online prefix queries.
- Common mistakes: Using 0-based index without shifting.

### 32. Lowest Common Ancestor (Binary Lifting)
- Definition: Find lowest shared ancestor of two nodes in rooted tree.
- Intuition: Jump powers of two to lift deeper node then both together.
- Steps: preprocess parent[k][v]; depth array; lift deeper; ascend until parents match.
- Complexity: Preprocess O(n log n); Query O(log n); Space O(n log n).
- Kotlin:
```kotlin
class LCA(n:Int, root:Int, adj: List<List<Int>>){
    private val LOG=17; private val up=Array(LOG){IntArray(n)}; private val depth=IntArray(n)
    init { dfs(root,root,adj) }
    private fun dfs(v:Int,p:Int,adj:List<List<Int>>){ up[0][v]=p; for(k in 1 until LOG) up[k][v]=up[k-1][up[k-1][v]]
        for(nxt in adj[v]) if(nxt!=p){ depth[nxt]=depth[v]+1; dfs(nxt,v,adj) }
    }
    fun query(a:Int,b:Int): Int {
        var u=a; var v=b
        if(depth[u]<depth[v]) u=v.also{v=u}
        var diff=depth[u]-depth[v]; var k=0
        while(diff>0){ if(diff and 1==1) u=up[k][u]; diff=diff shr 1; k++ }
        if(u==v) return u
        for(i in LOG-1 downTo 0) if(up[i][u]!=up[i][v]){ u=up[i][u]; v=up[i][v] }
        return up[0][u]
    }
}
```
- When to use: Tree queries, ancestor checks.
- Common mistakes: Not setting parent of root to itself.

### 33. Tree Traversals (In/Pre/Post)
- Definition: Visit nodes in specific orders.
- Intuition: Inorder gives sorted for BST; preorder useful for serialization.
- Steps: preorder=root-left-right; inorder=left-root-right; postorder=left-right-root.
- Complexity: Time O(n); Space O(h).
- Kotlin:
```kotlin
fun inorder(node: TreeNode?, visit:(Int)->Unit){ if(node==null) return; inorder(node.left, visit); visit(node.`val`); inorder(node.right, visit) }
```
- When to use: Tree printing, conversions.
- Common mistakes: Forget null checks causing NPE.

### 34. BST Insert/Delete/Search
- Definition: Maintain ordered binary tree.
- Intuition: Left smaller, right larger.
- Steps: insert recursively; search by compare; delete handle 0/1/2 children.
- Complexity: Time O(h); Space O(h) recursion.
- Kotlin:
```kotlin
fun insert(root: TreeNode?, v:Int): TreeNode {
    root ?: return TreeNode(v)
    if(v < root.`val`) root.left=insert(root.left,v) else if(v>root.`val`) root.right=insert(root.right,v)
    return root
}
```
- When to use: Ordered set/map when tree balanced.
- Common mistakes: Not handling duplicate keys; unbalanced -> O(n).

### 35. AVL Rotations (Balancing)
- Definition: Self-balancing BST maintaining height difference <=1.
- Intuition: Rotate to restore balance after insert/delete.
- Steps: update height → compute balance → rotate (LL, RR, LR, RL).
- Complexity: Time O(log n); Space O(1) extra per op.
- Kotlin:
```kotlin
fun rotateRight(y: Node): Node { val x=y.left!!; val T2=x.right; x.right=y; y.left=T2; y.update(); x.update(); return x }
```
- When to use: Need ordered structure with worst-case guarantees.
- Common mistakes: Forget height updates after rotation.

### 36. 0-1 BFS
- Definition: Shortest path where edge weights are 0 or 1.
- Intuition: Deque push front for 0-cost, back for 1-cost edges.
- Steps: dist=INF; deque with source; relax edges with pushes accordingly.
- Complexity: Time O(V+E); Space O(V).
- Kotlin:
```kotlin
data class WEdge(val to:Int,val w:Int)
fun zeroOneBfs(n:Int, adj:Array<List<WEdge>>, src:Int): IntArray {
    val dist=IntArray(n){Int.MAX_VALUE}; dist[src]=0
    val dq:ArrayDeque<Int> = ArrayDeque(); dq.add(src)
    while(dq.isNotEmpty()){
        val v=dq.removeFirst()
        for(e in adj[v]){
            val nd=dist[v]+e.w
            if(nd < dist[e.to]){
                dist[e.to]=nd
                if(e.w==0) dq.addFirst(e.to) else dq.addLast(e.to)
            }
        }
    }
    return dist
}
```
- When to use: Grid problems with obstacles (0) / cost (1).
- Common mistakes: Using priority queue unnecessarily.

### 37. Longest Increasing Subsequence (n log n)
- Definition: Longest sequence with strictly increasing order.
- Intuition: Maintain minimal tail for each length.
- Steps: binary search position of current value in tails array.
- Complexity: Time O(n log n); Space O(n).
- Kotlin:
```kotlin
fun lis(a:IntArray): Int {
    val tail=IntArray(a.size); var len=0
    for(v in a){ var l=0; var r=len; while(l<r){ val m=(l+r) ushr 1; if(tail[m] < v) l=m+1 else r=m } tail[l]=v; if(l==len) len++ }
    return len
}
```
- When to use: Sequence optimization, patience sorting link.
- Common mistakes: Using <= leads to non-decreasing LIS.

### 38. Longest Common Subsequence
- Definition: Longest sequence appearing in same order in two strings.
- Intuition: DP over prefixes.
- Steps: dp[i][j] = if match 1+dp[i-1][j-1] else max(top,left).
- Complexity: Time O(mn); Space O(min(m,n)) if optimized.
- Kotlin:
```kotlin
fun lcs(a:String, b:String): Int {
    val n=a.length; val m=b.length; val dp=IntArray(m+1)
    for(i in 1..n){ var prev=0
        for(j in 1..m){ val temp=dp[j]; dp[j]= if(a[i-1]==b[j-1]) prev+1 else max(dp[j], dp[j-1]); prev=temp }
    }
    return dp[m]
}
```
- When to use: Diff tools, DNA comparison.
- Common mistakes: Not preserving previous row value (prev variable).

### 39. 0/1 Knapsack
- Definition: Max value with weight capacity, each item once.
- Intuition: DP over items and capacity.
- Steps: for each item, traverse capacity descending updating dp[w].
- Complexity: Time O(nW); Space O(W).
- Kotlin:
```kotlin
fun knapsack(w:IntArray, v:IntArray, cap:Int): Int {
    val dp=IntArray(cap+1)
    for(i in w.indices) for(c in cap downTo w[i]) dp[c]=max(dp[c], dp[c-w[i]]+v[i])
    return dp[cap]
}
```
- When to use: Budgeting, resource allocation.
- Common mistakes: Iterating capacity ascending causes item reuse.

### 40. Unbounded Knapsack / Min Coin Change
- Definition: Items can be used unlimited times; minimize coins.
- Intuition: Iterate capacity ascending to allow reuse.
- Steps: dp[0]=0; for coin in coins: for(amount from coin..target) dp[a]=min(dp[a], dp[a-coin]+1).
- Complexity: Time O(n*amount); Space O(amount).
- Kotlin:
```kotlin
fun coinChangeMin(coins:IntArray, amount:Int): Int {
    val dp=IntArray(amount+1){amount+1}; dp[0]=0
    for(c in coins) for(a in c..amount) dp[a]=min(dp[a], dp[a-c]+1)
    return if(dp[amount]>amount) -1 else dp[amount]
}
```
- When to use: Currency problems, rod cutting variant.
- Common mistakes: Using descending loop makes it 0/1 instead of unbounded.

### 41. Edit Distance
- Definition: Minimum insert/delete/replace to convert string A to B.
- Intuition: DP on prefixes considering last operation.
- Steps: dp[i][j]=min of insert/delete/replace.
- Complexity: Time O(mn); Space O(min(m,n)).
- Kotlin:
```kotlin
fun editDistance(a:String, b:String): Int {
    val m=a.length; val n=b.length; val dp=IntArray(n+1){it}
    for(i in 1..m){ var prev=dp[0]; dp[0]=i
        for(j in 1..n){ val temp=dp[j]; dp[j]= if(a[i-1]==b[j-1]) prev else 1+min(prev, min(dp[j], dp[j-1])); prev=temp }
    }
    return dp[n]
}
```
- When to use: Spell checkers, DNA alignment.
- Common mistakes: Overwriting dp[j] before saving previous diag value.

### 42. Matrix Exponentiation (Fibonacci)
- Definition: Raise transformation matrix to power for linear recurrences.
- Intuition: Fast exponentiation on 2x2 matrix gives nth Fibonacci.
- Steps: repeated squaring multiply when bit set.
- Complexity: Time O(log n); Space O(1).
- Kotlin:
```kotlin
fun fib(n:Long): Long {
    if(n==0L) return 0
    fun mul(a:LongArray,b:LongArray)= longArrayOf(
        a[0]*b[0]+a[1]*b[2], a[0]*b[1]+a[1]*b[3], a[2]*b[0]+a[3]*b[2], a[2]*b[1]+a[3]*b[3])
    fun pow(m:LongArray, e:Long): LongArray {
        if(e==1L) return m
        var half=pow(m, e/2); half=mul(half, half)
        return if(e%2==0L) half else mul(half, m)
    }
    val m=longArrayOf(1,1,1,0)
    val res=pow(m,n)
    return res[1]
}
```
- When to use: k-step recurrences, fast Fibonacci.
- Common mistakes: Overflow; prefer mod for large numbers.

### 43. Fast Power (Modular Exponentiation)
- Definition: Compute a^b mod m efficiently.
- Intuition: Square-and-multiply using bits of exponent.
- Steps: while b>0, if bit set multiply ans; square base; shift b.
- Complexity: Time O(log b); Space O(1).
- Kotlin:
```kotlin
fun modPow(base:Long, exp:Long, mod:Long): Long {
    var a=base%mod; var e=exp; var res=1L
    while(e>0){ if((e and 1L)==1L) res = res*a % mod; a = a*a % mod; e = e shr 1 }
    return res
}
```
- When to use: Cryptography, combinatorics.
- Common mistakes: Using Int overflow; mod should be Long.

### 44. Activity Selection (Greedy)
- Definition: Select max non-overlapping intervals.
- Intuition: Sort by earliest finishing time, pick feasible activities.
- Steps: sort by end; pick first; if start >= lastEnd pick.
- Complexity: Time O(n log n); Space O(1).
- Kotlin:
```kotlin
fun maxActivities(intervals: List<Pair<Int,Int>>): Int {
    val sorted=intervals.sortedBy { it.second }; var count=0; var end=-1
    for((s,e) in sorted) if(s>=end){ count++; end=e }
    return count
}
```
- When to use: Scheduling, meeting room booking.
- Common mistakes: Sorting by start instead of end reduces optimality.

### 45. Huffman Coding
- Definition: Optimal prefix-free binary codes based on frequencies.
- Intuition: Merge two lowest frequency nodes repeatedly.
- Steps: min-heap of freq; pop two; push merged; build tree; assign 0/1 edges.
- Complexity: Time O(n log n); Space O(n).
- Kotlin:
```kotlin
data class Huff(val ch:Char?, val f:Int, val left:Huff?, val right:Huff?)
fun huffman(freq: Map<Char,Int>): Map<Char,String> {
    val pq=PriorityQueue(compareBy<Huff>{it.f}); freq.forEach{ pq+=Huff(it.key,it.value,null,null) }
    while(pq.size>1){ val a=pq.poll(); val b=pq.poll(); pq+=Huff(null,a.f+b.f,a,b) }
    val codes=mutableMapOf<Char,String>()
    fun dfs(n:Huff?, path:String){ if(n==null) return; if(n.ch!=null) codes[n.ch]=path else { dfs(n.left, path+"0"); dfs(n.right, path+"1") } }
    dfs(pq.poll(), ""); return codes
}
```
- When to use: Compression.
- Common mistakes: Forget to handle single-character input.

### 46. Backtracking: Subsets (Power Set)
- Definition: Generate all subsets of a set.
- Intuition: Decision to include/exclude each element.
- Steps: dfs(index) → add current → try include → exclude.
- Complexity: Time O(2^n); Space O(n).
- Kotlin:
```kotlin
fun subsets(nums:IntArray): List<List<Int>> {
    val res=mutableListOf<List<Int>>(); val cur=mutableListOf<Int>()
    fun dfs(i:Int){ if(i==nums.size){ res+=cur.toList(); return }
        dfs(i+1); cur+=nums[i]; dfs(i+1); cur.removeAt(cur.lastIndex)
    }
    dfs(0); return res
}
```
- When to use: Feature combinations, bitmask generation.
- Common mistakes: Not removing element after recursion.

### 47. Backtracking: Permutations
- Definition: All orderings of elements.
- Intuition: Swap element into position and recurse.
- Steps: backtrack(pos) swapping with each candidate.
- Complexity: Time O(n·n!); Space O(n).
- Kotlin:
```kotlin
fun permutations(nums:IntArray): List<List<Int>> {
    val res=mutableListOf<List<Int>>()
    fun backtrack(i:Int){ if(i==nums.size){ res+=nums.toList(); return }
        for(j in i until nums.size){ nums[i]=nums[j].also{nums[j]=nums[i]}; backtrack(i+1); nums[i]=nums[j].also{nums[j]=nums[i]} }
    }
    backtrack(0); return res
}
```
- When to use: Ordering problems, search spaces.
- Common mistakes: Forget to swap back.

### 48. Backtracking: N-Queens
- Definition: Place N queens so none attack each other.
- Intuition: Row by row placing queen in safe columns.
- Steps: track used cols/diagonals; recurse rows; collect boards.
- Complexity: Time O(n!); Space O(n).
- Kotlin:
```kotlin
fun nQueens(n:Int): List<List<String>> {
    val cols=BooleanArray(n); val diag=BooleanArray(2*n); val anti=BooleanArray(2*n)
    val board=CharArray(n){'.'}; val cur=Array(n){ board.copyOf() }; val res=mutableListOf<List<String>>()
    fun dfs(r:Int){ if(r==n){ res+=cur.map{String(it)}; return }
        for(c in 0 until n) if(!cols[c] && !diag[r+c] && !anti[r-c+n]){
            cols[c]=diag[r+c]=anti[r-c+n]=true; cur[r][c]='Q'
            dfs(r+1)
            cols[c]=diag[r+c]=anti[r-c+n]=false; cur[r][c]='.'
        }
    }
    dfs(0); return res
}
```
- When to use: Constraint satisfaction demonstration.
- Common mistakes: Not resetting board cell on backtrack.

### 49. Bridges / Articulation Points (Tarjan)
- Definition: Edges or vertices whose removal increases components.
- Intuition: Use DFS low-link to detect back edges.
- Steps: DFS timestamps; low[u]=min(low[u], low[v]); if(low[v]>disc[u]) edge is bridge.
- Complexity: Time O(V+E); Space O(V).
- Kotlin:
```kotlin
fun bridges(adj: List<List<Int>>): List<Pair<Int,Int>> {
    val n=adj.size; val disc=IntArray(n){-1}; val low=IntArray(n); var time=0; val res=mutableListOf<Pair<Int,Int>>()
    fun dfs(u:Int, p:Int){ disc[u]=time; low[u]=time; time++
        for(v in adj[u]) if(v!=p){
            if(disc[v]==-1){ dfs(v,u); low[u]=min(low[u], low[v]); if(low[v] > disc[u]) res += u to v }
            else low[u]=min(low[u], disc[v])
        }
    }
    for(i in 0 until n) if(disc[i]==-1) dfs(i,-1)
    return res
}
```
- When to use: Network reliability, articulation analysis.
- Common mistakes: Ignoring parent edge when updating low.

### 50. Quickselect (Kth Order Statistic)
- Definition: Select kth smallest element in average linear time.
- Intuition: Partition like quicksort but recurse one side.
- Steps: choose pivot → partition → recurse into side containing k.
- Complexity: Avg O(n); worst O(n^2); Space O(1).
- Kotlin:
```kotlin
fun quickSelect(a:IntArray, k:Int): Int {
    var l=0; var r=a.lastIndex; val target=k
    while(true){
        var i=l; var j=r; val p=a[(l+r) ushr 1]
        while(i<=j){ while(a[i]<p) i++; while(a[j]>p) j--; if(i<=j){ a[i]=a[j].also{a[j]=a[i]}; i++; j-- } }
        if(target<=j) r=j else if(target>=i) l=i else return a[target]
    }
}
```
- When to use: Median, percentile queries.
- Common mistakes: Off-by-one in target index (k zero vs one based).

