<?php
/**
 * 凤凰网发布系统的登录认证
 * http://pub.ifeng.com:8080/gsps/
 *
 */
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  define("PATH_ROOT","D:/www/dpa");
  define("PATH_PEAR","D:/www/pear");
  define('LOG_PATH',"D:/www");
  define('RES_WEBPATH_PREF',"/");
  define('INI_CONFIGS_PATH',"D:/www/config");
  define("IFWIN",true,true);
  define("db_character","utf8",true);
  define("db_character_contype","utf-8",true);
  define("out_character","utf8",true);
  define("out_character_contype","utf-8",true);

} else {
  define("PATH_ROOT","/data0/htdocs/admin/dpa");
  define("PATH_PEAR","/data0/lib/PEAR");
  define('LOG_PATH',"/");
  define('RES_WEBPATH_PREF',"/");
  define('INI_CONFIGS_PATH',"/data0/runtime/config");
  define("IFWIN",false,true);
  define("db_character","utf8",true);
  define("db_character_contype","utf-8",true);
  define("out_character","utf8",true);
  define("out_character_contype","utf-8",true);
}
ini_set('include_path','.'.PATH_SEPARATOR.PATH_PEAR.PATH_SEPARATOR.PATH_ROOT.PATH_SEPARATOR.LOG_PATH);

//require_once("functions.php");
require_once("HTTP/Request.php");
//require_once("simple_html_dom.php");

$l_url = "http://pub.ifeng.com:8080/cgi-bin/gsps/login3.cgi";
//$l_url = "http://pub.ifeng.com:8080/gsps/";
$l_method = "post";
main($l_url, $l_method);

function main($url,$method="GET"){

  $dataarr = array("txtUserID"=>"liuhz","txtPassword"=>"asd123");
  $req = new HTTP_Request($url);
  $req -> setBasicAuth("publish","h7u5L1");

  if ("POST"==strtoupper($method)) {
    $req->setMethod(strtoupper($method));
    foreach ($dataarr as $key => $val){
      $req->addPostData($key,$val);
    }
  }else if ("GET"==strtoupper($method)) {
    $req->setMethod(strtoupper($method));
  }else {

  }

  $req->sendRequest();
  $cookie_arr = $req->getResponseCookies();

  print_r($cookie_arr);

  print_r($req->getResponseBody());

  exit;
}
