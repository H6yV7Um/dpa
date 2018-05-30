<?php
/**
 * client url类, 兼容php4版本
 * 调用方法：
 *    $cu = new cURL();
 *    $cu->set_host($vhost);
 *    $content = $cu->get($url);// url可以是代理服务器的ip
 *
 * @version 0.0.1
 * @author chengfeng@ganji.com
 * @since 2011-06-21
 */
class cURL {
  var $headers;
  var $user_agent;
  var $compression;
  var $cookie_file;
  var $proxy;
  var $lastpage;
  var $_timeout = 30;

  function cURL($cookies = TRUE, $cookie = 'cookies.txt', $compression = 'gzip', $proxy = '') {
    $this->lastpage = '';
    $this->headers [] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg';
    //$this->headers[] = 'Connection: Keep-Alive';
    $this->headers [] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
    $this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';
    $this->compression = $compression;
    $this->proxy = $proxy;
    $this->cookies = $cookies;
    if ($this->cookies == TRUE) $this->cookie ( $cookie );
  }
  function set_host($host) {
    $this->headers [] = 'Host: ' . $host;
  }
  function cookie($cookie_file) {
    if (file_exists ( $cookie_file )) {
      $this->cookie_file = $cookie_file;
    } else {
      $fp = fopen ( $cookie_file, 'w' ) or $this->error ( 'The cookie file could not be opened. Make sure this directory has the correct permissions' );
      $this->cookie_file = $cookie_file;
      @fclose ( $fp );
    }
  }
  function get($url, $referer = null) {
    $process = curl_init ( $url );
    $h = $this->headers;
    if (! $referer) {
      $referer = $this->lastpage;
    }
    $this->lastpage = $url;
    if ($referer) {
      $h [] = "REFERER: $referer";
    }
    curl_setopt ( $process, CURLOPT_HTTPHEADER, $h );
    //curl_setopt($process, CURLOPT_HEADER, 0);
    curl_setopt ( $process, CURLOPT_USERAGENT, $this->user_agent );
    if ($this->cookies == TRUE) curl_setopt ( $process, CURLOPT_COOKIEFILE, $this->cookie_file );
    if ($this->cookies == TRUE) curl_setopt ( $process, CURLOPT_COOKIEJAR, $this->cookie_file );
    curl_setopt ( $process, CURLOPT_ENCODING, $this->compression );
    curl_setopt ( $process, CURLOPT_TIMEOUT, $this->_timeout );
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, 'http://chengfeng1:asd123WER@proxy1.wanda.cn:8080');
    curl_setopt ( $process, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $process, CURLOPT_FOLLOWLOCATION, 1 );
    $return = curl_exec ( $process );
    curl_close ( $process );
    return $return;
  }
  function post($url, $data, $referer = null) {
    $process = curl_init($url);
    $h = $this->headers;
    if (!$referer)
      $referer = $this->lastpage;

    $this->lastpage = $url;
    if ($referer)
      $h[] = "REFERER: $referer";

    $fileds_cnt = count($data);
    $fields_string = '';
    foreach($data as $key=>$value)
      $fields_string .= $key.'='.$value.'&' ;
    $fields_string = rtrim($fields_string, ' &');

    curl_setopt($process, CURLOPT_HTTPHEADER, $h);
    curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
    if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
    curl_setopt($process, CURLOPT_ENCODING, $this->compression);
    curl_setopt($process, CURLOPT_TIMEOUT, $this->_timeout);
    if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
    curl_setopt($process, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($process, CURLOPT_POST, $fileds_cnt);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
    $return = curl_exec($process);
    curl_close($process);
    return $return;
  }
  function error($error) {
    //echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
    die ( $error );
  }
}


/*
$vhost = '10.1.100.192';
$url = 'http://10.1.100.192:8090/KtvAppGatewayWeb/member/card';
//$url = 'http://k.dagexing.com';
$data = array('openId'=>'10', 'requestSrc'=>'APP');

$cu = new Curl();
$cu->SetTimeout(90);
//$cu->SetHost($vhost);
$content = $cu->post($url, $data);
//$content = $cu->get($url);
var_dump($content);
*/