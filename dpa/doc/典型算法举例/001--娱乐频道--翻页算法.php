[sql]
select {文档标题},{创建日期},{创建时间},{文档发布成html的外网url},{摘要},{权重} from {正文页}

[code]<?php
require_once("common/Pager.cls.php");
$dbR = $a_arr['dbR'];

// 总数
$dbR->table_name = '{正文页}';
$sql_where = 'where status_="use"';
$itemSum = $dbR->getCountNum($sql_where);

// 页面条目数
if (isset($a_arr['f_def_duo']['pagesize']['default'])) $l_pageSize = $a_arr['f_def_duo']['pagesize']['default'] + 0;
$l_pageSize = ($l_pageSize<1)?1:$l_pageSize;  // 保证非0, 后面的除法避免除数为0

// 第几页
if (isset($a_arr['f_data']['id'])) $l_p = $a_arr['f_data']['id'] + 0;   // 当前页码用文档的id。因为一般首页都是从id为1开始
else $l_p = 1;
$l_p = ($l_p>ceil($itemSum/$l_pageSize))?ceil($itemSum/$l_pageSize):$l_p;
$l_p = ($l_p<1)?1:$l_p;


$l_flag = 'p';
//$page = new Pager('index.shtml',$itemSum,$l_pageSize,$l_p,$l_flag,array($l_flag),'');
$page = new Pager('index.shtml',$itemSum,$l_pageSize,$l_p,$l_flag,array($l_flag));
$html = $page->getBar();

return $html;
