<?php
/**
 * 用法:
 D:/php5210/php D:/www/dpa/common/tools/curl.real.php -u "http://1.wanda.cn:81/ktvtopsongs"

 // 携带host信息的, 通过不同ip检查一致性
 D:/php5210/php D:/www/dpa/common/tools/curl.real.php -u "http://10.199.18.79:80" -h "Host:k.dagexing.com"
 D:/php5210/php D:/www/dpa/common/tools/curl.real.php -u "http://10.1.169.84:8043" -h "Host:k.dagexing.com"
 D:/php5210/php D:/www/dpa/common/tools/curl.real.php -u "http://k.dagexing.com"

 */
if ("WIN"===strtoupper(substr(PHP_OS, 0, 3)))
  require_once("D:/www/dpa/configs/system.conf.php");
else {
  $ini_array = parse_ini_file("/etc/sysconfig/network-scripts/ifcfg-eth0");
  if ("220.194.59.222" == trim($ini_array["IPADDR"]))
    require_once("/data0/deve/runtime/configs/system.conf.php");
  else
    require_once("/data0/htdocs/www/dpa/configs/system.conf.php");
}
require_once("common/functions.php");
require_once("common/lib/cURL.cls.php");
/*$a = "\u83b7\u53d6\u641c\u7d22\u7ed3\u679c\u5931\u8d25";
$a = "我的";
echo escape($a,"\u");
exit;*/
//$url = "http://www.sina.com.cn";echo urldecode($url)."\r\n";
//$url = "http://www.sina.com.cn/";
//$url = "http%3A%2F%2Fwww.sina.com.cn%2F";
//echo urldecode($url);exit;
/**
 * cd/usr/home/licun/
 * php curl.php -u "http://i.t.sina.com.cn/wap/sendmessage.php" -d "uid=1257113795&fuid=1769943507&content=hi_fdf" // 内网
 * php curl.php -u "http://i.t.sina.com.cn/wap/sendmessage.php" -d "uid=1257113795&fuid=1263367214&content=hi_fdf" // 外网
 *
 * php /usr/home/licun/curl.php -u "http://i.t.sina.com.cn/wap/sendmessage.php" -d "uid=1257113795&fuid=1263367214&content=hi_fdf" -C "D:/www/config/administrator@1.wanda.txt"
 * php /usr/home/licun/curl.php -u "http://i.t.sina.com.cn/wap/sendmessage.php" -d "uid=1257113795&fuid=1263367214&content=hi_fdf" -C "D:/www/config/admin@ni9ni.com.txt"
 *
 */
define("shuang__quot", '__@@_quot;@@__');
require_once 'Console/Getopt.php';
require_once 'JSON.php';

$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 获取参数列表
$_options = Console_Getopt::getopt($argv, 'u:d:f:j:C:h:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v)
    $_o[str_replace(shuang__quot, '"', $l_v[0])] = str_replace(shuang__quot, '"', $l_v[1]);
}

$l_url = (!empty($_o["u"])) ? $_o["u"] : "";
$l_file = (!empty($_o["f"])) ? $_o["f"] : "";
$l_cookie_file = (!empty($_o["C"])) ? $_o["C"] : "";
$l_json = (!empty($_o["j"])) ? $_o["j"] : "";
$l_post_data = (!empty($_o["d"])) ? $_o["d"] : "";
$add_header = (!empty($_o["h"])) ? $_o["h"] : "";
parse_str($l_post_data, $data_arr);
// 进行数据复原
if (function_exists('get_magic_quotes_gpc') &&
    -1 == version_compare(PHP_VERSION, '5.2.99') &&
    get_magic_quotes_gpc())
  $data_arr     = digui_deep($data_arr, 'stripslashes');

// 上传文件
if (!empty($l_file))
  $data_arr["file"] = $l_file;

if (file_exists($l_cookie_file)) {
  $_COOKIE = cArray::parse_cookiefile($l_cookie_file); // 覆盖$_cookie数组
  foreach ($_COOKIE as $l_name=>$l_v) {
    $l_tmp = array('name'=>$l_name, 'value'=>$l_v);
    $_G_cookie_arr[] = $l_tmp;
  }
}

main($l_url, $data_arr, $l_json, $_G_timeout, GetHeadArr($add_header), $_G_cookie_arr);

