<?php
/**
 * Memcache_addoneAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("common/lib/Publish.cls.php");
require_once('mvc/AddAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');
require_once("DataDriver/nosql/Memcached.cls.php");

class Memcache_addoneAction extends AddAction {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    print_r($form);
    print_r($get);
    print_r($request);
    exit;
    // 向memcache中加入一条或多条记录
    if (!empty($l_rlt)) {
      // 采用memcache，将其放到队列中，由专门的发布程序进行发布成功后记录到数据库中
      // 注册到memcache数组中去, [xiangmuID][biaoID] => 条件表达式
      $memcache = new Memcached();

      // key如何确定?
      $l_mem_key = "_lanmu_publish_";
      //if (1==$l_i) $memcache->delete($l_mem_key);
      $get_result = $memcache->get($l_mem_key);

      if (empty($get_result)) {
        // bool(false)的话会进行此步骤，向其中注册该数据
        $get_result = array();
        $get_result[$l_p_id][$l_t_id][] = $l_where;
        $memcache->set($l_mem_key, $get_result, 0);
      }else {
        if (!isset($get_result[$l_p_id][$l_t_id])) {
          $get_result[$l_p_id][$l_t_id][] = $l_where;
          $memcache->set($l_mem_key, $get_result, 0);
        }else {
          if (!in_array($l_where, $get_result[$l_p_id][$l_t_id])) {
            $get_result[$l_p_id][$l_t_id][] = $l_where;  // 不存在则加入进去
            $memcache->set($l_mem_key, $get_result, 0);
          }
        }
      }
    }
  }
}
