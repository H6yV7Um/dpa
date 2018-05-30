[sql]
select id,{所针对的项目id},{所针对的表id},{所针对的文档id},{投票类型} from {娱乐频道投票}

[code]<?php
$zd_p_id = '{所针对的项目id}';
$zd_t_id = '{所针对的表id}';
$zd__id  = '{所针对的文档id}';
$zd_type = '{投票类型}';
$l_table_name = '{娱乐频道投票}';

// 向redis中存放顶或踩的汇总数
if (extension_loaded('redis') && isset($form[$zd_p_id])) {
  require_once("DataDriver/db/Nosql.cls.php");
  $dbR = $a_arr['dbR'];
  $dbR->table_name = $l_table_name;
  $l_rlt = $dbR->getCountNum('where '.$zd_p_id.'='.$form[$zd_p_id].' and '.$zd_t_id.'='.$form[$zd_t_id].' and '.$zd__id.'='.$form[$zd__id].' and '.$zd_type.'="'.$form[$zd_type].'"'); // 此类型投票的数量

  //if (!defined("LOG_PATH")) define("LOG_PATH","/tmp");
  //file_put_contents(LOG_PATH."/redis_".date("Y-m").".txt", " $l_table_name  ". $dbR->getSQL(). " ". " ".var_export($l_rlt, true), FILE_APPEND);

  if (PEAR::isError($l_rlt)){
    return null;
  }

  $l_p_t_id_str = $form[$zd_p_id].'_'.$form[$zd_t_id].'_'.$form[$zd__id];

  $l_nosql = new Nosql("redis");

  if ($form[$zd_type]>0) {
    $l_r_key = "vote_".$l_p_t_id_str."_up";
  }else if (0==$form[$zd_type]) {
    $l_r_key = "vote_".$l_p_t_id_str."_0";
  }else {
    $l_r_key = "vote_".$l_p_t_id_str."_dn";
    $l_rlt = $l_rlt * -1;
  }
  $l_nosql->set($l_r_key, $l_rlt);

  // 同时更新到另外一张表中去
}

return null;