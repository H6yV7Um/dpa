<?php
/**
 * 获取config目录下指定配置文件的配置信息
 *
 * @param string $config_dir
 * @param string $file_name
 *
 * @since 2009-09-16
 * @return array
 */
function __fetch_config($config_dir,$file_name)
{
  $configs = array();
  $d = dir($config_dir);
  if ($d) {
  while (false !== ($entry = $d->read())) {
    if ($file_name==$entry)
    {
      $tail = substr($entry, -4);
      if($tail == '.ini')
      {
        $configs = parse_ini_file($config_dir."/".$entry, true);
      }
    }
  }
  $d->close();
  }
  return $configs;
}

function convCharacter($str,$in2db=false){
  $new_str = $str;             // 返回的结果
  if (out_character!=db_character) {   // 字符编码相同则不用转换
    $tar_char = $in2db?db_character:out_character;
    if ("utf8"==$tar_char) {
      // 判断是否为utf8编码的，如果是则不用转换，如果不是则需要转换，多一重保险
      if (!is_utf8_encode($str)) {
        $new_str = iconv("GBK","UTF-8//IGNORE",$str);
        $new_str = str_ireplace("charset=utf-8","charset=utf-8",$new_str);
      }
    }else if ("gb2312"==$tar_char || "latin1"==$tar_char) {
      // 判断是否为GB2312编码的，如果是则不用转换，如果不是则需要转换
      if (is_utf8_encode($str)) {
        $new_str = iconv("UTF-8","GBK//IGNORE",$str);
        $new_str = str_ireplace("charset=utf-8","charset=utf-8",$new_str);
      }
    }else {
      echo "只支持 gb2312, utf-8 编码";
      return $new_str;
    }
  }

  return $new_str;
}

// 获取mem信息
function get_simp_elem(&$a_obj, $a_num=true){
  // 循环出所有的字段
  $l_a = array();
  $l_num = count($a_obj->children());

  for($i=0;$i<$l_num;$i++){
    $l_f = $a_obj->children($i)->tag;
    $l_v = $a_obj->children($i)->innertext;

    $l_val=strval($l_v);  // 字符串类型
    if($a_num) {
      // 科学计数法都转为数字了 number_format(strval($l_v)*1.0,2,".","");
      $l_val *= 1.0;
    }

    $l_a[strtolower($l_f)] = $l_val;
  }

  return $l_a;
}

// 获取mem信息
function get_elem(&$a_obj, $a_num=true){
  // 循环出所有的字段
  $l_a = array();

  foreach($a_obj->children() as $l_f=>$l_v){
    $l_val=strval($l_v);  // 字符串类型
    if($a_num) {
      // 科学计数法都转为数字了 number_format(strval($l_v)*1.0,2,".","");
      $l_val *= 1.0;
    }

    $l_a[strtolower($l_f)] = $l_val;

    unset($l_v);
  }

  return $l_a;
}

function getDateTime($a_str,$a_type="date"){
  if ("date"==$a_type) {
    $sep = "-";
  }else if ("time"==$a_type) {
    $sep = ":";
  }else {
    $sep = "";
  }

  return substr($a_str,0,4).$sep.substr($a_str,4,2).$sep.substr($a_str,6,2);
}


function inserone(&$dbW, $data_arr,$a_exist_a){
  // 是否存在,拼装唯一性条件
  $a_exist_c = "";
  $i=0;
  if (is_array($a_exist_a)) {
    foreach ($a_exist_a as $l_f){
      if ($i>0) $a_exist_c .= " and ";
      $a_exist_c .= "`".$l_f."`='".convCharacter($data_arr[$l_f],true)."' ";
      $i++;
    }
  }else {
    $a_exist_c = $a_exist_a;
  }

  if($rlt = $dbW->getExistorNot($a_exist_c)){
    echo date("Y-m-d H:i:s"). " exist! " .$a_exist_c  .NEW_LINE_CHAR;
    if ($rlt["id"]>0) return $rlt["id"];
  } else {
    // 不存在则插入数据库中
    if ($dbW->insertOne($data_arr)) {
      return $dbW->LastID();
    }else {
      // 由于数据库设置了10秒踢连接，因此重新设置一次数据库连接，并重新执行insert操作
      print_r($dbW);
      $l_o_t = $dbW->table_name;
      $dbW = new DBW();
      $dbW->table_name = $l_o_t;
      print_r($dbW);

      echo date("Y-m-d H:i:s") . " database_reconnect  " . NEW_LINE_CHAR;

      if ($dbW->insertOne($data_arr)) {
        return $dbW->LastID();
      }else {
        echo $dbW->getSQL();
        echo date("Y-m-d H:i:s")." "."insert error!".NEW_LINE_CHAR;
        //print_r($data_arr);
        return false;
      }
    }
  }
  return false;
}


