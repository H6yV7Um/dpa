[sql]
select id,{所针对的项目id},{所针对的表id},{所针对的文档id} from {娱乐频道评论}

[code]<?php
$zd_p_id = '{所针对的项目id}';
$zd_t_id = '{所针对的表id}';
$zd__id  = '{所针对的文档id}';
$l_table_name = '{娱乐频道评论}';

// 向redis中存放评论的汇总数
if (extension_loaded('redis') && isset($form[$zd_p_id])) {
  require_once("DataDriver/db/Nosql.cls.php");
  $dbR = $a_arr['dbR'];
  $dbR->table_name = $l_table_name;
  $l_rlt = $dbR->getCountNum('where '.$zd_p_id.'='.$form[$zd_p_id].' and '.$zd_t_id.'='.$form[$zd_t_id].' and '.$zd__id.'='.$form[$zd__id]); // 此类型投票的数量

  //if (!defined("LOG_PATH")) define("LOG_PATH","/tmp");
  //file_put_contents(LOG_PATH."/redis_".date("Y-m").".txt", " $l_table_name  ". $dbR->getSQL(). " ". " ".var_export($l_rlt, true), FILE_APPEND);

  if (PEAR::isError($l_rlt)){
    return null;
  }

  $l_p_t_id_str = $form[$zd_p_id].'_'.$form[$zd_t_id].'_'.$form[$zd__id];

  $l_nosql = new Nosql("redis");
  $l_r_key = "liuyan_".$l_p_t_id_str."_num";
  $l_nosql->set($l_r_key, $l_rlt);

  // 同时更新到另外一张表中去
}

return null;