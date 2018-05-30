<?php
/**
 * 用法:
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://1.wanda.cn:81/ktvtopsongs" -x "http://chengfeng1:123456@proxy1.wanda.cn:8080"
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "https://graph.qq.com/user/get_vip_rich_info?oauth_consumer_key=100343998&access_token=1EFF6E082D7BA7BD3D50641B95B3FE6A&openid=8E95401719FC40339C16FE96B26877CA&format=json" -x "http://chengfeng1:asd123WER@proxy1.wanda.cn:8080"
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://www.sina.com.cn" -x "http://chengfeng1:asd123WER@proxy1.wanda.cn:8080"  -- 可以

 // 携带host信息的, 通过不同ip检查一致性
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://10.199.18.79:80" -h "Host=k.dagexing.com" -s prod
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://10.1.169.84:8043" -h "Host=k.dagexing.com"
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://k.dagexing.com"

 // 文件上传
 D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://1.wanda.cn:81/uploadaudio" -d "version=1&uid=30668&duration=12&sid=114295&score=a23571607606a34288737363a52463308618" -f "D:/baoda_pengliyuan.mp3" -F audio


D:/php5210/php D:/www/dpa/common/tools/curl.php -u "https://github.com/laravel/framework/blob/5.3/README.md" -x "http://chengfeng1:asd123WER@proxy1.wanda.cn:8080"

D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://www.sohu.com/" -x "http://chengfeng1:asd123WER@proxy1.wanda.cn:8080"

php D:/www/dpa/common/tools/curl.php -u "https://github.com/laravel/framework.git" -x "http://chengfeng1:asd123WER@proxy1.wanda.cn:8080"

 */
error_reporting(E_ALL & ~ (E_DEPRECATED | E_STRICT | E_NOTICE)); // PHP5.3兼容问题, PHP5.4严格性

if ("WIN"===strtoupper(substr(PHP_OS, 0, 3)))
  require_once("D:/www/dpa/configs/system.conf.php");
else if ("DAR"===strtoupper(substr(PHP_OS, 0, 3))) {
    require_once("/Users/cf/svn_dev/dpa/configs/system.conf.php");
    //
}
else {
  $ini_array = parse_ini_file("/etc/sysconfig/network-scripts/ifcfg-eth1");
  if ("139.196.176.221" == trim($ini_array["IPADDR"]))
    require_once("/data0/deve/runtime/configs/system.conf.php");
  else
    require_once("/data0/htdocs/www/dpa/configs/system.conf.php");
}
require_once("common/functions.php");
require_once("common/lib/cArray.cls.php");
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
// 抓取和解析用
require_once("HTTP/Request.php");

$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 获取参数列表
$_options = Console_Getopt::getopt($argv, 'u:d:f:j:C:h:x:F:b:s:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v)
    $_o[str_replace(shuang__quot, '"', $l_v[0])] = str_replace(shuang__quot, '"', $l_v[1]);
}

$l_url = (!empty($_o["u"])) ? $_o["u"] : "";
$l_file = (!empty($_o["f"])) ? $_o["f"] : "";
$upload_file_name = (!empty($_o["F"])) ? $_o["F"] : "file";
$l_cookie_file = (!empty($_o["C"])) ? $_o["C"] : "";
$l_json = (!empty($_o["j"])) ? $_o["j"] : "";
$l_post_data = (!empty($_o["d"])) ? $_o["d"] : "";
$add_header = (!empty($_o["h"])) ? $_o["h"] : "";
$proxy_str = (!empty($_o["x"])) ? $_o["x"] : "";
$GLOBALS['server_env'] = (isset($_o['s'])) ? $_o['s'] : 'test';
$GLOBALS['l_debug'] = (!empty($_o['b'])) ? $_o['b'] : 0;


parse_str($l_post_data, $data_arr);
if($add_header && !is_array($add_header))
    $add_header = GetHeadArr($add_header);

// 进行数据复原
if (function_exists('get_magic_quotes_gpc') &&
    -1 == version_compare(PHP_VERSION, '5.2.99') &&
    get_magic_quotes_gpc())
        $data_arr     = digui_deep($data_arr, 'stripslashes');

// 为微博卡券所做的修改
if (false !== strpos($l_url, 'weibocoupon/granttickets')) {
  $ts = time();
  $appid = 'weibo';
  $token = 'db54942cff7001cd127140feeca18852';
  $sign = md5($token . $ts . $appid);
  $l_post_data = $l_post_data . "&ts=$ts&sign=$sign";
}


