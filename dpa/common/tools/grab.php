<?php
/**
 * 功能描述：
 *    由某些网页开始，将网页内的所有连接地址<a标签也一同抓取入库，同时又进行抓取到的网页入库，如此循环反复进行
 *
 * 以后完善之???? 需要自动创建数据库，按照域名后缀、域名首字母等进行区分的。
 * 此功能更像是spider的功能
 *
 */
ini_set('memory_limit', '200M');

require_once("../configs/system.conf.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
// 抓取和解析用
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");
$_G_cookie_arr   = array();
$_G_timeout   = 30;

// 修改数据库连接信息
__gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],"taigu","taigu");

$level_num = 1;

$dbR = new DBR();
$dbW = new DBW();
$table_name = TABLENAME_PREF."grab";

main($dbR, $dbW, $table_name,$level_num,$_G_timeout, $_G_cookie_arr);

//

  /*
  // 先从文章列表（并不一定局限于文章列表，这里只是一个泛指，就是实际要抓取内容的列表）
  $dbR->table_name = TABLENAME_PREF."grab_article_list";
  // 如果当前进程只有一个，则优先处理doing，即上次异常中止的，为了防止一直循环，按照实际情况进行状态修改
  if (1==getExecProcNum()){
    // 先处理没有处理完成的而被中断的
    $l_doing = $dbR->getAlls("where status_='dong' order by id ");
    if (empty($l_doing)) $l_doing = array();

    // 接着处理in状态的
    $l_in = $dbR->getAlls("where status_='in' order by id ");
    if (empty($l_in)) $l_in = array();

    // 合并起来，然后统一进行逐项处理操作
    $l_rlt = array_merge($l_doing,$l_in);
    unset($l_doing);unset($l_in);
    // 如果以上都没有找到，则获取一条全新的请求进行处理
    if (empty($l_rlt)) {
      //
      $dbR->table_name = TABLENAME_PREF."grab_request";

      // 同样优先处理doing状态的, 一次只处理一条请求，因为可能包含了很多的子请求
      $l_doing = $dbR->GetOne("where status_='doing' order by id ");
      if (empty($l_doing)) {
        // 总要处理一条，那么就找一条in状态的进行处理
        $l_in = $dbR->GetOne("where status_='in' order by id ");
        if (empty($l_in)) $l_in = array();
      }else {
        // 进行相应的处理

      }
    }else {
      // 非空的话，进行处理

    }
  }else {
    // 全新的一个进程，则需要从请求表中额外获得一条进行处理
    $dbR->table_name = TABLENAME_PREF."grab_request";
    $l_in = $dbR->getAlls("where status_='in' order by id ");
  }*/

function main(&$dbR, &$dbW, $table_name,$level_num,$_G_timeout,$_G_cookie_arr=array()){
  // 获取所有的某个级别的链接地址
  $dbR->table_name = $table_name;
  $l_arr = $dbR->getAlls(" where levelnum='$level_num' ", "id,url");

  if (!empty($l_arr)) {
    $n_level_num = $level_num+1;

    foreach ($l_arr as $l_v){
      $l_h_u = array();
      $l_url = $l_v["url"];
      $content = request_cont_get($l_h_u, $l_url,$_G_timeout,$_G_cookie_arr);
      //inser2text();
      $l_all_a = getdetail($dbR, $dbW, $content,$l_url);

      // 记录到数据表中去
      if (!empty($l_all_a)) {
        foreach ($l_all_a as $l_grab_url){
          $data_arr = array(
            "levelnum"=>$n_level_num,
            "url"=>$l_grab_url,
            "createdate"=>date("Y-m-d"),
            "createtime"=>date("H:i:s"),
            "p_id"=>$l_v["id"],
          );
          $dbW->table_name = $table_name;
          inserone($dbW, $data_arr,"url='$l_grab_url'");
          usleep(300);
        }
      }

      usleep(1000);
    }
    // 同时启动下一轮的抓取,level_num加一即可
    main($dbR, $dbW, $table_name,$n_level_num,$_G_timeout,$_G_cookie_arr);
  }
}

// 将整个内容存到另外一张表中去，同时进行分词处理, 涉及到转码问题
function inser2text(){
  //$dbW->table_name = TABLENAME_PREF."grab_text";
  //inserone($dbW, array("id"=>$l_v["url"],"content"=>$content),"id='$l_grab_url'");
}

function inserone(&$dbW, $data_arr,$a_exist_c=""){
  // 是否存在
  if($rlt = $dbW->getExistorNot($a_exist_c)){
    echo date("Y-m-d H:i:s"). " exist! " .$a_exist_c  .NEW_LINE_CHAR;
    if ($rlt["id"]>0) return $rlt["id"];
  } else {
    // 不存在则插入数据库中
    if ($dbW->insertOne($data_arr)) {
      return $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."insert error!".NEW_LINE_CHAR;
      //print_r($data_arr);
      return false;
    }
  }
  return false;
}

// 获取详细信息
function getdetail(&$dbR,&$dbW,$content,$a_url){
  $l_r = array();
  if (false!==strpos($content, "<a")) {
    // 所有有href属性的a标签
    $l_xml = str_get_html($content);

    foreach($l_xml->find("a[href]") as $l_a){
      $l_href = $l_a->href;
      // 清下内存
      $l_a->clear();unset($l_a);


      $l_vali_url = proc_href($l_href,$a_url);
      if ($l_vali_url) {
        $l_r[] = $l_vali_url;
      }else {
        continue;
      }

      unset($l_href);
      unset($l_vali_url);
    }
    // 清下内存
    $l_xml->clear();unset($l_xml);
  }else {
    echo date("Y-m-d H:i:s"). "url: $a_url no_a_tag!"."\r\n";
  }
  return $l_r;
}

/**
 * // 相对url转绝对url，如果是js，则不记录
 *
 * @param string address $a_str
 * @param string $p_url
 * @return string
 */
function proc_href(&$a_str,$p_url){
  $l_str = strtolower(trim($a_str));
  if ("http://"==substr($l_str,0,7) || "https://"==substr($l_str,0,8)) {
    return $a_str;
  }

  // 如果 $a_str 是js 或锚点也不用
  if ("javascript:"==substr($l_str,0,11) || "#"==substr($l_str,0,1)) {
    return null;
  }

  // 相对链接,以后修改下
  return get_abs_url($p_url,$a_str);
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
  $rc = $req->sendRequest();
  //if(PEAR::isError($rc)) echo "HTTP_Request Error : ".$rc->getMessage(); // 通常有网络不通的情况

  $l_header = $req->getResponseHeader();  //
  if(key_exists("location",$l_header)) {
    $l_hurl = get_abs_url($l_url,$l_header["location"]);
    if (in_array($l_hurl,$l_h_u)) {
      // 死循环，需要退出循环
      echo date("Y-m-d H:i:s"). " recicle, exit!";
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
