<?php
function request_cont(&$l_h_u, $l_url, $a_arr="", $timeout=60, $cookie_arr=array(), $referer="", $a_BasicAuth=array()){
  $l_url = trim($l_url);
  // 将该地址也保存到历史url中
  if (!in_array($l_url,$l_h_u)) {
    $l_h_u[] = $l_url;
  }

  $req = new HTTP_Request($l_url);
  if (is_array($a_BasicAuth) && !empty($a_BasicAuth) && isset($a_BasicAuth["BasicAuth"])) {
    $req->setBasicAuth($a_BasicAuth["BasicAuth"],$a_BasicAuth["BasicAuthpass"]);
  }

  $req->_timeout = $timeout;
  if (!empty($cookie_arr)) {
    foreach ($cookie_arr as $cookie){
      $cookie_name  = $cookie["name"];
      $cookie_value = $cookie["value"];
      $req->addCookie($cookie_name,$cookie_value);
    }
  }

  // 外部没有指定 referer的时候采用其域名作为 referer
  if (false===strpos($referer,"://")) {
    $l_parse = parse_url($l_url);
    $referer = $l_parse["scheme"]."://".trim($l_parse["host"]," /")."/";
  }
  // 有数据则为post方法
  if (!empty($a_arr)) {
    $req->setMethod("POST");
    foreach ($a_arr as $l_k=>$l_v){
      if ("file"==$l_k) {
        $req->addFile($l_k, $l_v);  // 上传文件
      }else {
        $req->addPostData($l_k, $l_v);
      }
    }
  }else {
    $req->setMethod("GET");
  }
  $user_agent = array(
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)",
    "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20100101 Firefox/15.0.1",
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; TencentTraveler ; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; (R1 1.5); Media Center PC 3.0; .NET CLR 1.0.3705; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
  );
  $l_rand = mt_rand(1, count($user_agent));
  $req->addHeader("User-Agent", $user_agent[$l_rand-1]);
  $req->addHeader("Referer", $l_url);
  $rc = $req->sendRequest();
  //if(PEAR::isError($rc)) echo "HTTP_Request Error : ".$rc->getMessage(); // 通常有网络不通的情况

  $l_cookie_arr = $req->getResponseCookies();
  if (!empty($l_cookie_arr)){
    $cookie_arr = $l_cookie_arr;
  }

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
      $html_content = request_cont($l_h_u, $l_hurl, $a_arr, $timeout, $cookie_arr);
    }
  }else {
    $html_content = $req->getResponseBody();  // 抓取到了页面内容
  }

  return $html_content;
}


function get_abs_url($p_url,$a_url){
  $l_hurl = trim($a_url);
  $l_base_info = parse_url($p_url);
  $l_hurl_info = parse_url($l_hurl);

  // 父级必须有域名
  if ( false!==strpos($p_url,"://")) {
    if (!array_key_exists("scheme", $l_hurl_info)) {
      if ("/"==substr($l_hurl,0,1)) {
        $l_hurl = $l_base_info["scheme"]."://".$l_base_info["host"].$l_hurl;
      }else {
        if (array_key_exists("path",$l_base_info)) {
          $l_path = dirname($l_base_info["path"]);
        }else{
          $l_path = "";
        }

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
