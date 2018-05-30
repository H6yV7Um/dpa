<?php
/**
 * 上传到服务器上，自动获取数据表和字段定义
 */
require_once("../../configs/system.conf.php");
require_once("mod/AutoTblFields.cls.php");
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

$dbR = new DBR();
$dbW = new DBW();
$p_id = 1;  // wind的项目id是1

$a_data_arr = $a_data_ar2 = array("source"=>"db","creator"=>2);  // 能在外部增加字段的
fill_table($dbR,$dbW,$a_data_arr,"all",TABLENAME_PREF."field_def",TABLENAME_PREF."table_def",$p_id);
fill_field($dbR,$dbW,$a_data_ar2,"all",TABLENAME_PREF."field_def",TABLENAME_PREF."table_def");
