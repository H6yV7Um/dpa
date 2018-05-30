<?php
/**
 * class: DBW.cls.php
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
if ($GLOBALS['cfg']['IFMDB2'])
  require_once("mod/MDB2_DBW.cls.php");  // 使用MDB2
else if ($GLOBALS['cfg']['IFMYSQLI'])
  require_once("mod/MYSQLI_DBW.cls.php");  // 使用MySQLi
else {
  // 旧方式
require_once("DataDriver/db/MysqlW.cls.php");

class DBW
{
  var $table_name = null;
  var $MysqlW = null;
  var $isconnectionW = false;

  function __construct($_arr=null){
    $this->MysqlW =& new MysqlW();
    if (null == $_arr) {
      $this->MysqlW -> ConnectW();
    }else {
      $this->MysqlW -> ConnectW(trim($_arr["db_name"]), trim($_arr["db_user"]), trim($_arr["db_pwd"]), trim($_arr["db_host"]), trim($_arr["db_port"]));
    }
    $this->isconnectionW = $this->MysqlW->isconnection;
  }
  // for php4
  function DBW($_arr=null){
    $this->__construct($_arr);
  }

  function errorInfo($err=null){
    return $this->MysqlW->errorInfo($err);
  }

  function Query($sql){
    if (!$this->MysqlW->connectionW) {
      $this->MysqlW =& new MysqlW();
      $this->MysqlW -> ConnectW();
    }
    return $this->MysqlW->Query($sql);
  }

  function getExistorNot($condition){
    $sql = "select * from `{$this->table_name}` where $condition  limit 1";
    return $this->MysqlW -> Query_master_select($sql);
  }

  function alter_table($ar, $a_data=array(), $a_act='ADD'){
    // ALTER TABLE `vendor` ADD `peizhi_m` VARCHAR( 255 ) NULL ;
    // ALTER TABLE `vendor` ADD `peizhi_1` VARCHAR( 255 ) NULL , ADD `peizhi_2` VARCHAR( 255 ) NULL , ADD `peizhi_3` VARCHAR( 255 ) NULL ;

    if (!$this->MysqlW->connectionW) {
      $this->MysqlW =& new MysqlW();
      $this->MysqlW -> ConnectW();
    }

    $sql = " ALTER TABLE  `". $this->table_name."`";

    $i=0;
    foreach ($ar as $val){
      if ($i > 0) $sql .= " , "; // 至少第一项以后才能有逗号

      if (is_array($a_data) && array_key_exists($val, $a_data)) {
        // 当有字段的额外信息时
        if (is_array($a_data[$val])) {
          $field_primary = "";  // 地址调用，必须先申明一个，具体有啥用处以后完善之????
          $l_arr = $a_data[$val];
          // 也可能是修改字段，则需要使用到之前的字段英文名
          $l_change = array_key_exists("name_eng_old", $l_arr)? '`'.$l_arr["name_eng_old"] . '` ' : "" ;
          $sql .= " $a_act " . $l_change . PMA_Table::generateFieldSpec($l_arr["name_eng"], $l_arr["type"], $l_arr["length"], $l_arr["attribute"], isset($l_arr["collation"]) ? $l_arr["collation"] : '', ("YES"==$l_arr["is_null"])?false:true, $l_arr["default"], isset($l_arr["default_current_timestamp"]), $l_arr["extra"], $l_arr["description"], $field_primary, isset($l_arr["index"]) ? $l_arr["index"] : "id", isset($l_arr["default_orig"]) ? $l_arr["default_orig"] : false);
        }
      }else {
      $sql .= " $a_act `$val` VARCHAR( 255 ) NULL ";
      }
      $i++;
    }

    return $this->MysqlW->Query($sql);
  }

  function create_db($db_name, $db_charset="utf8"){
    if (!$this->MysqlW->connectionW) {
      $this->MysqlW =& new MysqlW();
      $this->MysqlW -> ConnectW();
    }

    $sql = 'CREATE DATABASE IF NOT EXISTS `'.$db_name.'` DEFAULT CHARACTER SET '.$db_charset.' COLLATE '.$db_charset.'_general_ci';
    return $this->MysqlW->Query($sql);
  }

  function create_table($a_name="tbl_001",$sql_query="`id` int(11) unsigned NOT NULL auto_increment,`lastmodify` timestamp NOT NULL , PRIMARY KEY  (`id`)",$MySQL_ENGINE="MyISAM",$MySQL_CHARSET="utf8"){
    if (!$this->MysqlW->connectionW) {
      $this->MysqlW =& new MysqlW();
      $this->MysqlW -> ConnectW();
    }
    if( isset($GLOBALS['cfg']['db_character']) ) $MySQL_CHARSET = $GLOBALS['cfg']['db_character'];
    $sql = " CREATE TABLE  ". '`' . $a_name . '`' . ' (' . $sql_query . ')'."ENGINE=".$MySQL_ENGINE." DEFAULT CHARSET=".$MySQL_CHARSET;

    return $this->MysqlW->Query($sql);
  }

  function insertOne($data_arr){
    return $this->MysqlW->InsertIntoTbl($this->table_name,$data_arr);
  }

  function updateOne($ar, $condition, $if_addcount = false){
    return $this->MysqlW->UpdateTableArray($this->table_name, $ar, $condition, $if_addcount);
  }

  function delOne($arr,$id_ziduan="d_id"){
    $condition = " where ";
    if (!empty($arr)) {
      foreach ($arr as $key => $val){
        // uid是否也需要引号
        if ($id_ziduan==$key) {
          $condition .= $key."=".$val." and "; // id 不需要单引号
        } else {
          $condition .= $key."='".$val."' and ";
        }
      }
      if ("and"==substr(rtrim($condition),-3)) { // 截取后三个字符
        $condition = substr(rtrim($condition),0,-3);
      }
    }

    return $this->MysqlW->DeleteData($this->table_name, $condition);
  }

  function LastID(){
    return $this->MysqlW->LastID();
  }

  /**
   * 获取sql语句
   *
   * @return string or bool
   */
  function getSQL(){
    return $this->MysqlW->getSQL();
  }

  function setDatabase($db_name){
    return $this->MysqlW->setDatabase($db_name);
  }
  /**
     * 设置当前的 schema
     * @access public
     * @param string  $schema
     * @return boolean
     */
  function SetCurrentSchema($schema){
    return $this->setDatabase($schema);
  }
  function GetCurrentSchema(){
    return $this->MysqlW->GetCurrentSchema();
  }

  function setCharset($charset = null){
    $this->MysqlW->setCharset($charset);
  }

  function Disconnect(){
    return $this->MysqlW->Disconnect();
  }

  function __destruct(){}
}
}