function main($l_url, $data_arr, $l_json, $_G_timeout, $addheader, $_G_cookie_arr) {
  $l_h_u = array();
  $l_content = request_cont($l_h_u, $l_url, $data_arr, $_G_timeout, $addheader, $_G_cookie_arr);  // 抓取到了页面内容
  if($l_json){
    if (function_exists('json_decode')) {
      $l_content = json_decode($l_content);
    }else {
    $json = new Services_JSON();
    $l_content = $json->decode($l_content);//unescape($l_content, "\\")
    }
    print_r($l_content);
  }else {
    if ("WIN"===strtoupper(substr(PHP_OS, 0, 3))) {
      echo iconv("UTF-8","GB2312//IGNORE", unescape($l_content, "\\"))."\r\n";
    }else {
      echo unescape($l_content, "\\")."\r\n";
    }
  }
  //return true;
}


function getSign($param, $apiId) {
    //
    if($param) {
        sort($param, 2);
        $string = implode(',', $param);
        return md5($string);
    }
    return md5($apiId);
}

function request_cont(&$l_h_u, $l_url, $a_arr="", $timeout=60, $addheader=array(), $cookie_arr=array()){
  $l_url = trim($l_url);
  // 将该地址也保存到历史url中
  if (!in_array($l_url,$l_h_u)) {
    $l_h_u[] = $l_url;
  }
  //echo date("Y-m-d H:i:s")." ".$l_url."\r\n";
  $req = new cURL();
  $req->_timeout = $timeout;
  if (!empty($cookie_arr)) {
    foreach ($cookie_arr as $cookie){
      $cookie_name  = $cookie["name"];
      $cookie_value = $cookie["value"];
      // $req->addCookie($cookie_name, $cookie_value);
    }
  }

  // 有数据则为post方法
  $method = 'get';
  if (!empty($a_arr))
    $html_content = $req->post($l_url, $a_arr);
  else
    $html_content = $req->get($l_url);

  return $html_content;
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

// Host:k.dagexing.com;User-agent:IE
function GetHeadArr($header_str) {
  $rlt = array();

  if (!empty($header_str)) {
    $l_tmp = explode(';', $header_str);

    foreach ($l_tmp as $tmp_val) {
      if (false !== strpos($tmp_val, ":")) {
        $l_tmp2 = explode(':', $tmp_val);
        $rlt[$l_tmp2[0]] = $l_tmp2[1];
      }
    }
  }

  return $rlt;
}


/**
 *
 *
 * @param unknown $url
 * @param unknown $params
 * @param string $method
 * @param number $timeout
 * @param unknown $header
 * @param unknown $cookies
 * @param string $keepalive
 * @return multitype:mixed unknown

调用示例：

$data = $ret = Util::request($api, $param, $method, $timeout, $header, $cookies, $keepalive);


        if(isset($data['_response']) && Util::is_json($data['_response'])){
            $_response = json_decode($data['_response'],true);
            $_response['HttpCode']=$data['HttpCode'];
//             return $_response;

            if(isset($_response['status']) && $_response['status']){
                return $_response;
            }else{
                return $data;
            }


        }

        return $data;


 */

function request($url, $params = array(), $method = 'GET', $timeout = 30, $header = array(), $cookies = array(), $keepalive=false ) {
    $cookiestr = '';
    if (is_array($cookies) && $cookies) {
        foreach ($cookies as $k => $v) {
            $cookiestr .= $k . '=' . $v . '; ';
        }
    }
    if (empty($header)) {
        $header = array("Accept-Charset: utf-8");
    }
    if(is_json($params)){
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: '.strlen($params);
    }

    $ch = curl_init();



    switch ($method) {
        case 'GET':
            if ($params) {
                $url = $url . '?' . http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
            break;
        case 'POST':
            if(!is_json($params) && is_array($params)){
                $params = http_build_query($params);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);


            break;


        case 'PUT':
            // 保证string
            $params = (is_array($params)) ? http_build_query($params) : $params;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);


            break;


        case 'DELETE':
            // 保证string
            $params = (is_array($params)) ? http_build_query($params) : $params;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);


            break;


        default:
            break;
    }

    if ($GLOBALS['l_debug']) echo 'header:' . "\r\n"; print_r($header); echo "\r\n\r\n";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_COOKIE, $cookiestr);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $_result = array();
    $response = curl_exec($ch);
    var_dump($response);
    //        $response=  json_decode($response, true);

    $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
    $_result['HttpCode']=$httpCode;
    $_result['_response']=$response;
    curl_close($ch);


    return $_result;
}

function is_json($string) {
    if (is_object(@json_decode($string))) {
        return true;
    } else {
        return false;
    }
}
