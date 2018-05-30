<?php
/**
 * class: DBW.cls.php
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
require_once("DataDriver/db/MDB2_MysqlW.cls.php");

class DBW extends MysqlW
{
  var $table_name = null;

  function __construct($_arr=null, $options=false){
    parent::ConnectW($_arr, $options);
  }
  // for php4
  function DBW($_arr=null, $options=false){
    $this->__construct($_arr, $options);
  }

  function getExistorNot($condition, $assoc=true){
    $sql = "select * from ".cString_SQL::FormatField($this->table_name)." where $condition  limit 1";
    $this->assoc = $assoc;
    return parent::Query_master_select($sql);
  }

  /**
   * 修改表结构, 比较复杂，可以参考phpmyadmin的tbl_addfield.php文件的做法稍后完善????
   *
   * @param array $ar    一维数组，兼容自动抓取，只需要提供多出的字段即可
   * @param array $a_data 至少是一个二维数组，每个字段的详细信息，包括类型、长度、默认值、是否为空等信息
   * @return result
   */
  function alter_table($ar, $a_data=array(), $a_act='ADD'){
    // ALTER TABLE `vendor` ADD `peizhi_m` VARCHAR( 255 ) NULL ;
    // ALTER TABLE `vendor` ADD `peizhi_1` VARCHAR( 255 ) NULL , ADD `peizhi_2` VARCHAR( 255 ) NULL , ADD `peizhi_3` VARCHAR( 255 ) NULL ;
    // ALTER TABLE `vendor` CHANGE `f1` `f1` ENUM( '', 'INT', 'OUT' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

    $sql = " ALTER TABLE  ". cString_SQL::FormatField($this->table_name);

    $i=0;
    foreach ($ar as $l_k=>$l_val){
      if ($i > 0) $sql .= " , "; // 至少第一项以后才能有逗号
      if (is_numeric($l_k)) {
        $val = $l_val;    // 字段英文名
        $l_comment = "";
      }else {
        // 通常 $ar = array('field_1','field_2') 数字索引; 也有可能是 字段英文名=>字段中文名
        $val = $l_k;
        $l_comment = " COMMENT '$l_val'";
      }

      if (is_array($a_data) && array_key_exists($val, $a_data)) {
        // 当有字段的额外信息时
        if (is_array($a_data[$val])) {
          $field_primary = "";  // 地址调用，必须先申明一个，具体有啥用处以后完善之????
          $l_arr = $a_data[$val];
          // 也可能是修改字段，则需要使用到之前的字段英文名
          $l_change = array_key_exists("name_eng_old", $l_arr)? cString_SQL::FormatField($l_arr["name_eng_old"]) . ' ' : "" ;
          $sql .= " $a_act " . $l_change . PMA_Table::generateFieldSpec($l_arr["name_eng"], $l_arr["type"], $l_arr["length"], $l_arr["attribute"], isset($l_arr["collation"]) ? $l_arr["collation"] : '', ("YES"==$l_arr["is_null"])?false:true, $l_arr["default"], isset($l_arr["default_current_timestamp"]), $l_arr["extra"], $l_arr["description"], $field_primary, isset($l_arr["index"]) ? $l_arr["index"] : "id", isset($l_arr["default_orig"]) ? $l_arr["default_orig"] : false);
        }
      }else {
        $sql .= " $a_act ".cString_SQL::FormatField($val)." VARCHAR( 255 ) NULL " . $l_comment;
      }
      $i++;
    }

    return parent::exec($sql);
  }

  function create_db($db_name, $db_charset="utf8"){
    $sql = 'CREATE DATABASE IF NOT EXISTS '.cString_SQL::FormatField($db_name).' DEFAULT CHARACTER SET '.$db_charset.' COLLATE '.$db_charset.'_general_ci';
    return parent::exec($sql);
  }

  function create_table($a_name="tbl_001",$sql_query="`id` int(11) unsigned NOT NULL auto_increment COMMENT '自增ID',`creator` varchar(100) NOT NULL default '0' COMMENT '创建者',`createdate` date NOT NULL default '0000-00-00' COMMENT '创建日期',`createtime` time NOT NULL default '00:00:00' COMMENT '创建时间',`mender` varchar(100) default NULL COMMENT '修改者',`menddate` date default NULL COMMENT '修改日期',`mendtime` time default NULL COMMENT '修改时间',`last_modify` timestamp NOT NULL COMMENT '最近修改时间', PRIMARY KEY  (`id`)",$MySQL_ENGINE="MyISAM",$MySQL_CHARSET="utf8"){
    $MySQL_CHARSET = $GLOBALS['cfg']['db_character'];
    $sql = " CREATE TABLE  ". cString_SQL::FormatField($a_name) . ' (' . $sql_query . ')'."ENGINE=".$MySQL_ENGINE." DEFAULT CHARSET=".$MySQL_CHARSET;
    return parent::exec($sql);
  }

  function rename_table($a_old_name,$a_new_name){
    $sql = "RENAME TABLE ". cString_SQL::FormatField($a_old_name)." TO ". cString_SQL::FormatField($a_new_name);
    return parent::exec($sql);
  }

  function insertOne($data_arr){
    return $this->InsertIntoTbl($this->table_name,$data_arr);
  }

  function updateOne($ar, $condition, $if_addcount = false){
    return $this->UpdateTableArray($this->table_name, $ar, $condition, $if_addcount);
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

    return $this->DeleteData($this->table_name, $condition);
  }

  function LastID(){
    return parent::LastID();
  }

  /**
   * 获取sql语句
   *
   * @return string or bool
   */
  function getSQL(){
    return parent::getSQL();
  }

  function Disconnect(){
    return parent::Disconnect($this->dbo);
  }

  function __destruct(){
    self::Disconnect();
  }
}