// 为CGV影城所做的修改
if (false !== strpos($l_url, 'http://otpstg.cgv.com.cn/') || false !== strpos($l_url, 'http://otp.cgv.com.cn/')) {
    if ($GLOBALS['l_debug']) echo 'url:' . $l_url . "\r\n\r\n";
    //$username  = 'XFF009';
    // $macKey = '5db6fd85fdd178a01f9f20b4f707eacb';    // 第三方平台/系统商提供

    if ('prod' == $GLOBALS['server_env']) {
        $username  = 'FFWUSER';
        $macKey = 'ac1c115e48676b0ea26863396abcca67';
        $l_url = str_replace('http://otpstg.cgv.com.cn/', 'http://otp.cgv.com.cn/', $l_url);
    } else {
        $username  = 'TPP004';
        $macKey = 'DhGhBxRkmbnaviYmyj9VoAMtwtMqsYc9';    // 第三方平台/系统商提供
        $l_url = str_replace('http://otp.cgv.com.cn/', 'http://otpstg.cgv.com.cn/', $l_url);
    }
    //$curr_time = time();// + 3600 * 8;
    if ($GLOBALS['l_debug']) echo $GLOBALS['server_env'] . ' url:' . $l_url . "\r\n\r\n";

    $params = array();
    // 对参数排序，可能是post，也可能是get
    if ($data_arr) {
        // POST请求
        $params = $data_arr;
    } else if (false !== strpos($l_url, '?')) {
        // GET请求
        $get_arr = parse_url($l_url);
        if ($get_arr && isset($get_arr['query']) && $get_arr['query']) {
            parse_str($get_arr['query'], $params);
        }
    }
    if ($GLOBALS['l_debug']) '' . print_r($params);


    //require_once 'D:/www/tests/aes256/Movie_Common_Aes.php';
    require_once 'D:/www/wanda_git/ffan/movie_platform/app/Lib/Encrypt/Aes256Cgv.php';
    require_once 'D:/www/wanda_git/ffan/movie_platform/app/Lib/Encrypt/CommonAes.php';
    $aes256 = new Lib\Encrypt\Aes256Cgv($macKey);
    //$aes256 = new Aes256Cgv($macKey);
    //$aes256 = new Aes256();
    // 别人提供 begin
    if ($params) {
        $tmpParam = array();
        foreach($params as $k=>$v) {
            // 手机号字段 用户手机号（需要AES256加密）, 传入时需要urlencode .

            if ('mobile' == $k) {
                // 计算签名的时候，手机号不需要加密，直接用原始数据
                //echo 'mobile:' . $v . ' aes256:' . aes::encrypt($v, $macKey) . "\r\n";
                //$v = urlencode(aes::encrypt($v, $macKey));
                //$v = urlencode($aes256->encode($v));
                $v = $aes256->encrypt($v);
                echo "Aes256Cgv::encrypt - Encrypt String : $v \r\n\r\n";
                //$v = urlencode($aes256->simple_encrypt($v));
                //$v = urlencode($aes256->encrypt($v));
                if (isset($data_arr[$k])) $data_arr[$k] = $v;
            }
            if ('seat' == $k) {
                //计算签名的时候，座位不需要encode，直接用原始数据
                //$v = array('3118220105#03#01','3118220105#03#02');
                //$v = array('3118220105%2303%2301','3118220105%2303%2302');
                $v = str_replace("'", '"', $v);
                $v = json_decode($v);
                //print_r($v);
                $c = array();
                foreach ($v as $l_val) {
                    $c[] = urlencode($l_val); //计算签名的时候，座位不需要urlencode，直接用原始数据
                }
                //print_r($c);
                $v = json_encode($c);
                echo 'seat:' . $v . "\r\n";
                if (isset($data_arr[$k])) $data_arr[$k] = $v;
            }
            $tmpParam[] = strtolower($k).'='.$v;
        }
        sort($tmpParam, SORT_STRING); // 对数组值进行升序排序
        $queryString = implode(',', $tmpParam);
    } else {
        $queryString = $username; // 无参数时，用用户ID
    }
    $content_md5 = md5($queryString);

    //print_r($queryString);
    //exit;
    //
    setlocale(LC_TIME, 'en_US');
    $date = gmdate("D, d M Y H:i:s", time())." GMT";
    // 验证
    $ori_signature = "Date: ".$date."\n"."Content-Md5: ". $content_md5;
    $hash_signature = base64_encode(hash_hmac('sha256', utf8_encode($ori_signature), utf8_encode($macKey), true));
    $authorization = 'hmac username="'.$username.'", algorithm="hmac-sha256", headers="Date Content-Md5", signature="'.$hash_signature.'"';
    // 别人提供 end

// -- end


 //print_r($data_arr);

    //设置必须的请求头信息
    $l_add_header_arr = array(
        //'Content-Type' => 'application/json',
        //'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',

        'Authorization'=> $authorization,
        'Content-Md5'  => $content_md5,
        'Date'         => $date,
        'Accept-Encoding' => 'gzip,deflate',
        //'X-Date'         => $date,
    );

    if ($GLOBALS['l_debug']) print_r($l_add_header_arr);

    if ($add_header)
        $add_header = array_merge($l_add_header_arr, $add_header);
    else
        $add_header = $l_add_header_arr;
    //$add_header = '';
    //$l_url = $l_url . '?' . http_build_query($params); // 部分接口需要get方法，如果参数值有#特殊符号导致参数缺失，需要用http_build_query处理
    //echo "\r\nurl: " . $l_url . "\r\n";
}

