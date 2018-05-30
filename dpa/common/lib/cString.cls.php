<?php
/**
 * 兼容PHP4的代码
 *
 */
class cString {
  function get_rand_useragent(){
    $l_arr = array(
      "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0)", // IE8
      "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)",
    );
  }
  // 将do=project_list分解成前后两部分
  function getNameActType($a_str){
    $l_rlt[0] = $a_str;
    $l_rlt[1] = "list";

    $pos = strrpos($a_str, "_"); // 最后一个"_"

    // strrpos php4版本前后的判断方法不一样
    if (phpversion()>"4.0.0") {
      if ($pos === false) {
        $found = 0;
      }else {
        $found = 1;
      }
    }else {
      if (is_bool($pos) && !$pos) {
        $found = 0;
      }else {
        $found = 1;
      }
    }

    if ($found) {
      // 有_的时候还要判断是否在list,del,add,edit, 不在则直接返回该字符串
      $l_act = substr($a_str,$pos+1);
      if (in_array(strtolower($l_act), array("list","del","add","edit"))) {
        $l_rlt[0] = substr($a_str,0,$pos);
        $l_rlt[1] = $l_act;
      }
    }

    return $l_rlt;
  }

  // 中文需要转义，没有中文就用英文替代
  function getNameCN($a_key,$table_name_cn){
    if (!empty($table_name_cn)) {
      return convCharacter($table_name_cn,true);
    }else {
      return $a_key;
    }
  }

  function GetAliasByDsnString($dsn,$l_name=""){
    if (is_string($dsn) && ""!=$dsn) {
      $l_b = parse_url($dsn);
      if (isset($l_b["path"]) && "/"!=trim($l_b["path"])) {
        $l_dbname = substr($l_b["path"],1);
      }else {
        $l_dbname = '';
      }
      $l_name = $l_b["host"] .":". (array_key_exists('port',$l_b)?$l_b["port"]:"3306") .":". $l_b["user"] .":". $l_b["pass"].":" . $l_dbname;
    }
    return $l_name;
  }

  /**
   * 根据内容类型, 获取内容
   *
   * @param array $request
   * @param array or string: $l_content
   * @return string
   */
  function GetContType(&$request,$l_content){
    if (!function_exists('json_decode')) {
      require_once('JSON.php');
      $json = new Services_JSON();
    }
    // 数据格式
    if (isset($request["cont_type"])) $cont_type = $request["cont_type"];
    else $cont_type = null;

    switch ($cont_type) {
      case "text":
        // 文本类型的，例如html代码等等，不用添加任何修饰的，$l_content通常都是字符串，不会进行如下的步骤:
        if (is_array($l_content)) {
          if (function_exists('json_decode')) {
            $l_content = json_encode($l_content);
          }else{
            $l_content = $json->encode($l_content);
          }
        }
        break;
      case "js_novar":
        if (function_exists('json_decode')) {
          $l_content = json_encode($l_content);
        }else{
          $l_content = $json->encode($l_content);
        }
        break;
      case "jsonp":
      case "json":
        // 用json相关函数进行json_encod
        /*if (isset($request['t_id']) && isset($l_arr_tbl)) {
        //
        $for_json = format_for_json($l_arr_tbl,"s_shu_xingqiu_id");
        $contentjs = getJson($for_json,"project","name_cn","pingyin_shouzimu");
        }else {
        // 获取所有的project及其所有的表定义表，而不用一个去获取，以后完善此方式????
        $for_json = format_for_json($arr,"p_id");
        $contentjs = getJson($for_json,$name="project","id","name_cn");
        }*/
        if (function_exists('json_decode')) {
          $l_content = json_encode($l_content);
        }else{
          $l_content = $json->encode($l_content);
        }
        break;
      case "js_var":
        // 就是通常的字符串,
        // 74E6 5E64 这样的字符串也会被当做数字, 是科学计数10的6次方，前端js var a=74E6， 也不会报错，因此不存在bug问题
        // 当然无论数字还是字符串，前部为字符串前端js也不会报错，同样能正确判断
        //if (!is_numeric($l_content)) {
        //  $l_content = "'".$l_content."'";
        //}
      default:
        // $l_content = array_values($l_content); 将获得 var js = [1,'succ']; 数组串
        if (function_exists('json_decode')) {
          $l_content = json_encode($l_content);
        }else{
          $l_content = $json->encode($l_content);
        }
        // 前面加上变量符号
        if (isset($request["var_flag"]) && !empty( $request["var_flag"] )){
          $l_var_flag = trim($request["var_flag"]);
        } else {
          $l_var_flag = 'js_data';  // 默认一个变量名称
        }
        $l_content = 'var '.$l_var_flag.'='.$l_content.";";
        break;
    }

    return $l_content;
  }

