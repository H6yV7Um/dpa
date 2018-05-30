<?php
// 从远程UIBI获取,数据格式为数组array
require_once("configs/UIBI_SIGN.conf.php");

class UIBI {
  function getSign(){
    return $GLOBALS['cfg']['UIBI_SIGN'];
  }

  // 通过用户信息产生sid
  function genSidByUserPass($sign,$n,$k,$user){
    // 产生sid的规则
    $l_ssss = md5("n=".$n).md5("k=".$k).md5("nk=".$n.$k.date("Y")).md5("n=".$n."k=".$k.$sign);
    return md5($l_ssss).$user;
  }

  // 公司认证策略
  function getSidFromUIBIByUserPass($a_uinfo){
    if (!is_array($a_uinfo) || empty($a_uinfo)) {
      return "";
    }

    $sign = UIBI::getSign();  // UIBI 提供，目前使用此

    $password   = $a_uinfo["password"];
    $md5pass    = array_key_exists("md5pass",$a_uinfo)?$a_uinfo["md5pass"]:false;
    $uid      = $a_uinfo["id"];

    if ($uid>0) {
      if ($md5pass) {
        $k = $password;
      }else{
        $k = md5($password);
      }

      $token = UIBI::genSidByUserPass($sign,$uid,$k,$a_uinfo["username"]);

      return $token;
    }else {
      return "";
    }
  }
}