function updateRec(&$dbW, $data_arr,$a_exist_a){
  // 是否存在,拼装唯一性条件
  $a_exist_c = "";
  $i=0;
  if (is_array($a_exist_a)) {
    foreach ($a_exist_a as $l_f){
      if ($i>0) $a_exist_c .= " and ";
      $a_exist_c .= "`".$l_f."`='".convCharacter($data_arr[$l_f],true)."' ";
      $i++;
    }
  }else {
    $a_exist_c = $a_exist_a;
  }

  if($rlt = $dbW->getExistorNot($a_exist_c)){
    if ($dbW->updateOne($data_arr, $a_exist_c)) {
      return true;
    }else {
      echo $dbW->getSQL();
      echo date("Y-m-d H:i:s")." "."update error!".NEW_LINE_CHAR;
      //print_r($data_arr);
      return false;
    }
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




function request_cont(&$l_h_u, $l_url, $timeout=60, $cookie_arr=array()){
  $req = new HTTP_Request(trim($l_url));
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
      $l_h_u[] = $l_hurl;    // 将该地址也保存到历史url中
      echo date("Y-m-d H:i:s"). " header.location " .$l_hurl.NEW_LINE_CHAR;  // 便于查看
      $html_content = request_cont($l_hurl, $timeout, $cookie_arr);
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

  return $l_hurl;
}


// 自动将多余的字段放到字段定义表，同时修改表结构创建字段
function autoCreateField(&$dbR, &$dbW, $tablename, $data_arr,$table_name_cn){
  // 先判断表是否存在，不存在则需要创建该表
  $l_tbls = getDBTblsList($dbR->getDBTbls());
  if (!in_array($tablename,$l_tbls)) {
    // 需要创建表, 创建最基本的字段, id,symbol
    $sql_q = "`id` int(11) unsigned NOT NULL auto_increment, `symbol` varchar(50) NOT NULL,`lastmodify` timestamp NOT NULL , PRIMARY KEY  (`id`)";
    if($dbW->create_table($tablename,$sql_q)){
      // subb
      //echo "create table succ!";
    }else {
      echo "create table error!"."\r\n";
      return 1;
    }
  }

  $rlt = 0;  // 返回结果数字
  $TBL_def = TABLENAME_PREF."table_def";
  $FLD_def = TABLENAME_PREF."field_def";
  // 可能需要实时调整表结构,需要根据汽车配置参数增加，默认都是null
  // 选出数据库中拥有的字段
  $old_struct = array();
  $dbR->table_name = $tablename;
  $l_fields = $dbR->getTblFields($tablename);
  if (!empty($l_fields)) {
    foreach ($l_fields as $l_v){
      $old_struct[] = $l_v["Field"];
    }
  }
  if (empty($old_struct)) $old_struct = array("id");  // 设置一个默认的

  // 要入库的所有字段
  $peizhi_ziduan = array_keys($data_arr);

  // 多出的字段，就是新字段相对旧字段多出的字段
  $duoziduan = array();
  foreach ($peizhi_ziduan as $l_ziduan){
    if (!in_array($l_ziduan,$old_struct)) {
      $duoziduan[] = $l_ziduan;
    }
  }

  // 对照一下配置字段是否存在一些现有数据库不存在的字段，如果有则修改表结构
  if (!empty($duoziduan)) {
    // 在表定义中检查是否有此表
    $dbR->table_name = $TBL_def;
    $tdf_arr = $dbR->getOne("where name_eng='" .$tablename. "'");
    if (empty($tdf_arr)) {
      // 插入一条记录，同时返回id
      $name_cn = getNameCN($tablename,$table_name_cn);
      $l_data = array(
        "name_eng"=>$tablename,
        "name_cn"=>$name_cn,
        "field_def_table"=>$FLD_def,
      );
      $dbW->table_name = $TBL_def;
      $t_id = inserone($dbW, $l_data, "name_eng='$tablename'");
      if (!$t_id) {
            $rlt = 1;
            return $rlt;  // 立即返回
          }
    }else {
      $t_id = $tdf_arr["id"];
    }

    // 然后 在字段定义中增加记录
    foreach ($duoziduan as $l_zidu){
      $l_data = array(
        "t_id"=>$t_id,
        "name_eng"=>$l_zidu,
        "name_cn"=>$l_zidu,
      );
      $dbW->table_name = $FLD_def;
      $f_id = inserone($dbW, $l_data, "t_id='$t_id' and name_eng='$l_zidu'");
      if (!$f_id) {
        $rlt = 1;
        return $rlt;
      }
    }

    // 最后修改表结构
    $dbW->table_name = $tablename;
    if($dbW->alter_table($duoziduan)){//成功修改表结构

    }else {
      // 修改表结构失败
      echo " alter table failed !"."\r\n";
      echo $dbW->getSQL()."\r\n";
      $rlt = 1;  // 修改表失败
      return $rlt;
    }
  }else{
    // 没有多的字段
  }
  return $rlt;
}

//
function getDBTblsList($a_arr, $zidu="Name"){
  $l_rlt = array();

  if (!empty($a_arr)) {
    foreach ($a_arr as $l_tbl){
      $l_rlt[] = $l_tbl[$zidu];
    }
  }
  return $l_rlt;
}

//
function getNameCN($a_key,$table_name_cn){

  if (!empty($table_name_cn)) {
    return convCharacter($table_name_cn,true);
  }else {
    return $a_key;
  }
}

function getTblNameCN($tbl_name,$table_name_arr){
  if (!empty($table_name_arr)) {
    if (TABLENAME_PREF==substr($tbl_name,0,strlen(TABLENAME_PREF))) {
      $l_tkey = substr($tbl_name,strlen(TABLENAME_PREF));
    }else {
      $l_tkey = $tbl_name;
    }
    $t_name_cn = $table_name_arr[$tbl_name];
  }else {
    $t_name_cn = $tbl_name;
  }
  return $t_name_cn;
}

function is_utf8_encode($input){
  //$encode = mb_detect_encoding($input, "ASCII,UTF-8,CP936,EUC_CN,BIG-5,EUC-TW");
  //return $encode == "UTF-8" ? true : false;
  $str1 = @iconv("UTF-8", "GBK",  $input);
  $str2 = @iconv("GBK" , "UTF-8", $str1);
  return $input == $str2 ? true : false;
}


function char_preg($a_charset="utf8")
{
  $l_c = array();
  $l_c['utf8']   = "/[\x01-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/e";
  $l_c['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
  $l_c['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
  $l_c['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

  if (key_exists($a_charset,$l_c)) {
    return $l_c[$a_charset];
  }else {
    return $l_c;
  }
}
// 从0开始截取一定长度的字符串, 折算为英文长度, $a_len长度不能为负值，实际应用中不会有这样的
function cn_substr($a_str, $a_len, $a_charset="utf8", $a_suffix="...")
{
  // suffix是英文字符串 ... 这样的或其他英文字符串，不要是中文的
  $l_s_len = ($a_len-strlen($a_suffix));
  $l_s_len = ($l_s_len<0)?0:$l_s_len;  // 后缀长度不能大于 a_len, 实际业务中也不会有这样的情况

  // 匹配所有的单个完整的字符和汉字
  preg_match_all(char_preg($a_charset),$a_str,$l_arr);

  $l_flag  = 0;  // 是否需要回退的标志
  $l_s_num = 0;  // 加后缀后的实际宽度
  $l_total = 0;  // 转换为字符的折算宽度，汉字算2个宽度
  $l_count = 0;  // 多少个字符，汉字算1个字符长度
  foreach($l_arr[0] as $k=> $l_v)
  {
    if(strlen($l_v)==1){
      if ($l_s_num<$l_s_len) {
        $l_s_num += 1;
        $l_count++;
      }
      $l_total += 1;
    }else {
      if ($l_s_num<$l_s_len) {
        if ($l_s_num==$l_s_len-1) $l_flag = 1;
        $l_s_num += 2;
        $l_count++;
      }
      $l_total += 2;
    }
  }
  if ($l_flag) $l_count--;  // 回退1
  // 如果总长度小于等于截取的字符串长度，则不必加后缀
  if ($l_total <= $a_len) $a_suffix = "";

  return join("",array_slice($l_arr[0],0,$l_count)).$a_suffix;
}
