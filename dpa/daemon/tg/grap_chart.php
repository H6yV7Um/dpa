<?php
/**
 * 抓取合作方提供的小图，存储到我们的服务器上
 *
 */
require_once("sina/start.php");
require_once("HTTP/Request.php");

// 抓取图片到本地
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
  define("PATH_IMG_SAVE","D:/www/xml/www.sinaimg.cn/cj");
}else {
  define("PATH_IMG_SAVE","/data1/rsync_data_center/www.sinaimg.cn/cj");
}

// 目前就抓这三张，其实依据规律可以都抓下来，但用不上
$_Img_arr = array(
  "http://210.5.28.134/cqs/charts/10-%23001-233-134-CN.gif",
  "http://210.5.28.134/cqs/charts/10-%23026-233-134-CN.gif",
  "http://210.5.28.134/cqs/charts/01-WTX%26-233-134-CN.gif",
);

$path = "tw_zq";  // 相对路径

main($_Img_arr,$path);
sleep(2);

// 图片分发
$fin_img = &Dist('finance_image');
assert_error($fin_img);
$sina3pj1_dir = DistQueueFile($fin_img, "/".$path."/");  // 需要后缀 /
assert_error($sina3pj1_dir);

$rcc = DistSend($fin_img);
assert_error($rcc);

function main($_Img_arr,$path="tw_zq"){
  //
  foreach ($_Img_arr as $l_url){
    // 先抓取内容
    $l_cont = request_cont($l_url);

    if(""!=$l_cont){
      // 保存路径和文件名
      $l_file_name = basename($l_url);
      $l_file_name = str_replace("%","_",$l_file_name);
      $l_tar_path = PATH_IMG_SAVE."/".$path."/".$l_file_name;
      writeContent( $l_cont, $l_tar_path, $mode='w' );
      // 如果抓到，则同步出去


    }else {
      echo date("Y-m-d H:i:s"). " image $l_url empty!"."\r\n";
    }
  }
}
/**
 * 封装的获取请求内容的函数
 *
 * @param 请求的url $l_url
 * @param 设置超时 $timeout
 * @param 数组 $cookie_arr
 * @return string 请求返回的内容，字符串 string
 */
function request_cont($l_url, $timeout=30, $cookie_arr=array()){
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
  $req->sendRequest();
  $html_content = $req->getResponseBody();  // 抓取到了页面内容

  return $html_content;
}


//建立目地文件夹
function createdir($dir='')
{
  if (!is_dir($dir)){
    // 该参数本身不是目录 或者目录不存在的时候
    $temp = explode('/',$dir);
    $cur_dir = '';
    for($i=0;$i<count($temp);$i++)
    {
      $cur_dir .= $temp[$i].'/';
      if (!is_dir($cur_dir))
      {
        @mkdir($cur_dir,0775);
      }
    }
  }
}

function writeContent( $content, $filePath, $mode='w' ){
    createdir(dirname($filePath));
    if ( !file_exists($filePath) || is_writable($filePath) ) {

      if (!$handle = @fopen($filePath, $mode)) {
        return "can't open file $filePath";
      }

      if (!fwrite($handle, $content)) {
        return "cann't write into file $filePath";
      }

      fclose($handle);

      return '';

    } else {
      return "file $filePath isn't writable";
    }
}
