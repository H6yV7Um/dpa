<?php
class SessionMemCachedClient {
  const MEMCACHE_GROUP = "session";
  private static $session_memcached_client_;
  private $memcached0_;
  private $memcached1_;

  // 构造方法声明为private，防止直接创建对象
  private function __construct() {
    if (!extension_loaded('memcached')) require_once($GLOBALS['cfg']['PATH_RUNTIME'] . '/DataDriver/nosql/Memcached.cls.php');
    $this->memcached0_ = new Memcached;
    // 设置一致性哈希函数
    $this->memcached0_->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
    $this->memcached0_->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);

    $this->memcached1_ = new Memcached;
    // 设置一致性哈希函数
    $this->memcached1_->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
    $this->memcached1_->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);

    // 解析server列表
    if (!array_key_exists(self::MEMCACHE_GROUP, $GLOBALS['g_memcached_servers'])) return ;
    $i = true;
    foreach ($GLOBALS['g_memcached_servers'][self::MEMCACHE_GROUP] as $host_port) {
      // 0->host, 1->port
      if ($i)
        $this->memcached0_->addServer($host_port[0], $host_port[1]);
      else
        $this->memcached1_->addServer($host_port[0], $host_port[1]);
      $i = !$i;
    }
  }

  // singleton 方法
  public static function GetInstance() {
    if (!isset(self::$session_memcached_client_))
      self::$session_memcached_client_ = new SessionMemCachedClient();
    return self::$session_memcached_client_;
  }

  // 阻止用户复制对象实例
  private function __clone() {
    // TODO: Add log info
  }

  public function Get($key) {
    $result = NULL;
    if ($this->memcached0_)
      $result = $this->memcached0_->get($key);
    if ($result)
      return $result;
    if ($this->memcached1_)
      $result = $this->memcached1_->get($key);
    return $result;
  }

  public function LatestGetCacheAvailable() {
    if ($this->memcached0_ && (Memcached::RES_NOTFOUND == $this->memcached0_->getResultCode() || Memcached::RES_SUCCESS == $this->memcached0_->getResultCode()))
      return true;
    if ($this->memcached1_ && (Memcached::RES_NOTFOUND == $this->memcached0_->getResultCode() || Memcached::RES_SUCCESS == $this->memcached0_->getResultCode()))
      return true;
    return false;
  }

  public function Delete($key) {
    $result = False;
    if ($this->memcached0_ && $this->memcached0_->delete($key))
      $result = True;
    if ($this->memcached1_ && $this->memcached1_->delete($key))
      $result = True;
    return $result;
  }

  public function Set($key, $value, $expiration) {
    $result = False;
    if ($this->memcached0_ && $this->memcached0_->set($key, $value, $expiration))
      $result = True;
    if ($this->memcached1_ && $this->memcached1_->set($key, $value, $expiration))
      $result = True;
    return $result;
  }
}

