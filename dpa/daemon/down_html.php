<?php
/**
 * 单页网页抓取到本地文件的方法
 * php down_html.php -u http://bj.ganji.com/ppc/banjia/
 */
ini_set('memory_limit', '200M');

require_once("../configs/system.conf.php");
require_once("common/Files.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;

//
define("COMMON_PATH_HOST","D:/www/dpa/daemon/aaaa");
define("COMMON_PATH_URL", "http://localhost/dpps/daemon/aaaa");

// 获取参数列表
require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'u:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

$url = (!empty($_o["u"])) ? $_o["u"] : "http://www.sina.com.cn/";

saveOne($url, COMMON_PATH_HOST,COMMON_PATH_URL, "index.html", $_G_timeout,$_G_cookie_arr);

function saveOne($url, $tar_path, $tar_url, $f_default="index.html", $_G_timeout=60,$_G_cookie_arr=array() ){
  // 将指定的文件获取到内容
  $l_h_u = array();
  $cont = request_cont_get($l_h_u, trim($url),$_G_timeout,$_G_cookie_arr);
  $cont = trim($cont);

  if (!empty($cont)) {
    $l_shiji_url = $l_h_u[count($l_h_u)-1];    // 是绝对地址
    // 下载相应的js、css、img文件，同时替换其中的链接
    $cont = down_css_js_and_chg($l_shiji_url, $cont, $tar_path, $tar_url,"css",$_G_timeout,$_G_cookie_arr);
    $cont = down_css_js_and_chg($l_shiji_url, $cont, $tar_path, $tar_url,"js",$_G_timeout,$_G_cookie_arr);
    $cont = down_img_and_chg($l_shiji_url, $cont, $tar_path, $tar_url,"img",$_G_timeout,$_G_cookie_arr);

    // 最后保存为文件
    $files = new Files();
    $tar_info = getFilenameByurl($l_shiji_url, $f_default);
    if (""!=$cont) $files->writeContent($cont,$tar_path.$tar_info["path"].$tar_info["filename"]);

    echo date("Y-m-d H:i:s")." url: ".$l_shiji_url." save succ! ".NEW_LINE_CHAR;
  }else {
    echo date("Y-m-d H:i:s")." url: ".$l_shiji_url." content empty! ".NEW_LINE_CHAR;
  }

  return $tar_path.$tar_info["path"].$tar_info["filename"];
}

// 正文中如果有图片，则需要下载，同时更改图片地址
function down_img_and_chg($a_url, $a_str, $tar_path, $tar_url, $a_type="img", $timeout=60,$_G_cookie_arr=array()){
  $l_array = array("img"=>array("img","src","default.jpg"));
  // 下载
  if (false!==strpos($a_str,"<".$l_array[$a_type][0])) {
    $l_html = str_get_html($a_str);
    foreach ($l_html->find($l_array[$a_type][0]."[".$l_array[$a_type][1]."]") as $l_ele){
      $l_href = html_entity_decode(trim($l_ele->$l_array[$a_type][1]));

      // 链接地址可能是相对路径、也可能是绝对路径，都需要保存到本地的相对路径下
      $l_url = get_abs_url( $a_url, $l_href );
      $l_finfo = getFilenameByurl($l_url,"index.html");

      // 下载图片
      $l_h_u = array();
      $cont = request_cont_get($l_h_u, trim($l_url),$timeout,$_G_cookie_arr);
      $cont = trim($cont);

      $l_file_name = genImgFileName($l_url);
      $files = new Files();
      if (""!=$cont) $files->writeContent($cont,$tar_path.$l_finfo["path"].basename($l_file_name));

      // 拼装新地址, 规则同上，文件名也同上用新文件名
      $l_n_url = $tar_url.$l_finfo["path"].basename($l_file_name);
      $l_ele->$l_array[$a_type][1] = $l_n_url;
    }
    $a_str = $l_html->innertext;    // CSS地址被替换过了的

    if(isset($l_ele)) $l_ele->clear();unset($l_ele);  // 释放内存
    $l_html->clear();unset($l_html);  // 清理内存
  }

  return $a_str;
}
// 正文中如果css,js，则需要下载，同时更改地址
function down_css_js_and_chg($a_url, $a_str, $tar_path, $tar_url, $a_type="css", $timeout=60,$_G_cookie_arr=array()){
  $l_array = array("css"=>array("link","href",".css",-4,"style.css"),
           "js"=>array("script","src",".js",-3,"somejs.js"));

  // 下载外链的css
  if (false!==strpos($a_str,"<".$l_array[$a_type][0])) {
    $l_html = str_get_html($a_str);
    foreach ($l_html->find($l_array[$a_type][0]."[".$l_array[$a_type][1]."]") as $l_ele){
      $l_href = html_entity_decode(trim($l_ele->$l_array[$a_type][1]));

      // 链接地址可能是相对路径、也可能是绝对路径，都需要保存到本地的相对路径下
      $l_url = get_abs_url( $a_url, $l_href );
      $l_finfo = getFilenameByurl($l_url,"index.html");

      if ($l_array[$a_type][2]==substr(strtolower($l_finfo["filename"]),$l_array[$a_type][3])) {
        // css文件需要下载, 但是保存的路径需要调整一下
        $l_file_name = saveOne($l_url, $tar_path,$tar_url, $l_array[$a_type][4],$timeout,$_G_cookie_arr);

        // 拼装新地址, 规则同上，文件名也同上用新文件名
        $l_n_url = $tar_url.$l_finfo["path"].basename($l_file_name);
        $l_ele->$l_array[$a_type][1] = $l_n_url."?".urlencode($l_url);  // 带上原地址
      }
    }
    $a_str = $l_html->innertext;    // CSS地址被替换过了的

    if(isset($l_ele)) $l_ele->clear();unset($l_ele);  // 释放内存
    $l_html->clear();unset($l_html);  // 清理内存
  }

  return $a_str;
}

function getFilenameByurl($a_url, $defaul = "index.html"){
  $l_file_name = $defaul;
  $l_path = "";
  $l_host = "";
  if (!empty($a_url)) {
    $l_info = parse_url(trim($a_url));

    if (key_exists("host",$l_info)) {
      $l_host = $l_info["host"];
    }

    if (key_exists("path",$l_info)) {
      if ("/"==substr($l_info["path"],-1)) {
        $l_path = $l_info["path"];
        $l_file_name = $defaul;
      }else {
        $l_path = dirname($l_info["path"]);
        $l_file_name = basename($l_info["path"]);
        $l_path = $l_path."/";
      }
    }else {
      $l_file_name = $defaul;
      $l_path    = "/";
    }
  }

  return array("host"=>$l_host,"path"=>$l_path,"filename"=>$l_file_name);
}

function get_abs_url($p_url,$a_url){
  $l_hurl = trim($a_url);
  $l_base_info = parse_url($p_url);
  $l_hurl_info = parse_url($l_hurl);

  if ( false!==strpos($p_url,"://")) {
  if (!key_exists("scheme", $l_hurl_info)) {
    if ("/"==substr($l_hurl,0,1)) {
      $l_hurl = $l_base_info["scheme"]."://".$l_base_info["host"].$l_hurl;
    }else {
      $l_path = dirname($l_base_info["path"]);
      if(DIRECTORY_SEPARATOR==$l_path){
        $l_path = "/";
      }else {
        // 相对于是没有./ 就直接替换掉
        if ("./"==substr($l_hurl,0,2)) $l_hurl = substr($l_hurl,2);
        $l_path = $l_path."/";
      }
      $l_hurl = $l_base_info["scheme"]."://".$l_base_info["host"].$l_path.$l_hurl;
    }
  }
  }else {
    // 一定出错了
    var_dump($p_url);
    var_dump($a_url);
    return null;
  }
  return $l_hurl;
}

/**
 * 封装的获取请求内容的函数
 *
 * @param 请求的url $l_url
 * @param 设置超时 $timeout
 * @param 数组 $cookie_arr
 * @return string 请求返回的内容，字符串 string
 */
function request_cont_get(&$l_h_u, $l_url, $timeout=60, $cookie_arr=array()){
  $l_url = trim($l_url);
  // 将该地址也保存到历史url中
  if (!in_array($l_url,$l_h_u)) {
    $l_h_u[] = $l_url;
  }

  $req = new HTTP_Request($l_url);
  $req->_timeout = $timeout;
  if (!empty($cookie_arr)) {
    foreach ($cookie_arr as $cookie){
      $cookie_name  = $cookie["name"];
      $cookie_value = $cookie["value"];
      $req->addCookie($cookie_name,$cookie_value);
    }
  }

  $req->setMethod("GET");
  $req->addHeader("User-Agent","Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)");
  $req->addHeader("Referer", $l_url);
  $req->sendRequest();
  $l_header = $req->getResponseHeader();  //
  if(array_key_exists("location",$l_header)) {
    $l_hurl = get_abs_url($l_url,$l_header["location"]);
    if (in_array($l_hurl,$l_h_u)) {
      // 死循环，需要退出循环
      return "";
    }else if (count($l_h_u)>100) {
      // 只允许的深度是100.超过100次的重定向请求将被抛弃
      return "";
    }else {
      $l_h_u[] = trim($l_hurl);    // 将该地址也保存到历史url中
      echo date("Y-m-d H:i:s"). " header.location " .$l_hurl.NEW_LINE_CHAR;  // 便于查看
      $html_content = request_cont_get($l_h_u, $l_hurl, $timeout, $cookie_arr);
    }
  }else {
    $html_content = $req->getResponseBody();  // 抓取到了页面内容
  }

  return $html_content;
}
//
function genImgFileName($a_str, $callback="mc_file"){
  $allow_arr = allowimgtype();
  // 获取文件后缀
  $l_extt = strtolower(getExtt($a_str));
  if (!in_array($l_extt,$allow_arr)) {
    $l_extt = ".jpg";  // 默认为jpg
  }

  // 文件名
  $l_a = parse_url($a_str);
  $l_file_name = basename($l_a["path"]);
  if(!empty($callback)){
    $l_file_name = $callback($l_file_name);
    $l_file_name = substr($l_file_name,0,16);
  }

  return $l_file_name.$l_extt;
}
function allowimgtype(){
   return array(".gif",".jpg",".jpeg",".png",".bmp");  // 允许的图片后缀
}
//
function getExtt($a_str){
  $l_a = parse_url($a_str);
  if (!key_exists("path",$l_a)) {
    $l_a["path"] = $a_str;
  }
  $l_extt = substr(basename($l_a["path"]), strrpos(basename($l_a["path"]),"."));// 最后一个.
  return $l_extt;
}
function mc_file(){
  $a = microtime();
    list($usec, $sec) = explode(" ", $a);
    $b = (float)$usec + (float)$sec;
    return str_replace(".","_",$b);
}
