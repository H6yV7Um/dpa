-- 初始化中需要insert的数据有 dpa整个数据库和表，
-- CREATE DATABASE IF NOT EXISTS `dpa`; 接着需要有 dpps_user 和 dpps_project 两张表以及初始的数据， 而其他的则可以自动完成。
-- 1) 来自dpa.sql
-- 2) 接着填充表定义表和字段定义表, 语句自动生成并填充
-- 3) insert 一些初始值以及更新字段算法, 语句来自 init_insert.sql

-- 表的初始数据 begin
-- 向用户表dpps_user中添加初始用户robot和admin,grab 向项目表dpps_project中添加初始数据
-- INSERT INTO `dpps_user` (`id`, `g_id`, `parent_id`, `username`, `pwd`, `nickname`, `mobile`, `telephone`, `email`, `fixed`, `locked`, `stat_priv`, `admin`, `expired`, `description`, `badPwdStr`, `lastPwdChange`, `isIPLimit`, `if_super`) VALUES (1, 0, 0, 'robot', '21232f297a57a5a743894a0e4a801fc3', '后台机器人', '18601357705', '18601357705', 'cheng@ni9ni.com', 'T', 'F', '02', 'T', '0000-00-00 00:00:00', 'Robot,alias,nickname', '', '1228703174', 'F', '1'),(2, 0, 1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '超级管理员', '18601357705', '18601357705', 'cheng@ni9ni.com', 'T', 'F', '02', 'T', '0000-00-00 00:00:00', 'Administrator', '', '1228703174', 'F', '1'),(3, 0, 1, 'grab', '21232f297a57a5a743894a0e4a801fc3', '抓取机器人', '18601357705', '18601357705', 'cheng@ni9ni.com', 'T', 'F', '02', 'T', '0000-00-00 00:00:00', 'Grab', '', '1228703174', 'T', '0');
-- INSERT INTO `dpps_project` (`id`, `name_cn`, `type`, `parent_id`, `db_host`, `db_name`, `db_port`, `db_user`, `db_pwd`, `db_timeout`, `db_sock`, `if_use_slave`, `slave_db_host`, `slave_db_name`, `slave_db_port`, `slave_db_user`, `slave_db_pwd`, `slave_db_timeout`, `slave_db_sock`, `if_use_slave2`, `slave2_db_host`, `slave2_db_name`, `slave2_db_port`, `slave2_db_user`, `slave2_db_pwd`, `slave2_db_timeout`, `slave2_db_sock`, `if_daemon_pub`, `daemon_pub_cgi`, `status_`, `search_order`, `list_order`, `if_hide`, `description`, `host_id`, `res_pub_map`) VALUES (1, '通用发布系统', 'SYSTEM', 0, 'localhost', 'dpa', 3307, 'root', 'eswine_db1', 0, NULL, 'n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'n', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, 'use', 0, 0, 'no', NULL, 0, 0);

-- 表的初始数据 end

-- 向用户权限的三张表中添加初始数据 ，后续再完善之


-- 需要增加字段的表

-- 1) 项目表 dpps_project
--    SELECT * FROM `dpps_field_def`  where name_eng='parent_id' and t_id = (select id from dpps_table_def where name_eng='dpps_project')
--   a) parent_id字段修改，sql语句:
update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(id,"-",name_cn),id from dpps_project order by id
[add_select]
-请选择-,0' where name_eng='parent_id' and t_id = (select id from dpps_table_def where name_eng='dpps_project');

update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(id,"-",name_cn),id from dpps_project order by id
[add_select]
-所在项目,默认此项-,0' where name_eng='table_field_xiangmu_id' and t_id = (select id from dpps_table_def where name_eng='dpps_project');

--   b) 更新数据类型 :
-- SELECT * FROM `dpps_field_def`  where name_eng='db_pwd' and t_id = (select id from dpps_table_def where name_eng='dpps_project')
update  dpps_field_def set `f_type`='Form::Password' where name_eng='db_pwd' and t_id = (select id from dpps_table_def where name_eng='dpps_project')

--   c) 列表的时候，显示哪些字段，以及显示顺序需要单独创建字段


-- 2) 模板设计表 dpps_tmpl_design，用于后台管理的时候的呈现


-- 3) 表定义表  dpps_table_def


-- 4) 字段定义表  dpps_field_def


-- 5) 用户表  dpps_user
--   a) 更新数据类型 :
update  dpps_field_def set `f_type`='Form::Password', `arithmetic`='[PwdGen]
md5' where name_eng='pwd' and t_id = (select id from dpps_table_def where name_eng='dpps_user')

