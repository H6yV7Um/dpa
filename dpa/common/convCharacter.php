<?php
function convCharacterByStr($str,$tar_char="gb2312"){
  if ("utf8"==$tar_char) {
    // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换，多一重保险
    if (!is_utf8_encode($str)) {
      $str = iconv("GBK","UTF-8//IGNORE",$str);
    }
  }else if ("gb2312"==$tar_char) {
    // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
    if (is_utf8_encode($str)) {
      $str = iconv("UTF-8","GBK//IGNORE",$str);
    }
  }else {
    echo "只支持 gb2312, utf-8 编码";
    return $str;
  }

  return $str;
}

function convCharacter($str,$in2db=false){
  $new_str = $str;             // 返回的结果
  if ($GLOBALS['cfg']['out_character']!=$GLOBALS['cfg']['db_character']) {   // 字符编码相同则不用转换
    $tar_char = $in2db?$GLOBALS['cfg']['db_character']:$GLOBALS['cfg']['out_character'];
    if ("utf8"==$tar_char) {
      // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换，多一重保险
      if (!is_utf8_encode($str)) {
        $new_str = iconv("GBK","UTF-8//IGNORE",$str);
        $new_str = str_ireplace("charset=GB2312","charset=utf-8",$new_str);
      }
    }else if ("gb2312"==$tar_char) {
      // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
      if (is_utf8_encode($str)) {
        $new_str = iconv("UTF-8","GBK//IGNORE",$str);
        $new_str = str_ireplace("charset=utf-8","charset=GB2312",$new_str);
      }
    }else {
      echo "只支持 gb2312, utf-8 编码";
      return $new_str;
    }
  }

  return $new_str;
}

//
function convArrCharacter($a_arr,$in2db=false){
  if (is_array($a_arr) && !empty($a_arr)) {
    foreach ($a_arr as $l_k=>$l_v){
      if (is_array($l_v)) {
        $l_val = convArrCharacter($l_v, $in2db);
      }else {
        $l_val = convCharacter($l_v,$in2db);
      }
      $a_arr[convCharacter($l_k,$in2db)] = $l_val;
    }
  }

  return $a_arr;
}

//
/**
 * 用于对字符编码进行转换, 字符串和数组均可
 *
 * @param string|array $cont  字符串数据均可
 * @param string $source_charact    原数据的字符编码
 * @param string $tar_charact 目标数据的字符编码
 * @return string|array $cont 依然返回同原来一样的数据类型
 */
function convStrOrArrCharacter2other($cont, $source_charact="gb2312", $tar_charact="utf8"){
  // 只允许两种编码
  $l_allow_charact = array("utf-8"=>"UTF-8","utf8"=>"UTF-8","gbk"=>"GBK","gb2312"=>"GBK");

  $tar_charact_lower = strtolower($tar_charact);
  $source_charact_lower = strtolower($source_charact_lower);

  if ( !array_key_exists($tar_charact_lower,$l_allow_charact) || array_key_exists($source_charact_lower,$l_allow_charact) ) {
    echo "目前只对 gb2312, utf-8 互相转化";
    return null;
  }
  if (is_array($cont)) {
    if (!empty($cont)) {
      foreach ($cont as $l_k=>$l_v){
        $cont[convStrOrArrCharacter2other($l_k, $source_charact, $tar_charact)] = convStrOrArrCharacter2other($l_v, $source_charact, $tar_charact);
      }
    }
  }else {
    if ("UTF-8"==$l_allow_charact[$tar_charact_lower]) {
      // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换
      if (!is_utf8_encode($cont)) {
        $cont = iconv("GBK","UTF-8//IGNORE",$cont);
      }
    }else if ("GBK"==$l_allow_charact[$tar_charact_lower]) {
      // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
      if (is_utf8_encode($cont)) {
        $cont = iconv("UTF-8","GBK",$cont);
      }
    }else {
      echo "只支持 gb2312, utf-8 编码";
      return null;
    }
  }

  return $cont;
}
