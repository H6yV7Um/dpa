<?php
/**
 * 执行完以后自动关闭
 * Windows:
 *    shutdown -r 重启计算机;shutdown -s 关闭计算机;shutdown -l 注销当前用户
 * UNIX:
 *    reboot 重启系统; poweroff 关闭系统;
 *

// windows xp 锁屏命令
at /delete 9:31 rundll32.exe user32.dll,LockWorkStation
rundll32.exe user32.dll,LockWorkStation

 *
 */
class Shutdown_computer
{
  function restart_computer()
  {
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )
    {
      $l_cmd = 'shutdown -r';
    }else {
      // 需要进行身份判断，如果是root才能执行
      $l_cmd = 'sudo reboot';
    }
    exec($l_cmd);
  }

  function shutdown()
  {
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )
    {
      $l_cmd = 'shutdown -s';
    }else {
      // 需要进行身份判断，如果是root才能执行
      $l_cmd = 'sudo poweroff';
    }
    exec($l_cmd);
  }
}