update  dpps_field_def set `f_type`='Form::CodeResult', `arithmetic`='[html]
$_SESSION["user"]["id"]' where name_eng='parent_id' and t_id = (select id from dpps_table_def where name_eng='dpps_user')

-- 6) 用户文档权限表  dpps_user_doc_privileges
--   a) 更新数据类型 :
update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(username,"-",nickname),id from dpps_user order by id' where name_eng='u_id' and t_id = (select id from dpps_table_def where name_eng='dpps_user_doc_privileges')

update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(name_cn,"-",id),id from dpps_project order by id' where name_eng='suoshuxiangmu_id' and t_id in (select id from dpps_table_def where name_eng in ('dpps_user_doc_privileges','dpps_user_proj_privileges','dpps_user_tempdef_privileges'))

update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[code]<?php
			$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];
			$dbR = new DBR($l_name0_r);

			// 首先获取所属的项目，
			if (isset($a_arr["default_over"]["suoshuxiangmu_id"])) {
				$l_pro_id = $a_arr["default_over"]["suoshuxiangmu_id"]["value"];
			}else if (is_array($a_arr["f_info"]["suoshuxiangmu_id"]["length"])) {
				// 没有设置的项目的前端默认保持了一致，
				$l_pro_id = current($a_arr["f_info"]["suoshuxiangmu_id"]["length"]) + 0;
			}
			if (isset($l_pro_id) && $l_pro_id>0) {
				$dbR->table_name = TABLENAME_PREF."project";
				$p_arr = $dbR->getOne(" where id = ".$l_pro_id);
				if (empty($p_arr)) {
					return null;
				}
				$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
				$dbR->dbo = &DBO('''', $dsn);
			}

			$dbR->table_name = TABLENAME_PREF."table_def";
			$l_rlt = $dbR->getAlls("where `name_eng` NOT LIKE ''%table_def'' and `name_eng` NOT LIKE ''%field_def''", "CONCAT(name_cn,''-'',id),id");
			return $l_rlt;
[add_select]
-请选择-,0' where name_eng='suoshubiao_id' and t_id in (select id from dpps_table_def where name_eng in ('dpps_user_doc_privileges','dpps_user_tempdef_privileges'))

update  dpps_table_def set `js_verify_add_edit`='TRUE',`js_code_add_edit`='<script type="text/javascript" src="http://img3.ni9ni.com/js/jquery.min.js"></script>
<script type="text/javascript">
function browserDetect(){
		var sUA=navigator.userAgent.toLowerCase();
		var sIE=sUA.indexOf("msie");
		var sOpera=sUA.indexOf("opera");
		var sMoz=sUA.indexOf("gecko");
		if(sOpera!=-1)return "opera";
		if(sIE!=-1){
			nIeVer=parseFloat(sUA.substr(sIE+5));
			if(nIeVer>=6)return "ie6";
			else if(nIeVer>=5.5)return "ie55";
			else if(nIeVer>=5)return "ie5";
		}
		if(sMoz!=-1)return "moz";
		return "other";
}


function jsRemoveItemFromSelect(objSelect) {
	var ie_or = browserDetect();
	var ie_or2 = ie_or.substring(0,2);
	if (ie_or2=="ie") {
		objSelect.options.length = 0;
    }else{
		objSelect.innerHTML = "";
	}
	return true;
}

function jsAddItemToSelect(objSelect, objItemText, objItemValue) {
    //判断是否存在
    if (jsSelectIsExitItem(objSelect, objItemValue)) {
        //alert("该Item的Value值已经存在");
    } else {
        var varItem = new Option(objItemText, objItemValue);
        objSelect.options.add(varItem);
    }
}

function jsSelectIsExitItem(objSelect, objItemValue) {
    var isExit = false;
    for (var i = 0; i < objSelect.options.length; i++) {
        if (objSelect.options[i].value == objItemValue) {
            isExit = true;
            break;
        }
    }
    return isExit;
}

$(function(){

$("#suoshuxiangmu_id").change(function (){
	var a_pid=$("#suoshuxiangmu_id").val();

	// 如果数据已经存在，则无需请求，如果不存在，则需要请求一次
 	// 拼装请求url，
	var l_url = "/dpa/main.php";
	var l_ = Math.round((Math.random()) * 100000000);
	var var_flag = "json_project";

    $.ajax({
	   	url: l_url,
	   	//cache:false,
		data:"_r=" + l_ + "&do=GetTemplateListJS&cont_type=json&var_flag=" + var_flag + "&p_id=" + a_pid + "&_r="+l_,
	   	scriptCharset:"utf-8",

		complete:function () {
	   		eval("var l_data = " + var_flag + ";");


			if(a_pid>=0){
				// 先清空
				if(jsRemoveItemFromSelect(document.getElementById("suoshubiao_id"))){
					// 然后再赋值
					if(0==a_pid){
						jsAddItemToSelect(document.getElementById("suoshubiao_id"), "-请选择-", 0);
					}else {
						for(var s_id in l_data[a_pid]){
							jsAddItemToSelect(document.getElementById("suoshubiao_id"), l_data[a_pid][s_id],s_id);
						}
					}
				}
			}
			//$("#content").text( l_data.title ).css({"color":"red","font-size":"12px"});
		},
	   	//success:function(){alert("success");},

		dataType: "script", //script能自己删除节点
	   	type: "GET"
	});

});
})
</script>' where name_eng in ('dpps_user_doc_privileges','dpps_user_tempdef_privileges') and p_id = 1


update  dpps_table_def set `js_verify_add_edit`='TRUE',`js_code_add_edit`='<script type="text/javascript" src="http://img3.ni9ni.com/js/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
$("input[type=''password'']").after(''<input type="text" id="db_pwd_mingwen" name="db_pwd_mingwen" size="20" style="display:none" /><input type="checkbox" id="db_pwd_check" name="db_pwd_check" /><span>显示明文</span>'');
$("#db_pwd_mingwen").val( $("input[type=''password'']").val() );
$("input[type=''password'']").keyup(function (){
	$("#db_pwd_mingwen").val( $("input[type=''password'']").val() );
});
$("#db_pwd_mingwen").keyup(function (){
	$("input[type=''password'']").val( $("#db_pwd_mingwen").val() );
});

$("#db_pwd_check").click(function (){
    //alert( document.getElementById("db_pwd_check").checked );
	//if( $("#db_pwd_check").attr("checked")==true ){};
	$("input[type=''password'']").toggle();
	$("#db_pwd_mingwen").toggle();
});
})
</script>
' where name_eng in ('dpps_project','dpps_user') and p_id = 1



-- 7) 用户文档权限表  dpps_user_proj_privileges
--   a) 更新数据类型 :
update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(username,"-",nickname),id from dpps_user order by id' where name_eng='u_id' and t_id = (select id from dpps_table_def where name_eng='dpps_user_proj_privileges')

-- 8) 用户文档权限表  dpps_user_tempdef_privileges
--   a) 更新数据类型 :
update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(username,"-",nickname),id from dpps_user order by id' where name_eng='u_id' and t_id = (select id from dpps_table_def where name_eng='dpps_user_tempdef_privileges')



-- ALTER TABLE  `dpps_field_def` CHANGE  `length`  `length` VARCHAR( 600 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '255' COMMENT  '长度';

-- ALTER TABLE  `dpps_field_def` CHANGE  `f_type`  `f_type` ENUM(  'Form::CodeResult',  'Form::TextField',  'Form::Date',  'Form::DateTime',  'Form::Password',  'Form::TextArea',  'Form::HTMLEditor', 'Form::Select',  'Form::DB_Select',  'Form::DB_RadioGroup',  'Form::ImageFile',  'Form::File',  'Application::SQLResult',  'Application::PostInPage',  'Application::CrossPublish',  'Application::CodeResult' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'Form::TextField' COMMENT  '字段的算法类型';

-- UPDATE  `dpps_field_def` SET  `length` =  '''Form::CodeResult'',''Form::TextField'',''Form::Date'',''Form::DateTime'',''Form::Password'',''Form::TextArea'',''Form::HTMLEditor'',''Form::Select'',''Form::DB_Select'',''Form::DB_RadioGroup'',''Form::ImageFile'',''Form::File'',''Application::SQLResult'',''Application::PostInPage'',''Application::CrossPublish'',''Application::CodeResult''' WHERE  `t_id` = 12 and name_eng='f_type';



ALTER TABLE  `dpps_user` ADD  `status_` ENUM(  'use',  'stop',  'test',  'del',  'scrap' ) NOT NULL DEFAULT  'use' COMMENT  '状态, 使用、停用等';

ALTER TABLE  `dpps_user_proj_privileges` ADD  `status_` ENUM(  'use',  'stop',  'test',  'del',  'scrap' ) NOT NULL DEFAULT  'use' COMMENT  '状态, 使用、停用等';

ALTER TABLE  `dpps_user_tempdef_privileges` ADD  `status_` ENUM(  'use',  'stop',  'test',  'del',  'scrap' ) NOT NULL DEFAULT  'use' COMMENT  '状态, 使用、停用等';

ALTER TABLE  `dpps_user_doc_privileges` ADD  `status_` ENUM(  'use',  'stop',  'test',  'del',  'scrap' ) NOT NULL DEFAULT  'use' COMMENT  '状态, 使用、停用等';