// 为百度糯米合作所做的修改
if (false !== strpos($l_url, 'http://movieapi.baidu.com/') || false !== strpos($l_url, 'http://movieapi-dev.baidu.com/')) {
    $sfrom     = 'buyticket';  // 第三方平台/系统商提供
    $secretKey = '1234567';    // 第三方平台/系统商提供

    // 所有接口都要对发送的参数进行加密。
    // 参与签名的字段：接口文档中约定的参数除sign以外都参与签名,即sfrom以及请求接口的参数

    // 计算sign值
    $params = $data_arr;       // 所有请求参数需参与签名
    if ($GLOBALS['l_debug']) print_r($params);
    $params['sfrom'] = $sfrom; // 外加sfrom参与签名
    if ($GLOBALS['l_debug']) print_r($params);
    ksort($params);            // 将参与签名的参数按升序排序

    $queryArr = array();
    foreach($params as $key => $value){
        $queryArr[] = $key.'='. $value;
    }
    $queryString = implode('&', $queryArr);//把所有的参数使用&凭借成querystring
    //$queryString = urldecode(http_build_query($params));// 拼接queryString，把所有的参数使用&拼接成querystring，注意:不能进行url转义，

    if ($GLOBALS['l_debug']) echo "\r\nurl: " . $l_url . "\r\n";
    if ($GLOBALS['l_debug']) echo "\r\nqueryString: " . $queryString . "\r\n";
    if ($GLOBALS['l_debug']) echo "\r\nsecretKey: " . $secretKey . "\r\n";
    $sign = md5(md5($secretKey . $queryString) . $secretKey);//进行32的md5计算,md5和querystring进行一次拼接计算md5值之后，再和key进行一次拼接计算md5得到的sign值，
    if ($GLOBALS['l_debug']) echo "\r\nsign: " . $sign . "\r\n";

    // 最后把sign值放在querystring之后进行请求
    $l_post_data = $l_post_data . "&sign=$sign"; // 请求参数无需排序，TODO
    $data_arr['sign'] = $sign; // url上如果有#，可能会被截断，需要注册到post数组中去
    if ($GLOBALS['l_debug']) echo "\r\n" . $l_post_data . "\r\n";

    $l_url = $l_url . '?' . http_build_query($params) . "&sign=$sign"; // 部分接口需要get方法，如果参数值有#特殊符号导致参数缺失，需要用http_build_query处理
    //echo "\r\nurl: " . $l_url . "\r\n";
}

// 上传文件
if (!empty($l_file)) {
  $data_arr[$upload_file_name] = $l_file;
  if (false !== strpos($l_post_data, 'upload_type=content') || false !== strpos($l_url, 'upload_type=content'))
    $data_arr['image'] = file_get_contents($l_file); // TODO del 临时修改 图片上传支持二进制流, D:/php5210/php D:/www/dpa/common/tools/curl.php -u "http://1.wanhui.cn:8093/uploadpicture?upload_type=content" -f "D:/test.jpg"
}

if (file_exists($l_cookie_file)) {
  $_COOKIE = cArray::parse_cookiefile($l_cookie_file); // 覆盖$_cookie数组
  foreach ($_COOKIE as $l_name=>$l_v) {
    $l_tmp = array('name'=>$l_name, 'value'=>$l_v);
    $_G_cookie_arr[] = $l_tmp;
  }
}

main($l_url, $data_arr, $l_json, $_G_timeout, $add_header, $_G_cookie_arr, $proxy_str);

