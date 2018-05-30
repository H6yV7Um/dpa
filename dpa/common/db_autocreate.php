<?php
class db_autocreate{
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
}
