<?php
/**
 * 数据缓存管理类, 包括了memcached和redis等这些NOSQL数据库
 * 对memcache进行封装
 *
 * @version    $Id: MemCacheManager.inc.php 48302 2008-05-30 10:24:54Z cheng_feng $
 * @package    Cacher
 * @author     green_boxer@sina.com
 * @since      2007-07-10
 */
class Memcached
{
  const OPT_HASH=1;
  const HASH_CRC=1;
  const OPT_DISTRIBUTION=1;
  const DISTRIBUTION_CONSISTENT=1;
  const RES_DATA_EXISTS=1;
  const RES_NOTFOUND=1;

  /**
   * 是否支持缓存加锁
   *
   * @var boolean
   * @since 2007-07-10
   * @access public
   */
  var $LockAble = false;

  /**
   * 缓存用的key的全局后缀
   *
   * @var string
   * @since 2007-08-08
   * @access private
   */
  var $_keyPrefix = false;

  /**
   * 缓存配置
   *
   * @var array
   * @since 2007-08-06
   * @access public
   */
  var $cacheOptions;

  /**
   * Memcache实例
   *
   * @var object
   * @since 2007-08-08
   * @access public
   */
  var $memcache;

  /**
   * constructor
   *
   *
   * @since  2007-04-20
   * @access public
   * @return void
   */
  function Memcached($CacheServer=null)
  {
    $this->memcache = new Memcache;
    if (null===$CacheServer) {
      $this->setCachePath("127.0.0.1:11211");
    }
  }

  /**
   * 写入一个值
   *
   * @param string $key
   * @param mixed  $value
   * @param int    $ttl
   *
   * 测试发现 $ttl 为 0 或者null或者没有时间参数set($key,$value,MEMCACHE_COMPRESSED)均为永久存储, 为负数-10则不存储
   *
   * @since  2007-04-20
   * @access public
   * @return boolean
   */
  function set($key, $value, $ttl = 0)
  {
    if($ttl > 0)
    {
      $this->cacheOptions['lifeTime'] = $ttl;
    }
    if($this->_keyPrefix)
    {
      $key = $this->_keyPrefix . $key;
    }
    return $this->memcache->set($key, $value, MEMCACHE_COMPRESSED, $ttl);
  }

  function cas($cas, $key, $object, $expiration)
  {
    return $this->set($key, $object, $expiration);
  }

  function add($key, $value, $ttl = 0)
  {
    return $this->set($key, $value, $ttl);
  }

  /**
   * 获取一个值
   *
   * @param string $key
   *
   * @since  2005-9-22
   * @access public
   * @return mixed
   */
  function get($key)
  {
    if($this->_keyPrefix)
    {
      $key = $this->_keyPrefix . $key;
    }
    return $this->memcache->get($key);
  }

  function getMulti($key_arr)
  {
      $data_arr = array();
      if ($key_arr)
      foreach ($key_arr as $key) {
          $value = self::get($key);
            if (false !== $value) $data_arr[$key] = $value;
      }
      return $data_arr;
  }

  /**
   * 删除某个键值
   *
   * @param string $key
   *
   * @since  2007-04-20
   * @access public
   * @return void
   */
  function delete($key)
  {
    return $this->memcache->delete($key);
  }

  /**
   * 设置Memcache 服务器地址 host:port
   * 多个服务器用空格分割
   *
   * @param string $CacheServer
   *
   * @since  2007-08-08
   * @access public
   * @return void
   */
  function setCachePath($CacheServer)
  {
    $this->cacheOptions['cacheServer'] = $CacheServer;
    $memcached_servers = explode(' ', $CacheServer);
    foreach ($memcached_servers as $memcached) {
      list($server, $port) = explode(':', $memcached);
      $this->memcache->addServer($server, $port, FALSE);
    }
  }

  /**
    * 添加MemCache服务器
    *
    * @param string $host
    * @param int $port
    *
    * @since 2007-08-08
    * @access public
    * @return void
    */
  function addServer($host, $port)
  {
    $this->memcache->addServer($host, $port, FALSE);
  }

  /**
   * 为缓存的Key设一个全局的前缀
   *
   * @param string $prefix
   *
   * @since 2007-08-08
   * @access public
   * @return void
   */
  function setKeyPrefix($prefix)
  {
    $this->_keyPrefix = $prefix;
  }

  function close()
  {
    $this->memcache->close();
  }

  function __destruct()
  {
    $this->close();
  }

  function setOption() {}

  function getResultCode()
  {
    return 1;
  }
}

