<?php
/**
 * class: DBR.cls.php
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
if ($GLOBALS['cfg']['IFMDB2'])
  require_once("mod/MDB2_DBR.cls.php");  // 使用MDB2
else if ($GLOBALS['cfg']['IFMYSQLI'])
  require_once("mod/MYSQLI_DBR.cls.php");  // 使用MySQLi
else {
  // 旧方式
require_once("DataDriver/db/MysqlR.cls.php");

class DBR
{
  var $table_name = null;
  var $MysqlR = null;
  var $isconnectionR = false;

  function __construct($_arr=null){
    $this->MysqlR =& new MysqlR();
    if (null == $_arr) {
      $this->MysqlR -> ConnectR();
    }else {
      if (!is_array($_arr)){
        // dsn需要解析 , ???? 需要判断host是否为空
        $b = parse_url($_arr);
        if (!empty($b["host"])) {
          $_arr = array();
          $_arr["db_host"] = $b["host"];
          $_arr["db_port"] = $b["port"];
          $_arr["db_user"] = $b["user"];
          $_arr["db_pwd"]  = $b["pass"];
          $_arr["db_name"] = ltrim($b["path"], " /");
        }
      }
      $this->MysqlR -> ConnectR(trim($_arr["db_name"]), trim($_arr["db_user"]), trim($_arr["db_pwd"]), trim($_arr["db_host"]), trim($_arr["db_port"]));
    }
    $this->isconnectionR = $this->MysqlR->isconnection;
  }
  // for php4
  function DBR($_arr=null){
    $this->__construct($_arr);
  }

  function errorInfo($err=null){
    return $this->MysqlR->errorInfo($err);
  }

  function getExistorNot($id){
    $sql = "select * from `{$this->table_name}` where d_id =$id limit 1";
    return $this->MysqlR -> GetOne($sql);
  }

  function getCountNum($where_limit=""){
    $sql = "select count(1) as num from `{$this->table_name}` $where_limit ";
    $_t = $this->MysqlR -> GetRow($sql);
    return $_t["num"];
  }

  // get all
  function getAlls($where_limit="",$ziduan="*"){
    $sql = "select $ziduan from `{$this->table_name}`  $where_limit ";
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }

  //
  function getCol($ziduan="*",$where_limit=""){
    $sql = "select $ziduan from `{$this->table_name}` $where_limit ";
    return $this->MysqlR -> GetCol($sql);
  }

  function getOne($where_limit="",$ziduan="*",$debug=false){
    $sql = "select $ziduan from `{$this->table_name}` $where_limit ";
    if($debug) echo $sql;
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetRow($sql);
  }

  function query_plan($sql){
    return $this->MysqlR -> GetPlan($sql);
  }

     /**
   * 获取sql语句
   *
   * @return string or bool
   */
  function getSQL(){
    return $this->MysqlR->getSQL();
  }

  function getTblFields($table_name=null){
    if(null==$table_name) $table_name = $this->table_name;
    $sql = "desc `$table_name` "; // show fields from table
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }

  // 依据数据表结构，获取添加数据时候的字段和默认值，分为必选和全字段
  function getInSertArr($table_name=null){
       $arr = $this->getTblFields($table_name);

       $fields_full           = array();
    $fields_bixu           = array();

       // 重新
       if (!empty($arr)) {
         foreach ($arr as $row ) {
           $l_field = trim($row["Field"]);
           $l_v = ("NULL"==$row["Default"])?"":convCharacter($row["Default"],true);

        // 必须的字段单独用数组存放, 排除掉自增和timestamp
        if ("NO"==strtoupper($row["Null"]) && "auto_increment"!=strtolower($row["Extra"]) && "timestamp" != strtolower($row["Type"])) {
          $fields_bixu[$l_field] = $l_v;
        }

           $fields_full[$l_field] = $l_v;
         }
       }

       return array($fields_full,$fields_bixu);
  }

  function getTblFields2($table_name=null){
       $arr = $this->getTblFields();


    $fields_names           = array();
    $fields_types           = array();
    foreach ($arr as $row ) {
        $fields_names[]     = $row["Field"];
        // loic1: set or enum types: slashes single quotes inside options
        if (preg_match('@^(set|enum)\((.+)\)$@i', $row['Type'], $tmp)) {
            $tmp[2]         = substr(preg_replace('@([^,])\'\'@', '\\1\\\'', ',' . $tmp[2]), 1);
            $fields_types[] = $tmp[1] . '(' . str_replace(',', ', ', $tmp[2]) . ')';
        } else {
            $fields_types[] = $row['Type'];
        }
    }
    //
    $fields_options = "";
    $add_type = true;
    $index_type = "BTREE";
    foreach($fields_names AS $key => $val) {
            if ($index_type != 'FULLTEXT'
                || preg_match('@^(varchar|text|tinytext|mediumtext|longtext)@i', $fields_types[$key])) {
                $fields_options .= "\n" . '                '
                     . '<option value="' . htmlspecialchars($val) . '"' . (($val == $selected) ? ' selected="selected"' : '') . '>'
                     . htmlspecialchars($val) . (($add_type) ? ' [' . $fields_types[$key] . ']' : '' ) . '</option>' . "\n";
            }
        }

    return array($fields_names,$fields_types,$fields_options);
  }

  function getTblIndex($table_name=null){
       if(null==$table_name) $table_name = $this->table_name;
    $sql = "show index from `$table_name` ";  // or show keys
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }

  function getTblIndex2($table_name=null){
       $arr = $this->getTblIndex();

       $indexes      = array();
    $prev_index   = '';
       $indexes_info = array();
    $indexes_data = array();
    foreach ($arr as $row){
        if ($row['Key_name'] != $prev_index ){
            $indexes[]  = $row['Key_name'];
            $prev_index = $row['Key_name'];
        }
        $indexes_info[$row['Key_name']]['Sequences'][]     = $row['Seq_in_index'];
        $indexes_info[$row['Key_name']]['Non_unique']      = $row['Non_unique'];
        if (isset($row['Cardinality'])) {
            $indexes_info[$row['Key_name']]['Cardinality'] = $row['Cardinality'];
        }

        $indexes_info[$row['Key_name']]['Comment']         = (isset($row['Comment']))
                                                           ? $row['Comment']
                                                           : '';
        $indexes_info[$row['Key_name']]['Index_type']      = (isset($row['Index_type']))
                                                           ? $row['Index_type']
                                                           : '';

        $indexes_data[$row['Key_name']][$row['Seq_in_index']]['Column_name']  = $row['Column_name'];
        if (isset($row['Sub_part'])) {
            $indexes_data[$row['Key_name']][$row['Seq_in_index']]['Sub_part'] = $row['Sub_part'];
        }
    }
    return array($indexes,$indexes_info,$indexes_data);
  }

  function getDBTbls($db_name=null){
    if(null==$db_name) $db_name = $this->MysqlR->schema;
    $sql = "show table status from `".$db_name."`";
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }
  function SHOW_CREATE_TABLE($table_name=null){
    if(null==$table_name) $table_name = $this->table_name;
    $sql = "SHOW CREATE TABLE `$table_name` ";  // or show keys
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }
  function SHOW_DATABASES(){
    $sql = "SHOW DATABASES";
    $this->MysqlR -> assoc = true;
    return $this->MysqlR -> GetPlan($sql);
  }
  /**
     * 设置当前的 schema
     * @access public
     * @param string  $schema
     * @return boolean
     */
  function SetCurrentSchema($schema){
    $this->MysqlR->SetCurrentSchema($schema);
    $this->schema = $this->GetCurrentSchema(); // 顺便切换一下数据库
    return $this->MysqlR->errorInfo();
  }
  function GetCurrentSchema(){
    return $this->MysqlR->GetCurrentSchema();
  }
  function setCharset($charset = null){
    $this->MysqlR->setCharset($charset);
  }

  function Disconnect(){
    return $this->MysqlR->Disconnect();
  }

  function __destruct(){}
}
}