<?php
/**
 * @copyright Copyright(c) 2009
 * All rights reserved.
 *
 * @filesource  UnixDaemon.php
 * @author    chengfeng
 * @version   $Id: UnixDaemon.php,v 1.3 2009/09/01 10:44:23  Exp $
 * @package    SystemDriver
 */
require_once("common/Files.cls.php");
/**
 * UnixDaemon实现
 * @package    SystemDriver
 */
class UnixDaemon
{
  /**
   * File that stored daemon pid
   *
   * @var string
   */
  var $_pidLogFile = "/tmp/daemon.pid";

  /**
   * Enter description here...
   *
   * @var int daemon pid
   */
  var $_pid = 0;

  /**
   * Constructor...
   *
   * @param string $strPidFile
   */
  function __construct($strPidFile = "")
  {
    if(!empty($strPidFile))
    {
      // 创建文件和路径,如果没有则创建路径和该文件
      $files = new Files();
      $files->overwriteContent("",$strPidFile);
      $strPath = dirname($strPidFile);
      // 保证创建成功
      if(is_dir($strPath))
      {
        $this->_pidLogFile = $strPidFile;
      }
    }
  }

  // 兼容php4
  function UnixDaemon($strPidFile = "")
  {
    $this->__construct($strPidFile);
  }

  /**
   * return daemon pid
   * @access public
   */
  function getpid()
  {
    return $this->_pid;
  }

  /**
   * Set signal handler
   *
   * @param int $signo
   * @return bool
   * @throws Exception
   */
  function sig_handler($signo)
  {
    switch($signo)
    {
      case SIGTERM:
        //Unlink pid log file
        if(@unlink($this->_pidLogFile) === false)
        {
          write_log("Cannot delete pid log file", LOG_TYPE_FAIL);
        }
        else
        {
          write_log("Daemon shutdown.");
        }

        echo "Daemon stopped.";
        exit();
      default:
        break;
    }
  }

  /**
   * Start daemon
   *
   * @throws Exception
   */
  function start()
  {
    $this->daemonize();

    write_log("Daemon start.");
  }

  /**
   * Stop daemon
   *
   * @return bool
   */
  function stop()
  {
    $this->get_pid_from_file();

    //Kill process
    if(!posix_kill($this->_pid, SIGTERM))
    {
      write_log ( " Class: ".__CLASS__ . ' | File: ' .__FILE__.'| Line: '.__LINE__ ."Cannot send SIGTERM to daemon");
    }

    write_log("Sending SIGTERM to daemon.");
  }

  /**
   * Restart daemon
   *
   * @return bool
   */
  function restart()
  {
    $this->stop();
    $this->start();
  }

  /**
   * Get pid from pid log file
   *
   * @throws Exception
   */
  function get_pid_from_file()
  {
    $strContents = file_get_contents($this->_pidLogFile);

    $this->_pid = @intval($strContents);
  }

  /**
   * Log pid in pid file
   *
   * @throws Exception
   */
  function log_pid()
  {
    $files = new Files();
    $files->overwriteContent($this->_pid,$this->_pidLogFile);
  }

  /**
   * Get process arguments from cmdline file of Linux ProcFS
   *
   * @param int $intPid
   * @return string
   * @throws Exception
   */
  function get_linux_proc_args($intPid)
  {
    $strProcCmdlineFile = "/proc/" . $intPid . "/cmdline";

    if (file_exists($strProcCmdlineFile)) {
      $strContents = @file_get_contents($strProcCmdlineFile);

      $strContents = preg_replace("/[^\w\.\/\-]/", " ", trim($strContents));
      $strContents = preg_replace("/\s+/", " ", $strContents);
      $arrTemp = explode(" ", $strContents);
    }else {
      $arrTemp = array();
    }

    if(count($arrTemp) < 2)
    {
      return "";
    }

    return trim($arrTemp[1]);
  }

  /**
   * Get Linux process executing filename from exe file of ProcFS
   *
   * @param int $intPid
   * @return string
   * @throws Exception
   */
  function get_linux_proc_exe($intPid)
  {
    $strProcExeFile = "/proc/" . $intPid . "/exe";

    if (is_dir(dirname($strProcExeFile))) {
      $strLink = @readlink($strProcExeFile);
    }else {
      $strLink = "";
    }

    return $strLink;
  }

  /**
   * Check whether daemon is running or not
   *
   * @return bool
   * @throws Exception
   */
  function check_running()
  {
    $strContents = file_get_contents($this->_pidLogFile);

    $intPid = intval($strContents);

    switch(strtolower(PHP_OS))
    {
      case "freebsd":
      case "linux":
      {

        $strExe = $this->get_linux_proc_exe($intPid);
        $strArgs = $this->get_linux_proc_args($intPid);

        if($strExe !=(PHP_BINDIR . "/php"))
        {
          return false;
        }

        if($strArgs != $_SERVER['PHP_SELF'])
        {
          return false;
        }

        break;
      }
      default:
        return false;
        break;
    }

    return $intPid;
  }

  /**
   * Daemonize program
   *
   * @throws Exception
   */
  function daemonize()
  {
    $pid = posix_getpid();
    $this->_pid = $pid;

    //Running a Single Copy...
    $chk = $this->check_running();
    if($chk !== false)
    {
      write_log( " Class: ".__CLASS__ . ' | File: ' .__FILE__.'| Line: '. __LINE__ .": Daemon already running");
    }

    //Detatch from the controlling terminal
    if(!posix_setsid())
    {
      write_log( " Class: ".__CLASS__ . ' | File: ' .__FILE__.'| Line: '.__LINE__ .": Cannot detach from terminal");
    }

    $this->log_pid();
  }
}

/**
 * Write log
 *
 * @param string $msg
 */
function write_log($msg)
{
  if(!empty($msg))
  {
    echo $msg."\n";
  }
}
