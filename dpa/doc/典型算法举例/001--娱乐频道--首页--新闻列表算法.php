[sql]
select {文档标题},{创建日期},{创建时间},{文档发布成html的外网url},{摘要},{权重} from {正文页}

[code]<?php
$dbR = $a_arr['dbR'];

if (isset($a_arr['f_def_duo']['pagesize']['default'])){
  $l_pageSize = $a_arr['f_def_duo']['pagesize']['default'] + 0;
}else {
  $l_pageSize = 2;
}
if (isset($a_arr['f_data']['id'])){
  $l_p = $a_arr['f_data']['id'] + 0;   // 当前页码用文档的id。因为一般首页都是从id为1开始
}else {
  $l_p = 1;
}
$l_p = ($l_p<1)?1:$l_p;

$offset = ($l_p-1) * $l_pageSize;
$dbR->table_name = '{正文页}';
$l_rlt = $dbR->getAlls('where status_="use" order by createdate desc, createtime desc limit '.$offset.',' . $l_pageSize, '{文档标题},{摘要},{创建日期},{创建时间},{文档发布成html的外网url}');

$html = '';
// 逐项进行处理, 特别是要进行截字
if (!empty($l_rlt)) {
  foreach ($l_rlt as $l_v){
    $l_title = $l_v['{文档标题}'];
    $l_url   = $l_v['{文档发布成html的外网url}'];
    $l_date  = $l_v['{创建日期}'];
    $l_zhy   = $l_v['{摘要}'];
    // 判断是否有继续阅读
    if (false!==strpos($l_zhy,'<span class="moretext"></span>')) {
      $l_more  = ' <a href="'.$l_url.'#more" class="more-link"><span class="moretext"> 继续阅读<img src="http://img3.ni9ni.com/book/kh/2012/0307/content_more.gif" /></span></a>';
    }else {
      $l_more = '';
    }

    $html .= '<div class="entry">
          <h2><a href="'.$l_url.'" title="'.$l_title.'">'.$l_title.'</a></h2>
          <p>' . $l_zhy . $l_more.' </p>
          <p class="info">抢沙发<em class="date">'.$l_date.'</em> <em class="author">来自网络</em> </p>
        </div>
        ';
  }
}

return $html;