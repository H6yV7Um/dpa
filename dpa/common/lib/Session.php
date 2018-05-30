<?php
require_once('SessionMemCachedClient.php');

class Session {
  const SESSION_PREFIX = "SESS_";
  // single ton
  private static $session_handler_;

  public static function Init() {
    if (!isset(self::$session_handler_))
      self::$session_handler_ = new Session();
  }

  // 阻止用户复制对象实例
  public function __clone() {
    trigger_error('Clone Session is not allowed.', E_USER_ERROR);
  }

  private function __construct() {
    session_set_save_handler(array($this, "Open"),
                             array($this, "Close"),
                             array($this, "Read"),
                             array($this, "Write"),
                             array($this, "Destroy"),
                             array($this, "Gc"));
    // for web user
    /* ini_set("session.name", "SESSIONID");
    if (isset($_GET[SESSIONID]) && !empty($_GET[SESSIONID])){
        $session_id = $_GET[SESSIONID];
    } elseif (isset($_COOKIE[SESSIONID]) && !empty($_COOKIE[SESSIONID])) {
        $session_id = Cookie::Get(SESSIONID);
    } else {
        // SESSION 生成因子尽可能丰富些，与当前机器性质绑定，确保多机器生成的不同
        $sessFactor = ''. session_id() . $_SERVER['SERVER_ADDR'] . microtime() . uniqid();
        if(file_exists("/etc/passwd")) {
            $sessFactor .= filectime("/etc/passwd"). md5_file("/etc/passwd");
        }
        $session_id = md5($sessFactor);
        Cookie::Set(SESSIONID, $session_id);
    }
    session_id($session_id); */
    //session_start();
  }

  public function __destruct() {
    session_write_close();
  }

  public function Open($save_path, $session_name) {
    return true;
  }

  public function Close() {
    return true;
  }

  public function Read($session_id) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    return (string)$memcached_client_->get($key);
  }

  public function Write($session_id, $data) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    return $memcached_client_->set($key, $data, SESSION_EXPIRE_TIME);
  }

  public function Destroy($session_id) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    return $memcached_client_->delete($key);
  }

  public function Gc($maxlifetime) {
    return true;
  }
}

Session::Init();
