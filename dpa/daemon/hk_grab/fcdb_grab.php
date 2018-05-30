<?php
require_once("../../configs/system.conf.php");
require_once("Request.php");
require_once("simple_html_dom.php");
require_once("mod/AutoTblFields.cls.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

define("COMM_TSTRU", "http://www.finchina.com/datadict/dataview/tstrudefine.asp?oid=@oid@");
define("COMM_TBASE", "http://www.finchina.com/datadict/dataview/tbaseinfo.asp?oid=@oid@");
define("COMM_DETAIL","http://www.finchina.com/datadict/dataview/viewfielddetail.asp?id=@id@&oid=@oid@");

$dbR = new DBR();
$dbR->table_name = TABLENAME_PREF."table_def";
$dbW = new DBW();
$dbW->table_name = TABLENAME_PREF."table_def";
$p_id = 2;  // 财汇项目id为 2

$timeout = 30;  // 30 s
$cookie_arr = getFcdbLoginCookie($timeout);

// 登录以后，抓取单个页面
for($i=1;$i<1000;$i++){
  proc_one($dbR,$dbW,$p_id,$i,$cookie_arr,$timeout);
  usleep(100000);
}

//
function proc_one($dbR,$dbW,$p_id,$oid,$cookie_arr,$timeout){
  $l_url = str_replace("@oid@",$oid,COMM_TSTRU);
  $l_h_u = array();
  $html_content_gbk = request_cont($l_h_u, $l_url, $timeout, $cookie_arr);
  //$html_content = file_get_contents("b.htm");

  // 如果 structure 内容存在，则同时请求表基本信息页面
  $sep = "表结构定义";
  if (false!==strpos($html_content_gbk, $sep)) {
    //usleep(100000);  // 发起另一个请求的时候间隔一会儿
    $l_url = str_replace("@oid@",$oid,COMM_TBASE);

    // 用于修正用的抓取数据,后来用于修正数据主键和唯一索引用的
    gettstrudefine($dbR,$dbW,$p_id,$oid,$html_content_gbk, $cookie_arr, $timeout);
  }else {
    echo date("Y-m-d H:i:s")." table_id ".$oid." empty! ".NEW_LINE_CHAR;
  }
}

// 用于修正表定义中的
// $html_content_base_gbk = request_cont($l_url, $cookie_arr, $timeout);


function gettstrudefine(&$dbR, &$dbW, $p_id, $oid, $content, $cookie_arr, $timeout){
  $arr = array();

  $content = iconv("GBK", "UTF-8", $content);

  $l_html = str_get_html($content);
  $l_div = $l_html->find("div",0);  // nei rong
  foreach ($l_div->find("table") as $l_k => $l_tbl){
    // $l_k = 2 表名 以及中文名；3 则具体的是各个字段的信息
    if (2==$l_k) {
      $t_name_eng = $l_tbl->find("td",1)->plaintext;
      $t_name_cn = trim($l_tbl->find("td",3)->plaintext);
      $arr["t_name_eng"]   = trim($t_name_eng);
      $arr["t_name_cn"]   = trim($t_name_cn);

      // 需要插入到数据库中， table_def表中
      if($dbW->getExistorNot("p_id=$p_id and name_eng='".$t_name_eng."'")){
        echo date("Y-m-d H:i:s")." p_id: $p_id  name_eng: $t_name_eng exist in table_def err!".NEW_LINE_CHAR;
      } else {
        // 不存在则插入数据库中
        $t_id = intable_def($dbW, $arr, $oid, TABLENAME_PREF."field_def", TABLENAME_PREF."table_def",$p_id);
        if($t_id!=$oid){
          $l_tbl->clear();unset($l_tbl);  // 清理内存
          echo date("Y-m-d H:i:s")." ".$t_name_eng." insert table_def err!".NEW_LINE_CHAR;
          break;  // 不成功就直接退出该表
        }
      }
      usleep(300);
    }else if (3==$l_k) {
      foreach ($l_tbl->find("tr") as $l_k2 => $l_tr){
        if($l_k2>0){
          // 具体的字段信息
          $f_name_eng = $l_tr->find("td",1)->plaintext;
          // 依据是否有 * 判断其是否为主键, 并结合表基础信息中的主键和唯一索引判断主键和唯一索引
          if (false!==strpos($f_name_eng, "*")) {
            $Null = "NO";
            $f_name_eng = str_replace("*","",$f_name_eng);
          }else {
            $Null = "YES";
          }

          $arr["field"][$l_k2]["name_eng"] = strtolower($f_name_eng);
          $arr["field"][$l_k2]["name_cn"]  = $f_name_cn = trim($l_tr->find("td",2)->plaintext);
          // 需要依据base表定后面的类型，长度等数据

          $arr["field"][$l_k2]["Null"] = $Null;
          $arr["field"][$l_k2]["Key"] = null;
          $arr["field"][$l_k2]["Extra"] = null;

          $arr["field"][$l_k2]["type"] = $f_type = trim($l_tr->find("td",3)->plaintext);
          $arr["field"][$l_k2]["length"] = $f_length = trim($l_tr->find("td",4)->plaintext);
          $arr["field"][$l_k2]["attribute"] = null;
          $arr["field"][$l_k2]["Default"] = null;
          $arr["field"][$l_k2]["f_unit"] = $f_unit = $l_tr->find("td",5)->plaintext;
          $arr["field"][$l_k2]["f_desc"] = $f_desc = $l_tr->find("td",6)->plaintext;

          // 备注也可以抓取一次，获取其枚举列表
          // http://www.finchina.com/datadict/dataview/viewfielddetail.asp?id=3989&oid=183

          // 插入数据库
          infield_def($dbW, $oid, $arr["field"][$l_k2], TABLENAME_PREF."field_def", TABLENAME_PREF."table_def");
          usleep(300);
        }

        $l_tr->clear();unset($l_tr);
      }
    }

    $l_tbl->clear();unset($l_tbl);
  }

  // $l_title = str_replace(array("\r","\n","&nbsp;"),"",trim($l_title));
  // 小对象也要清理下
  $l_div->clear();unset($l_div);
  $l_html->clear();unset($l_html);

  return $arr;
}

function getFcdbLoginCookie($timeout){
  $l_cookie = array();
  // 登录验证
  $req = new HTTP_Request("http://www.finchina.com/datadict/script/check_login.asp");
  $req->_timeout = $timeout;
  $req->setMethod("POST");
  $req->addPostData("url","");
  $req->addPostData("userid","sinacj");
  $req->addPostData("password","sinacj");
  $req->addPostData("Submit1","确定");
  $req->sendRequest();
  $l_cookie = $req->getResponseCookies();

  return $l_cookie;
}

function request_cont(&$l_h_u, $l_url, $timeout=60, $cookie_arr=array()){
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
      $html_content = request_cont_get($l_h_u, $l_hurl, $timeout, $cookie_arr);
    }
  }else {
    $html_content = $req->getResponseBody();  // 抓取到了页面内容
  }

  return $html_content;
}

