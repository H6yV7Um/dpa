
一、
测试机配置：
cat /proc/meminfo  
MemTotal:       188592 kB
MemFree:          3712 kB
cat /proc/cpuinfo
cpu MHz         : 2992.692
cache size      : 2048 KB


daemon_diff_between_win_unix


1. windows 下 exec("php-win daemon.php >> a.txt") 并不会交给系统，而是等待其执行完以后，如，daemon耗时10秒，则需要等待10秒，再进入下一个流程，而
   linux   下 exec("php daemon.php >> a.txt &  ") 则会交给系统，不需要等待其执行完，而是几乎立即就进入下一个流程。

2. linux下具体时间测试为：daemon.php 是10秒钟就能执行完的程序
for循环执行 1   条 exec("php daemon.php >> a.txt &") 耗时 0.014秒
for循环执行 50  条 exec("php daemon.php >> a.txt &") 耗时 0.72秒
for循环执行 50  条 exec("php daemon.php >> a.txt &") daemon是120秒才能执行完的， 耗时 0.72秒
for循环执行 100 条 exec("php daemon.php >> a.txt &") 耗时 1.42秒
for循环执行 100 条 exec("php daemon.php >> a.txt &") daemon是120秒才能执行完的， 耗时 1.43秒
for循环执行 200 条 exec("php daemon.php >> a.txt &") 耗时 2.85秒

for循环执行 300 条 exec("php daemon.php >> a.txt &") 耗时 200秒
for循环执行 500 条 exec("php daemon.php >> a.txt &") 耗时 14~30秒  14,15,24,25比较多
for循环执行 500 条 exec("php daemon.php >> a.txt &") daemon是120秒才能执行完的， 耗时 172~290秒  14,15,24,25比较多
for循环执行 1000条 exec("php daemon.php >> a.txt &") 耗时  168秒   


二、pcntl_fork
测试机配置：
cat /proc/meminfo
MemTotal:      1034488 kB
MemFree:        107512 kB
cat /proc/cpuinfo
cpu MHz         : 3052.601
cache size      : 512 KB

1. 开300个子进程，每个进程29秒运行完
begin time: 2009-12-01 15:40:15 | microtime: 0.79710200 1259653215
end__ time: 2009-12-01 15:41:14 | microtime: 0.17537500 1259653274

2. 开400个子进程，每个进程29秒运行完
begin time: 2009-12-01 15:54:25 | microtime: 0.93370300 1259654065
end__ time: 2009-12-01 15:55:25 | microtime: 0.03675400 1259654125

3. 开500个子进程，每个进程29秒运行完
begin time: 2009-12-01 15:57:42 | microtime: 0.86439600 1259654262
end__ time: 2009-12-01 15:59:11 | microtime: 0.35031200 1259654351

4. 开1000个子进程，每个进程29秒运行完
begin time: 2009-12-01 16:01:15 | microtime: 0.99530700 1259654475
end__ time: 2009-12-01 16:02:45 | microtime: 0.79021300 1259654565
(似乎有丢失没有执行的情况发生) ,并没有显示所有的进程


// 同一的程序，采用如下方法 ,daemon.php需要10秒执行完
// 机器配置 同上 cpu 3052.601 mem 1034488 kB
linux   下 exec("php daemon.php >> a.txt &  ") 则会交给系统，不需要等待其执行完，而是几乎立即就进入下一个流程。

for循环执行 200 条 exec("php daemon.php >> a.txt &") 耗时 3.14秒
for循环执行 300 条 exec("php daemon.php >> a.txt &") 耗时 4.66秒
for循环执行 500 条 exec("php daemon.php >> a.txt &") 耗时 9.14秒
for循环执行1000 条 exec("php daemon.php >> a.txt &") 耗时 17.30秒

