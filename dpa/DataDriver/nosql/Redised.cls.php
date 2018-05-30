<?php
/**
 * NOSQL数据管理类
 * 对redis进行封装
 *
 * @version    $Id: Redised.cls.php 48302 2008-05-30 10:24:54Z cheng_feng $
 * @package    Cache
 * @author     green_boxer@sina.com
 * @since      2012-08-31
 */
require_once("configs/MDB2_db.conf.php");
__gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_REDIS_DSN_CONFIGS_FILE'],"redis_w","redis_r",array("SRV_REDIS_DSN_W","SRV_REDIS_DSN_R"));

class Redised
{
  /**
   * redis实例
   *
   * @var object
   * @since 2012-08-31
   * @access public
   */
  var $l_nosql;

  /**
   * constructor
   *
   *
   * @since  2012-08-31
   * @access public
   * @return void
   */
  function __construct($dsn=array(), $options=false)
  {
    $this->l_nosql = new redis();
    $this->connect($dsn, $options);
  }
  // for php4
  function Redised($dsn=array(), $options=false)
  {
    $this->__construct($dsn, $options);
  }

  /**
   * 连接服务器
   *
   * @param array or string $dsn
   * @param array $options
   */
  function connect($dsn=array(), $options=false)
  {
    $l_configs = array();

    // 如果没有指定 dsn ，则使用 $_SERVER 变量的数据
    if (is_array($dsn)) {
      if (empty($dsn)) {
        // 默认使用 SRV_REDIS_DSN_W 进行处理
        $l_configs = parse_url($_SERVER["SRV_REDIS_DSN_W"]);
      } else if (array_key_exists("host", $dsn)) {
        $l_configs["host"] = $dsn["host"];
        $l_configs["port"] = empty($dsn["port"]) ? 6379 : $dsn["port"];
      }
    }else if (is_string($dsn)) {
      $l_configs = parse_url($_SERVER["SRV_REDIS_DSN_W"]);
    }
    if (empty($l_configs)) exit("redis -- error!");
    $this->l_nosql->connect($l_configs["host"], $l_configs["port"]);
  }

  /**
   * 写入一个值, 是一个统一的方法，支持设置过期时间的，也可不用设置过期时间
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
    if( null === $ttl)
    {
      return $this->l_nosql->set($key, $value);
    }
    else
    {
      return $this->l_nosql->setex($key, $ttl, $value);  // 等于0立即过期, 小于0不存储
    }
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
