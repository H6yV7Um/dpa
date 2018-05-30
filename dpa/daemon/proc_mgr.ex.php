<?php
/**
 * php并行控制
 *
 * PHP Version 5
 *
 * @since 2008-11-04
 * @author yangguang4@staff.sina.com.cn
 * @copyright  2008 Sina
 */
declare(ticks = 1);
class proc_mgr
{
  /**
   * 存储子进程PID
   *
   * @var array
   * @since 2008-11-04
   */
  private $_childrens;

  /**
   * 子进程运行分配数组
   *
   * @var array
   * @since 2008-11-04
   */
  private $_child_proc_dispatch_map;

  /**
   * 是否需要持久运行
   *
   * @var boolean
   * @since 2008-11-04
   */
  private $_persist_runing = false;

  /**
   * 构造函数
   *
   * @param array    $child_proc_dispatch_map  子进程运行分配数组
   * @param boolean  $persist                  父进程是否需要持久运行，监视控制子进程
   *
   * @since 2008-11-04
   * @return void
   */
  public function __construct(array $child_proc_dispatch_map, $persist = true)
  {
    $this->_child_proc_dispatch_map = $child_proc_dispatch_map;
    $this->_persist_runing = $persist;
  }

  /**
   * 启动子进程
   *
   * @since 2008-11-04
   * @return void
   */
  public function start_proc()
  {
    $isParent = true;
    for($i = 0; $i < count($this->_child_proc_dispatch_map); $i++)
    {
      $dispatch = $this->_child_proc_dispatch_map[$i];
      $pid = pcntl_fork();
      if ($pid == -1) {
        STDERR('could not fork');
        exit(1);
      } else if ($pid) {
        $this->_childrens[$i] = $pid;
      } else {
        $this->_init_child_proc($dispatch[0], $dispatch[1]);
        exit(0);
      }
    }
    if ($isParent) {
      if($this->_persist_runing)
      {
        pcntl_signal(SIGTERM, array($this, "kill_children"));
        pcntl_signal(SIGINT,  array($this, "kill_children"));
        pcntl_signal(SIGCHLD, array($this, "children_die"));
        //pcntl_signal(SIGCLD, array($this, "children_die"));
        while(1)
        {
          sleep(10);
        }
      }
      else
      {
        $children = $this->_childrens;
           $status = null;
            while (count( $children)) {
              $child_pid = pcntl_wait( $status);
              array_pop( $children);
          }
      }
    }
  }

  /**
   * 子进程被杀死时父进程的回调
   * 会试图重启子进程
   *
   * @since 2008-11-04
   * @return void
   */
  public function children_die($signo)
  {
    $status = null;
    $child_pid = pcntl_wait( $status);
    $idx = array_search($child_pid, $this->_childrens);
    if($idx !== FALSE)
    {
      $dispatch = $this->_child_proc_dispatch_map[$idx];
      $pid = pcntl_fork();
      if ($pid == -1) {
        STDERR('could not fork');
        exit(1);
      } else if ($pid) {
        MSG($child_pid ." child process exit , try restart , new child process " . $pid);
        $this->_childrens[$idx] = $pid;
      } else {
        $this->_init_child_proc($dispatch[0], $dispatch[1]);
      }
    }
  }

  /**
   * 父进程收到退出的信号的时候杀掉子进程
   *
   * @since 2008-11-04
   * @return void
   */
  public function kill_children($signo)
  {
    MSG('parent process get kill sig, stop child');
    //忽略子进程退出
    pcntl_signal(SIGCHLD, SIG_DFL);
    //pcntl_signal(SIGCLD, SIG_DFL);
    pcntl_signal(SIGTERM, SIG_DFL);
    foreach($this->_childrens as $pid)
    {
      posix_kill($pid, SIGTERM);
    }
    exit;
  }

  /**
   * 初始化启动子进程
   * 配置相关的信号处理
   *
   * @since 2008-11-04
   * @return void
   */
  private function _init_child_proc($cb, $para)
  {
    pcntl_signal(SIGTERM, SIG_DFL);
    pcntl_signal(SIGINT,  SIG_IGN);
    $isParent = false;
    call_user_func($cb, $para);
  }
}
?>