  /**
   * 用于解析各种mysql的各项成dsn格式的字符串
   *
   * @param array $a_data 外来数组
   * @param array $a_ge 常见的表示主机的数组索引名称
   * @return string
   */
  function parse_mysql_ini($a_data){
    $a_ge=array(
      "mysql_dsn"=>array(),
      "dsn"=>array(),
      "db_host"=>array("host"=>"db_host","port"=>"db_port","user"=>"db_user","pass"=>"db_pass","path"=>"db_name"),
      "mysql_host"=>array("host"=>"mysql_host","port"=>"mysql_port","user"=>"mysql_user","pass"=>"mysql_pass","path"=>"mysql_db_name"),
      "hostspec"=>array("host"=>"hostspec","port"=>"port","user"=>"username","pass"=>"password","path"=>"database")
    );

    $l_dsn = "";

    foreach ($a_ge as $l_n=>$l_v){
      //
      if (array_key_exists($l_n, $a_data)){
        $l_str = $a_data[$l_n];
        if (false!==strpos($l_str,"://")) {
          // 如果是dsn格式的
          $l_dsn = $l_str;
          break;  // 找到就退出
        }else {
          // 需要依据 $a_ge 数组的相应指示进行拼装

          // 有一个特殊情况就是数据库获取到的数据是 db_pwd
          if ("db_host"==$l_n && array_key_exists("db_pwd",$a_data) ) {
            $l_pass = $a_data["db_pwd"];
          }else {
            $l_pass = $a_data[$l_v["pass"]];
          }

          $l_dsn = "mysql://".$a_data[$l_v["user"]].":".$l_pass."@".$a_data[$l_v["host"]].":".$a_data[$l_v["port"]]."/" . ltrim($a_data[$l_v["path"]],"/");
          break;  // 找到就退出
        }
      }
    }

    return $l_dsn;
  }

  // 剔除字符串的行注释，例如sql中的 -- ，然后返回没有注释的新字符串
  // 注意仅仅对于行注释，暂时不针对块注释，以后参考 simple_html_dom的<!-- 也能做块注释????
  function lineDelBySpe($a_str,$a_spe="--"){
    $a_str = preg_replace("/^$a_spe.+/","",$a_str);
    $a_str = preg_replace("/\n$a_spe.+/","\n",$a_str);

    /*if (false!==strpos($a_str,$a_spe)) {
      $l_arr = cArray::explode_str2arr("\n",$a_str,"after");

      if (!empty($l_arr)) {
        $a_str = "";
        foreach ($l_arr as $l_v) {
          if ($a_spe != substr($l_v,0,strlen($a_spe))) {
            $a_str .= $l_v;
          }
        }
      }
    }*/

    return $a_str;
  }

  /**
   * 字符串的数组替换, 修正str_replace 的一个bug
   *
   * 如下，并不能达到替换预期
$letters = array('`aups_f1`',"`aups_f1283`", '`aups_f2`');
$fruit   = array('`aups_f1`',"`aups_f2`",    '`aups_f11`');
$text    = '
  `aups_f1` varchar(200) default NULL,
  `aups_f2` varchar(200) default NULL,
  `aups_f1283` varchar(255) default NULL,';
$output  = str_replace($letters, $fruit, $text);
echo $output;
   *
   * @param array $a_array_combine
   * @param string $a_str
   * @return string
   */
  function str__replace($a_array_combine,$a_str){
    if (is_array($a_array_combine) && !empty($a_array_combine)) {
      //require_once("common/lib/cArray.cls.php");
      $l_arr = cArray::str__replace($a_array_combine,$a_str);
      $a_str = implode("",$l_arr);
    }
    return $a_str;
  }

