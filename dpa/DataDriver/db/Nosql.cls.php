<?php
/**
 * NOSQL数据管理类
 * 对redis, memcache等的联合支持
 *
 * @version    $Id: Nosql.cls.php 48302 2008-05-30 10:24:54Z cheng_feng $
 * @package    Cache
 * @author     green_boxer@sina.com
 * @since      2012-09-01
 */
class Nosql
{
  /**
   * NoSQL实例
   *
   * @var object
   * @since 2012-09-01
   * @access public
   */
  var $l_nosql;

  /**
   * NoSQL种类
   *
   * @var object
   * @since 2012-09-01
   * @access public
   */
  var $l_nosql_type;

  /**
   * constructor
   *
   *
   * @since  2012-08-31
   * @access public
   * @return void
   */
  function __construct($a_nosql_type='redis', $dsn=array(), $options=false)
  {
    // 暂时只支持memcache和redis
    if ("redis" == $a_nosql_type) {
      $this->l_nosql_type = $a_nosql_type;  // 是memcache还是redis
      require_once( str_replace("\\","/",dirname(dirname(__FILE__)))."/nosql/Redised.cls.php");
      $this->l_nosql = new Redised($dsn, $options);
    } else {
      $this->l_nosql_type = "memcache";
      if (extension_loaded('memcached')) {
        require_once($GLOBALS['cfg']['PATH_RUNTIME'] . '/common/lib/MemCachedClient.php');
        $this->l_nosql = MemCachedClient::GetInstance('default');//
      } else {
        // 为了向前兼容，因此没有将Memcached的方法进行迎合修改，即添加缓存服务器的时候，沿用旧方式
        require_once( str_replace("\\","/",dirname(dirname(__FILE__)))."/nosql/Memcached.cls.php");
        if (empty($dsn))$this->l_nosql = new Memcached();
        else $this->l_nosql = new Memcached($dsn);
      }
    }
  }
  // for php4
  function Nosql($a_nosql_type='redis', $dsn=array(), $options=false)
  {
    $this->__construct($dsn, $options, $a_nosql_type);
  }

  /**
   * 写入一个值
   *
   * 使用注意：
   * 为了兼容, 如果是永不过期的数据，调用时就不设置$ttl即可
   *
   * @param string $key
   * @param mixed  $value
   * @param int    $ttl
   *
   * @since  2012-08-31
   * @access public
   * @return boolean
   */
  function set($key, $value, $ttl = null)
  {
    // memcache 的过期时间如果是0,表示永远不过期;
    // 而redis永远不过期只能调用set不是setex,redis的setex--过期时间0表示立即失效,非永久失效
    // 测试发现 memcache的 $ttl 为 0 或者null或者没有时间参数set($key,$value,MEMCACHE_COMPRESSED)均为永久存储, 为负数-10则不存储
    // 虽然默认值一个为0一个是null, 但都表示永久存储, 因此可以直接使用

    // memcache和redis存储的数据类型的差别, memcache能存储数组和对象，而redis只能存储序列化的数据,
    // 数组被redis存储后get出来的将是字符串"Array";对象被redis存储以后get出来的是字符串"Object"
    return $this->l_nosql->set($key, $value, $ttl);
  }

  /**
   * 获取一个值
   *
   * @param string $key
   *
   * @since  2012-08-31
   * @access public
   * @return mixed
   */
  function get($key)
  {
    return $this->l_nosql->get($key);
  }

  /**
   * 删除某个键值
   *
   * @param string $key
   *
   * @since  2012-08-31
   * @access public
   * @return void
   */
  function delete($key)
  {
    return $this->l_nosql->delete($key);
  }

  function close()
  {
    $this->l_nosql->close();
  }

  function __destruct()
  {
    $this->close();
  }
}