function main($l_url, $data_arr, $l_json, $_G_timeout, $addheader, $_G_cookie_arr, $proxy_str){
  $l_h_u = array();
  $l_content = request_cont($l_h_u, $l_url, $data_arr, $_G_timeout, $addheader, $_G_cookie_arr, $proxy_str);  // 抓取到了页面内容
  if (strlen($l_content)<=10) {
      echo "response Body begin: \r\n";
      var_dump($l_content);
      echo "response Body end: \r\n";
  }
  if($l_json){
    if (function_exists('json_decode')) {
      $l_content = json_decode($l_content);
    }else {
      $json = new Services_JSON();
      $l_content = $json->decode($l_content);//unescape($l_content, "\\")
    }
    // 对数组的每个项使用字符编码转换
    if ("WIN"===strtoupper(substr(PHP_OS, 0, 3))) {
        $l_content = cArray::array_map_recursive('iconv2gb2312', $l_content);
    }
    print_r($l_content);
  } else {
    if ("WIN"===strtoupper(substr(PHP_OS, 0, 3))) {
      echo iconv("UTF-8","GB2312//IGNORE", unescape($l_content, "\\"))."\r\n";
    }else {
      echo unescape($l_content, "\\")."\r\n";
    }
  }
}

function request_cont(&$l_h_u, $l_url, $a_arr="", $timeout=60, $addheader=array(), $cookie_arr=array(), $proxy_dsn=''){
  $l_url = trim($l_url);
  // 将该地址也保存到历史url中
  if (!in_array($l_url,$l_h_u)) {
    $l_h_u[] = $l_url;
  }
  //echo date("Y-m-d H:i:s")." ".$l_url."\r\n";
  $req = new HTTP_Request($l_url);

  // 设置代理
  if ($proxy_dsn) {
    $proxy_arr = parse_url($proxy_dsn);
    if (isset($proxy_arr['host'])) {
      if (!isset($proxy_arr['port']))
        $proxy_arr['port'] = 80;
      if (isset($proxy_arr['user']))
          $req->setProxy($proxy_arr['host'], $proxy_arr['port'], $proxy_arr['user'], $proxy_arr['pass']);
      else
          $req->setProxy($proxy_arr['host'], $proxy_arr['port']);
    }
  }

/*$cookie_arr = array(
  array('name'=>'PHPSESSID', 'value'=>'l9s8ns0jc7cmmgiis6c1a3hj46'),
  array('name'=>'SESSIONID', 'value'=>'2a4d9761073b4dc59832c248ca664028'),
);*/

  $req->_timeout = $timeout;
  if (!empty($cookie_arr)) {
    foreach ($cookie_arr as $cookie){
      $cookie_name  = $cookie["name"];
      $cookie_value = $cookie["value"];
      $req->addCookie($cookie_name,$cookie_value);
    }
  }

  // 有数据则为post方法
  if (!empty($a_arr)) {
    $req->setMethod("POST");
    foreach ($a_arr as $l_k=>$l_v){
      if ($GLOBALS['upload_file_name'] == $l_k) {
        $req->addFile($l_k, $l_v);  // 上传文件, 多个文件怎么办？TODO
      }else {
        $req->addPostData($l_k, $l_v);
      }
    }
  } else
    $req->setMethod("GET");

  $req->addHeader("User-Agent","Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)");
  $req->addHeader("Referer", $l_url);
  if (!empty($addheader)) {
    foreach ($addheader as $head_key => $head_value)
      $req->addHeader($head_key, $head_value);
  }
  $rc = $req->sendRequest();
  //if(PEAR::isError($rc)) echo "HTTP_Request Error : ".$rc->getMessage(); // 通常有网络不通的情况

  // 返回的状态码-200，401，404等等
  //$l_response_code = $req->getResponseCode();
  //echo 'response code:'."\r\n"; var_dump($l_response_code);

  $l_cookie = $req->getResponseCookies();  //
  //echo 'response cookie:'."\r\n"; var_dump($l_cookie);
  $l_header = $req->getResponseHeader();  //
  //echo 'response header:'."\r\n";print_r($l_header);
  if(key_exists("location",$l_header)) {
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
      // cookie 和 header均需要重新携带，如果header过来已经携带了这些cookie信息的话
      // print_r($l_cookie);
      $html_content = request_cont($l_h_u, $l_hurl, $a_arr, $timeout, $addheader, $cookie_arr, $proxy_dsn);
    }
  }else {
    $html_content = $req->getResponseBody();  // 抓取到了页面内容
  }
  //echo 'response body:'."\r\n";print_r($html_content);
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

// Host=k.dagexing.com&User-agent=IE
function GetHeadArr($header_str) {
  $rlt = array();

  if (!empty($header_str)) {
    $l_tmp = explode('&', $header_str);

    foreach ($l_tmp as $tmp_val) {
      if (false !== strpos($tmp_val, "=")) {
        $l_tmp2 = explode('=', $tmp_val);
        $rlt[$l_tmp2[0]] = $l_tmp2[1];
      }
    }
  }

  return $rlt;
}

function iconv2gb2312 ($str) {
  return iconv("UTF-8", "GBK//IGNORE", $str);
}

