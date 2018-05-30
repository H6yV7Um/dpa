<?php
class MemCachedClient {
  private static $memcached_client_;
  private $memcached_;

  // 构造方法声明为private，防止直接创建对象
  private function __construct($memcache_group) {
    if (!array_key_exists($memcache_group, $GLOBALS['g_memcached_servers'])) return ;
    $this->memcached_ = new Memcached;
    // 设置一致性哈希函数
    $this->memcached_->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC);
    $this->memcached_->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);

    // 解析server列表
    foreach ($GLOBALS['g_memcached_servers'][$memcache_group] as $host_port) {
      // 0->host, 1->port
      $this->memcached_->addServer($host_port[0], $host_port[1]);
    }
  }

  // singleton 方法
  public static function GetInstance($memcache_group) {
    if (!isset(self::$memcached_client_[$memcache_group]))
      self::$memcached_client_[$memcache_group] = new MemCachedClient($memcache_group);
    return self::$memcached_client_[$memcache_group];
  }

  // 阻止用户复制对象实例
  private function __clone() {
    // TODO: Add log info
  }

  public function AddCount($key, $property, $expiration, $count=1) {
    # 增加计数，带Cas
    $count = (int)$count;
    $cas = 0.0;
    if ($this->memcached_) {
      $i = 0;
      do {
        $object = $this->memcached_->get($key, null, $cas);
        if (is_object($object) && property_exists($object, $property)) {
          $object->$property = (int)$object->$property + $count;
          if ($this->memcached_->cas($cas, $key, $object, $expiration))
            return true;
        } elseif (is_array($object) && isset($object[$property])) {
          $object[$property] = (int)$object[$property] + $count;
          if ($this->memcached_->cas($cas, $key, $object, $expiration))
            return true;
        } else
          return false;
        $i += 1;
      } while($i < 5 &&
        $this->memcached_->getResultCode() == Memcached::RES_DATA_EXISTS);
      $this->memcached_->delete($key);
      return true;
    }
    return false;
  }

  public function ReduceCount($key, $property, $expiration, $count=1) {
    # 减少计数，带Cas
    $count = (int)$count;
    $cas = 0.0;
    if ($this->memcached_) {
      $i = 0;
      do {
        $object = $this->memcached_->get($key, null, $cas);
        if (is_object($object) && property_exists($object, $property)) {
          $object->$property = (int)$object->$property;
          if ($object->$property >= $count)
            $object->$property -= $count;
          else
            $object->$property = 0;
          if ($this->memcached_->cas($cas, $key, $object, $expiration))
            return true;
        } elseif (is_array($object) && isset($object[$property])) {
          $object[$property] = (int)$object[$property];
          if ($object[$property] >= $count)
            $object[$property] -= $count;
          else
            $object[$property] = 0;
          if ($this->memcached_->cas($cas, $key, $object, $expiration))
            return true;
        } else
          return false;
        $i += 1;
      } while($i < 5 &&
        $this->memcached_->getResultCode() == Memcached::RES_DATA_EXISTS);
      $this->memcached_->delete($key);
      return true;
    }
    return false;
  }

  public function UpdateWithCas($key, $property_values, $expiration) {
    $cas = 0.0;
    if ($this->memcached_) {
      $object = $this->memcached_->get($key, null, $cas);
      if (is_object($object)) {
        foreach ($property_values as $property => $value)
          $object->$property = $value;
        if ($this->memcached_->cas($cas, $key, $object, $expiration))
          return true;
        $this->memcached_->delete($key);
      } else if (is_array($object)) {
        foreach ($property_values as $property => $value)
          $object[$property] = $value;
        if ($this->memcached_->cas($cas, $key, $object, $expiration))
          return true;
        $this->memcached_->delete($key);
      } else if (Memcached::RES_NOTFOUND == $this->memcached_->getResultCode()) {
        return true;
      }
    }
    return false;
  }

  public function get($key, $cache_cb=null, &$cas=null) {
    # overload the get function
    if ($this->memcached_)
      return $this->memcached_->get($key, $cache_cb, $cas);
    return NULL;
  }

  public function __call($method_name, $arguments) {
    if ($this->memcached_ && method_exists($this->memcached_, $method_name))
      return call_user_func_array(array($this->memcached_, $method_name),
                                  $arguments);
    return NULL;
  }
}

