[publish]<?php
if (!isset($dbR)) $dbR = $arr['dbR'];
$dbR->table_name = 'aups_t002';        // 正文页

// 总条目数
$sql_where = 'where status_="use"';
$l_itemSum = $dbR->getCountNum($sql_where);

// 页面条目数
if (isset($arr['f_def_duo']['pagesize']['default'])) $l_pageSize = $arr['f_def_duo']['pagesize']['default'] + 0;
$l_pageSize = ($l_pageSize<1)?1:$l_pageSize;  // 防止除数为0的情况

// 总页数, 非cli模式下需要对总页码进行限制
$l_totalpage = ceil($l_itemSum/$l_pageSize);
if ('cli'!=php_sapi_name()) {
  $l_totalpage = ($l_totalpage>100)?100:$l_totalpage;  // 非cli模式下只能生成100页, 即循环100次
}

// 初始的文件名必须保持一致
$l_url = Publish::getUrl($arr,$actionMap,$actionError,$request,$response);  // 获取替换的url
$l_dir_url  = dirname($l_url);

// 页码进行循环, 同时执行发布
$l_old_id = $arr['f_data']['id'];
for ($l_p=1;$l_p<=$l_totalpage;$l_p++){
  $arr['f_data']['id'] = $l_p;  // 页码

  // 文件名路径需要更改. 翻页当前的文件名 1则index.shtml, 从_2开始index_2.shtml
  $l_filename = basename($l_url);  // 循环过程中在不断修改，因此需要不断地初始化
  if ($l_p>1) {
    $l_extt = substr( $l_filename, strrpos($l_filename,".") );
    $l_filename = str_replace($l_extt, "_".$l_p . $l_extt, $l_filename);
  }
  $l_new_url = $l_dir_url.'/'.$l_filename;

  // 获取的内容也需要做一些修改, 只需调用一下相关的字段算法即可，因为翻页、内容列表均在算法中灵活处理
  // 其中的内容需要修改，只需要重新执行一下 文章摘要列表(aups_f119) 的算法即可.
  Parse_Arithmetic::do_arithmetic_by_add_action($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
    Parse_Arithmetic::Int_FillDefDuo($arr, $response, $request);

  Publish::toPublishing($arr,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_data_arr,$l_tmpl_one,$l_new_url,$a_other_arr["if_delete"]);
}
$arr['f_data']['id'] = $l_old_id;  // 恢复原值
