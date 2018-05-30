<?php
header('Content-Type: text/html;charset='.$GLOBALS['cfg']['out_character_contype']);
$request =& $this->request;
$cb = isset($request["cb"]) ? $request["cb"] : "";  // 回调函数
if ( !isset($request["cont_type"]) || "text"==$request["cont_type"]) {
  $request["cont_type"] = "text";  // 没有设置的时候，一定要强制为text, 因为下面的方法中默认的是js串
  echo cString::GetContType($request,$response['html_content']) . $cb;
}else {
  //echo cString::GetContType($request,$response['html_content']) . $cb; // 需要其他模板参数
  echo $response['html_content']; // 需要其他模板参数
}
