-- 模板设计表 dpps_tmpl_design
-- select * from dpps_field_def where name_eng='tbl_id' and t_id = (select id from dpps_table_def where name_eng='dpps_tmpl_design') 
--    更新数据类型: 
-- update  dpps_field_def set `f_type`='Form::DB_Select', `arithmetic`='[query]
-- sql=select CONCAT(name_eng,"-",name_cn),id from dpps_table_def where `status_`=''use'' order by list_order' where name_eng='tbl_id' and t_id = (select id from dpps_table_def where name_eng='dpps_tmpl_design') 

update `dpps_field_def` set `list_order`='10' where `name_eng`='id' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='15',`f_type`='Form::DB_Select', `arithmetic`='[query]
sql=select CONCAT(name_eng,"-",name_cn),id from dpps_table_def where `status_`=''use'' order by list_order' where `name_eng`='tbl_id' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='20' where `name_eng`='creator' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='21' where `name_eng`='createdate' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='22' where `name_eng`='createtime' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='35' where `name_eng`='mender' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='36' where `name_eng`='menddate' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='37' where `name_eng`='mendtime' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='40' where `name_eng`='content_type' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='100' where `name_eng`='if_publish' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='110' where `name_eng`='default_html' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='120' where `name_eng`='default_url' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='130' where `name_eng`='tmpl_expr' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='140' where `name_eng`='status_' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
update `dpps_field_def` set `list_order`='1001' where `name_eng`='description' and `t_id` = (select id from dpps_table_def where name_eng='dpps_tmpl_design');
