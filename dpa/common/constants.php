<?php
function getBreenCnName($key, $all_arr=false){
  $arr = array(
    "DD"=>"大豆",  "DP"=>"豆粕",
    "YM"=>"玉米",  "XM"=>"小麦",
    "MH"=>"棉花",  "YZ"=>"油脂",
    "BT"=>"白糖",  "DM"=>"稻米",  "XJ"=>"橡胶"
  );
  if ($all_arr) {
    return $arr;  // 返回整个数组
  }
  if (key_exists($key,$arr)) {
    return $arr[$key];
  }else {
    return $key;
  }
}

function getBTypeCnName($key, $all_arr=false){
  $arr = array(
    "SCPL"=>"市场评论",  "KCSJ"=>"库存数据",
    "XHSC"=>"现货市场",  "CCJG"=>"持仓交割",
    "JGBG"=>"机构报告",  "JGXF"=>"加工消费",
    "SCDT"=>"生产动态",  "JCK"=>"进出口",  "TQZK"=>"天气状况"
  );
  if ($all_arr) {
    return $arr;  // 返回整个数组
  }
  if (key_exists($key,$arr)) {
    return $arr[$key];
  }else {
    return $key;
  }
}

//
function getStaticArticleLink($breed,$b_type,$createdate,$createtime,$aid){
  return $breed."/".$b_type."/".str_replace("-","",$createdate)."/".substr(str_replace(":","",$createtime),0,4).$aid.".html";
}
function getStaticColumnLink($breed,$b_type){
  return $breed."/".$b_type."/index.html";
}
