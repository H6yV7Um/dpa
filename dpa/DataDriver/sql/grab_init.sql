-- 第一张表 dpps_grab_article_list 通常是自动填充的，无需人工添加内容，如有需要再写相关算法
-- 第一张表 end

-- 第三张表 dpps_grab_request 需要更新的字段属性
-- a) parent_id 字段修改，sql语句: 
update `dpps_field_def` set `f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(id,"id-级别",levelnum),id from dpps_grab_request where levelnum<=1 order by id
[add_select]
-无-,0' where name_eng='parent_id' and t_id = (select id from dpps_table_def where name_eng='dpps_grab_request');

update `dpps_field_def` set `f_type`='Form::DB_Select', `arithmetic`='[project]
name=通用发布系统
[query]
sql=select CONCAT(name_cn,"-",id),id from dpps_project where `type` in ("CMS") order by id' where name_eng='p_id_to' and t_id = (select id from dpps_table_def where name_eng='dpps_grab_request');

update `dpps_field_def` set `f_type`='Form::DB_Select', `arithmetic`='[code]
			$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];			
			$dbR = new DBR($l_name0_r);
			
			// 首先获取所属的项目，
			if (isset($a_arr["default_over"]["p_id_to"])) {
				$l_pro_id = $a_arr["default_over"]["p_id_to"]["value"];
			}else if (is_array($a_arr["f_info"]["p_id_to"]["length"])) {
				// 没有设置的项目的前端默认保持了一致，
				$l_pro_id = current($a_arr["f_info"]["p_id_to"]["length"]) + 0;
			}
			if (isset($l_pro_id) && $l_pro_id>0) {
				$dbR -> table_name = TABLENAME_PREF."project";
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
-请选择-,0' where name_eng='t_id_to' and `t_id` = (select id from dpps_table_def where name_eng='dpps_grab_request');

update `dpps_field_def` set `f_type`='Form::Select',`arithmetic`='1,1
2,2
3,3' where `name_eng`='levelnum' and `t_id` = (select id from dpps_table_def where name_eng='dpps_grab_request');

-- 表级别的 js 代码
update  `dpps_table_def` set `js_verify_add_edit`='TRUE',`js_code_add_edit`='<script type="text/javascript" src="http://img3.ni9ni.com/js/jquery.min.js"></script>
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

$("#p_id_to").change(function (){
	var a_pid=$("#p_id_to").val();
	
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
				if(jsRemoveItemFromSelect(document.getElementById("t_id_to"))){
					// 然后再赋值
					if(0==a_pid){
						jsAddItemToSelect(document.getElementById("t_id_to"), "-请选择-", 0);
					}else {
						for(var s_id in l_data[a_pid]){
							jsAddItemToSelect(document.getElementById("t_id_to"), l_data[a_pid][s_id],s_id);
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
</script>' where name_eng like '%grab_request'; 
-- 第三张表 end


-- 1) 创建时间统一更新
update `dpps_field_def` set `list_order`='1' where `name_eng`='id';
update `dpps_field_def` set `list_order`='1051' where `name_eng`='creator';
update `dpps_field_def` set `list_order`='1052' where `name_eng`='createdate';
update `dpps_field_def` set `list_order`='1053' where `name_eng`='createtime';
update `dpps_field_def` set `list_order`='1054' where `name_eng`='mender';
update `dpps_field_def` set `list_order`='1055' where `name_eng`='menddate';
update `dpps_field_def` set `list_order`='1056' where `name_eng`='mendtime';
update `dpps_field_def` set `list_order`='1060' where `name_eng`='expireddate';
update `dpps_field_def` set `list_order`='1061' where `name_eng`='audited';
update `dpps_field_def` set `list_order`='1062' where `name_eng`='status_' and t_id not in (select id from dpps_table_def where name_eng in ('dpps_field_def','dpps_table_def','dpps_tmpl_design'));
update `dpps_field_def` set `list_order`='1063' where `name_eng`='flag';
update `dpps_field_def` set `list_order`='1064' where `name_eng`='arithmetic';
update `dpps_field_def` set `list_order`='1065' where `name_eng`='unicomment_id';
update `dpps_field_def` set `list_order`='1999' where `name_eng`='published_1';
update `dpps_field_def` set `list_order`='2000' where `name_eng`='url_1';
update `dpps_field_def` set `list_order`='2012' where `name_eng`='last_modify';
update `dpps_field_def` set `creator`='admin' where creator='0';
update `dpps_field_def` set `createdate`=DATE_FORMAT(NOW(),'%Y-%m-%d') where createdate='0000-00-00';
update `dpps_field_def` set `createtime`=DATE_FORMAT(NOW(),'%H:%i:%s') where createtime='00:00:00';

-- 站台网-交友频道 的抓取  ALTER TABLE `dpps_grab_request` AUTO_INCREMENT =1 ;
-- INSERT INTO `dpps_grab_request` (`name_cn`, `url`, `arithmetic`, `creator`, `createdate`, `createtime`, `parent_id`, `p_id_to`, `t_id_to`, `levelnum`, `domain`, `startdate`, `starttime`, `status_`, `if_article`, `arti_total`, `arti_hidden`, `if_album`, `album_toal`) VALUES ('站台网-交友', 'http://www.zhantai.com', '[code]<?php\r\n// 此处涉及到多处项目, 先去共用数据库中获取到站台网的城市数据。然后获取生活表中交友下属的所有栏目信息\r\n$dbR = new DBR();\r\n$l_err = $dbR->errorInfo();\r\nif ($l_err[1]>0){\r\n	// 数据库连接失败后\r\n	echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";\r\n	return null;\r\n}\r\n$dbR->table_name = TABLENAME_PREF."project";\r\n$p_arr_gongyong = $dbR->GetOne("where name_cn=''共用数据''");\r\n$p_arr_shenghuo = $dbR->GetOne("where name_cn=''生活频道''");\r\nif (PEAR::isError($p_arr_gongyong) || PEAR::isError($p_arr_shenghuo)) {\r\n	echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来\r\n	return null;\r\n}\r\n\r\n// 获取城市列表\r\n$dbR = new DBR($p_arr_gongyong);\r\n$dbR->table_name = "region_sheng";\r\n$l_city = $dbR->getAlls("where status_=''use'' and name_eng_zhantai != '''' ", "id,name_eng,name_cn,name_eng_zhantai");\r\n\r\n// 获取生活频道交友所属的二级栏目配置, 级别为2\r\n$dbR = new DBR($p_arr_shenghuo);\r\n$dbR->table_name = "aups_t003";\r\n$l_jiaoyou = $dbR->getAlls("where status_=''use'' and aups_f071=2 and aups_f078= ''交友聚会'' ", "id,zhantai_lanmu,aups_f070");\r\n\r\n// 进行拼装\r\n$l_urls = array();\r\nif (!empty($l_city) && !empty($l_jiaoyou)) {\r\n	foreach ($l_city as $l_c) {\r\n		foreach ($l_jiaoyou as $l_j) {\r\n			$l_urls[] = "http://www.jiaov.us/". trim($l_c[''name_eng_zhantai''], "/") . "/". trim($l_j[''zhantai_lanmu''],"/"). "/";\r\n		}\r\n	}\r\n}\r\nreturn $l_urls;', 'admin', DATE_FORMAT(NOW(), '%Y-%m-%d'), DATE_FORMAT(NOW(), '%H:%i:%s'), 0, 19, 2, 1, 'zhantai.com', '0000-00-00', '00:00:00', 'in', '1', NULL, NULL, '0', NULL);

update `dpps_grab_request` set `arithmetic` = '[code]<?php
// 此处涉及到多处项目, 先去共用数据库中获取到站台网的城市数据。然后获取生活表中交友下属的所有栏目信息
$l_name0_r = $GLOBALS[''cfg''][''SYSTEM_DB_DSN_NAME_R''];
$dbR = new DBR($l_name0_r);
$l_err = $dbR->errorInfo();
if ($l_err[1]>0){
	// 数据库连接失败后
	echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
	return null;
}
$dbR->table_name = TABLENAME_PREF."project";
$p_arr_gongyong = $dbR->GetOne("where name_cn=''共用数据''");
$p_arr_shenghuo = $dbR->GetOne("where name_cn=''生活频道''");
if (PEAR::isError($p_arr_gongyong) || PEAR::isError($p_arr_shenghuo)) {
	echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
	return null;
}

// 获取城市列表
$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
$dbR->dbo = &DBO(''gongyong'', $dsn);
$dbR->table_name = "region_sheng";
$l_city = $dbR->getAlls("where status_=''use'' and name_eng_zhantai != '''' ", "id,name_eng,name_cn,name_eng_zhantai");

// 获取生活频道交友所属的二级栏目配置, 级别为2
$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_shenghuo);
$dbR->dbo = &DBO(''shenghuo'', $dsn);
$dbR->table_name = "aups_t003";
$l_jiaoyou = $dbR->getAlls("where status_=''use'' and aups_f071=2 and aups_f078= ''交友聚会'' ", "id,zhantai_lanmu,aups_f070");

// 进行拼装
$l_urls = array();
if (!empty($l_city) && !empty($l_jiaoyou)) {
	foreach ($l_city as $l_c) {
		foreach ($l_jiaoyou as $l_j) {
			$l_urls[] = "http://jiaov.us/". trim($l_c[''name_eng_zhantai''], "/") . "/". trim($l_j[''zhantai_lanmu''],"/"). "/";
		}
	}
}
return $l_urls;' where name_cn='站台网-交友';