  function getMysqlDsnStr($dsn){
    return "mysql://".$dsn["db_user"].":".$dsn["db_pwd"]."@".$dsn["db_host"].":".$dsn["db_port"]."/".$dsn["db_name"];
  }

  function getMysqlDsnStrFromMDB2DSN($dsn){
    return "mysql://".$dsn["username"].":".$dsn["password"]."@".$dsn["hostspec"].":".$dsn["port"]."/".$dsn["database"];
  }

  /**
   * php_strip_whitespace的参数必须为文件路径，而此衍生版参数为字符串
   *
   * @param string $a_str
   * @return string
   */
  function php_strip_whitespace_str($a_str){
    // 将字符串存入文件，
    $l_f = 'u'.'nl'."i"."n"."k".'';
    require_once("common/Files.cls.php");
    $files = new Files();
    $l_file_name = "php__strip__whitespace_" . preg_replace('/\W/',"_", utime()).".txt";
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) $l_file_path = "C:/";
    else $l_file_path = "/tmp/";

    $l_php_code_file = $files->overwriteContent($a_str, rtrim($l_file_path,"/")."/".$l_file_name);

    if (file_exists($l_php_code_file)) {
      $a_str = php_strip_whitespace($l_php_code_file);
      $l_f($l_php_code_file);
    }else {
      echo $l_php_code_file ." file not exist! \r\n";  // 需要打日志
    }

    return $a_str;
  }
}

class cString_num
{
  /**
   * 获取数组的维度
   *
   * @param array or string $a_arr
   * @param int $l_DepthCount
   * @return int
   */
  function get_array_depth($a_arr,$l_DepthCount=0) {
    $l_DepthArray = array();

    if (is_array($a_arr)){
      $l_DepthCount++;
      foreach ($a_arr as $l_v){
        $l_DepthArray[]=cString_num::get_array_depth($l_v,$l_DepthCount);
      }
    }else {
      return $l_DepthCount;
    }
    foreach($l_DepthArray as $l_v){
      $l_DepthCount=$l_v>$l_DepthCount?$l_v:$l_DepthCount;
    }
    return $l_DepthCount;
  }

  // 判断字符串是全英文、全中文还是中英混合
  function Check_stringType($str1,$encode="utf-8") {
    $strA = trim($str1);
    $lenA = strlen($strA);
    $lenB = mb_strlen($strA, $encode);
    if ($lenA === $lenB) {
      return 1; //全英文
    } else {
      if ($lenA % $lenB == 0) {
        return 2; //全中文
      } else {
        return 3; //中英混合
      }
    }
  }
}

//
class cString_SQL
{
  // 解析url字符串中显示的字段
  function decodestr2sql($a_str){
    $a_str = trim( urldecode($a_str) );
    if (""==$a_str) return $a_str;

    $l_tmp = explode(",",$a_str);
    $l_str = implode("','",$l_tmp);
    $l_str = "'".$l_str."'";
    return $l_str;
  }

  /**
   * 拼装唯一性条件
   *
   * @param array $data_arr
   * @param array or string: $a_exist_a, 如果是数组，则只单纯的字段而已
   */
  function getUniExist($data_arr,$a_exist_a){
    // 是否存在,拼装唯一性条件
    $a_exist_c = "";
    $i=0;
    if (is_array($a_exist_a)) {
      foreach ($a_exist_a as $l_f){
        if ($i>0) $a_exist_c .= " and ";
        $a_exist_c .= cString_SQL::FormatField($l_f)."=". cString_SQL::FormatValue(convCharacter($data_arr[$l_f],true), 'string');
        $i++;
      }
    }else {
      $a_exist_c = $a_exist_a;
    }
    return $a_exist_c;
  }

