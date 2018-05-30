<?PHP
/**
 * Log.cls.php
 * @author  chengfeng 2008-09-23
 */
class Log {
  /**
  * append content to file
  * @access public
  * @param string $content content will be written
  * @param int $level log level
  * @return bool
  */
  function Append( $str, $log_file="", $log_path='', $level=0){
    if (''==$log_path) $log_path = $GLOBALS['cfg']['LOG_PATH'];  // 改为全局变量后所做的修改

    $today = date('Y-m-d');
    $str = date("Y-m-d H:i:s") . " " . $str . "\n";
    switch ($level)
    {
      case 0 :
      default:
        if (empty($log_file)) {
          $log_file = $today . ".txt";
        }
      break;
    }
    $logfile = rtrim($log_path," /")  ."/". $log_file;

    if (file_exists($logfile)) {
      file_put_contents($logfile, $str, FILE_APPEND);
    } else{
      file_put_contents($logfile, $str);
    }

    return true;
  }
}