// 往表定义表中插入数据
function intable_def(&$dbW, $a_arr, $oid, $f_def="field_def", $t_def="table_def",$p_id=0){
  $name_eng = $a_arr["t_name_eng"];   //
  $name_cn  = $a_arr["t_name_cn"];       // 暂时用英文的

  $dbW->table_name = $t_def;
  if($dbW->getExistorNot("name_eng='".$name_eng."' and p_id=$p_id")){
    //continue;
  } else {
    // 不存在则插入数据库中
    $data_arr = array(
      "id"        => $oid,
      "p_id"        => $p_id,
      "field_def_table"=> $f_def,
      "source"    => "grab",
      "creator"     => convCharacter($_SESSION["user"]["id"],true),
      "createdate"    => date("Y-m-d"),
      "createtime"    => date("H:i:s"),
      "name_eng"     => trim($name_eng),
      "name_cn"     => convCharacter($name_cn,true)
    );
    if ($dbW->insertOne($data_arr)) {
      $last_id = $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."insert error!".NEW_LINE_CHAR;
      print_r($data_arr);
    }
  }
  return $last_id;
}

// 往字段定义表中插入数据
function infield_def(&$dbW, $t_id, $a_arr, $f_def="field_def", $t_def="table_def"){
  $dbW->table_name = $f_def;

  $name_eng   = trim($a_arr["name_eng"]);
  $name_cn   = trim($a_arr["name_cn"]);

  if($dbW->getExistorNot("t_id = $t_id  and name_eng='".$name_eng."'")){
    echo date("Y-m-d H:i:s")." t_id: $t_id  name_eng: $name_eng exist in field_def ! ".NEW_LINE_CHAR;
  } else {
    // 不存在则插入数据库中
    $data_arr = array(
      "creator"     => convCharacter($_SESSION["user"]["id"],true),
      "createdate"    => date("Y-m-d"),
      "createtime"    => date("H:i:s"),
      "source"    => "grab",
      "t_id"        => $t_id,
      "name_eng"     => $name_eng,
      "name_cn"     => convCharacter($name_cn,true),
      "is_null"      => $a_arr["Null"],
      "key"        => $a_arr["Key"],
      "extra"        => $a_arr["Extra"],
      "type"        => $a_arr["type"],
      "length"      => $a_arr["length"],
      "attribute"      => $a_arr["attribute"],
      "default"      => convCharacter($a_arr["Default"],true)
    );
    if ($dbW->insertOne($data_arr)) {
      $last_id = $dbW->LastID();
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." insert field_def err!".NEW_LINE_CHAR;
      print_r($data_arr);
    }
  }

  return $last_id;
}