  /**
   * 将 array ( 'name_cn'=>'用户库', 'name_eng'=>'userdb') 这样的数组连成字符串
   *
   * @param array $ar
   * @return string
   */
  function FmtFieldValArr2Str(&$ar, $a_sep=" and ", $addcount = false){
    $sql = 1;
    if (!is_array($ar)||empty($ar)) {  // 确保$ar为非空数组
      return $sql;
    }

    // TODO, 支持条数限制 __LIMIT__ , __OFFSET__ 以后放到常量定义里面 ,类似魔术变量一样
    $limit_str = '';
    if (array_key_exists($GLOBALS['cfg']['__OFFSET__'], $ar)) {
        $limit_str = ' LIMIT ' . $ar[$GLOBALS['cfg']['__OFFSET__']] . ', ' . $ar[$GLOBALS['cfg']['__LIMIT__']];
        unset($ar[$GLOBALS['cfg']['__OFFSET__']]);
        unset($ar[$GLOBALS['cfg']['__LIMIT__']]);
    }

    $l_tmp = array();
    if ($addcount) {
      foreach ($ar as $key => $val)
        $l_tmp[] = cString_SQL::FormatField(trim($key)) . "=" . cString_SQL::FormatField(trim($key)) . " + " . $val;
    } else {
      foreach ($ar as $key => $val){
        $l_tmp[] = cString_SQL::FormatField(trim($key)) . "=" . cString_SQL::FormatValue(trim($val), 'string');
      }
    }
    if ($l_tmp) $sql = implode($a_sep, $l_tmp);

    return $sql . ' ' . $limit_str;
  }

  /**
    * 将值转换成SQL可读格式
    * @access public
    * @staitc
    * @return string|integer
    */
  function FormatValue( $theValue, $theType=null ,$slashes='gpc' ) {

    if (empty($theType)) {
      if (is_numeric($theValue)) $theValue = 0 + $theValue;
      $theType = gettype($theValue);
    }

    switch ( $theType ) {
      case "integer":
        $theValue = ($theValue === '') ? "NULL" : intval($theValue) ;
        break;
      case "double":
        $theValue = ($theValue != '') ? "'".doubleval($theValue)."'" : "NULL";
        break;
      case "string":
        if ($theValue != "NOW()") {
          $theValue = cString_SQL::stripslashe_but_single_quote($theValue);
          $theValue = "'" . $theValue . "'";
        }
        break;
      default :
        $theValue = "NULL";
        break;
    }
    return $theValue;
  }
  /**
    * 格式化SQL成员字段名, 字段名中可以有单双引号，但是千万不能有`符号, 采用转义的\`都不行
    * @access public
    * @static
    * @param string $theField
    * @return string
    */
  function FormatField( $theField ){
    $theField = cString_SQL::stripslashe_but_single_quote($theField);
    return '`'. str_replace("`", "",$theField).'`';
  }

  // 只在查询的时候，但如果查询 \% 的时候会出问题，需要加上 addslashes
  function stripslashe_but_single_quote($theValue){
    // magic_quotes_sybase 在php6以后就废弃
    // mysql_escape_string 则将 单引号、双引号 \ 全部转义了，类似 addslashes

    // 字符串复原以后, 还需要对\字符串进行转义。因为mysql的\字符是转义前缀，不转换会丢失\字符
    if (false!==strpos($theValue, '\\'))
      $theValue = str_replace('\\', '\\\\', $theValue);  // 需要将\转义

    // 模糊匹配的还需要转义%
/*    if (false !== strpos($theValue, '%'))
      $theValue = str_replace('%', '\%', $theValue);   // 将%转义

    // 模糊匹配的还需要转义_
    if (false !== strpos($theValue, '%'))
      $theValue = str_replace('_', '\_', $theValue);   // 将%转义
*/
    if (false!==strpos($theValue, "'")) {
      $theValue = str_replace("'", "\'", $theValue);  // 只将单引号转义
    }

    // 查询 \% 的时候需要加上 addslashes
    return $theValue;
  }
}
