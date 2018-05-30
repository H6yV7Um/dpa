<?php
require_once $GLOBALS['cfg']['PATH_PHP_LIBS'] . '/thirdparty/log4php/Logger.php';

class Log {
  private static $log_handler_;

  // 构造方法声明为private，防止直接创建对象
  private function __construct() {
    self::Warn("Create Log not allowed");
  }

  private static function GetLogger() {
    if (!empty(self::$log_handler_))
      return self::$log_handler_;
    Logger::configure($GLOBALS['cfg']['LOG_CONF_FILE']);
    self::$log_handler_ = Logger::getLogger("default");
    @self::$log_handler_->info("Init logger success");
    return self::$log_handler_;
  }

  private static function GetCallInfo() {
    $call_info = debug_backtrace();
    return empty($call_info) ? '' : $call_info[1]['file'] . ' ' .
                 $call_info[1]['line'] . ': ';
  }

  public static function Trace($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->trace(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }

  public static function Debug($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->debug(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }

  public static function Info($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->info(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }

  public static function Warn($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->warn(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }

  public static function Error($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->error(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }

  public static function Fatal($message, $if_add_callinfo=true) {
    $log_handler = self::GetLogger();
    @$log_handler->fatal(($if_add_callinfo ? self::GetCallInfo() : '') . $message);
  }
}

/*
 * 扩展LoggerAppenderDailyFile，重写setFile。如果定义了LOG4PHP_PATH，使用LOG4PHP_PATH，否则使用log4php.properties中的log4php.appender.default.file
 * 使用自定义路径：
 * 1.define('LOG4PHP_PATH', 'your path');
 * 2.修改properties
 *   增加 log4php.appender.myappender = MyAppender
 *   修改default为myappender
 * 3.详细配置请参考http://dianshang.mmqdd.com/svn/dssvn/trunk/php/wanhui/map/log4php.properties
 */
class CustomAppender extends LoggerAppenderDailyFile {

  public function setFile($file) {
    if (defined('LOG4PHP_PATH')) {
      $file_name = end(explode('/', $file));
      $this->file = LOG4PHP_PATH . $file_name;
    } else
      $this->file = $file;
  }
}