//==============================================================================================
//PART I(项目相关)
//==============================================================================================

//==============================================================================================
//项目列表表单(main.php?do=project_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理项目的编辑
//-------------------------------------------------------------
function On_ProjectListForm_ProjectEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	var p_id;
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					p_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				p_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要编辑的项目!");
		return;
	}
	var pt = oForm.elements["pt"].value;
	var cgi_url =  "main.php?do=project_edit&pt=" + pt + "&id=" + p_id;
	window.self.open(cgi_url, "_self");
}

//-------------------------------------------------------------
//处理发布项目的导出
//-------------------------------------------------------------
function On_ProjectListForm_ProjectExportClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	var p_id;
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					p_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				p_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要导出的项目!");
		return;
	}
	var pt = oForm.elements["pt"].value;
	var cgi_url =  "main.php?do=project_export&id=" + p_id;
	window.self.open(cgi_url, "_self");
}

function On_ProjectListForm_ProjectPolymClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
        var p_id;
        var j = 0;
        if(oCheckboxColl != null)
        {
                var len = oCheckboxColl.length;
                if(len != null)
                {       
                        for(var i=0; i<len; i++)
                        {       
                                if(oCheckboxColl[i].checked)
                                {       
                                        p_id = oCheckboxColl[i].value;
                                        j++;
                                }
                        }
                }
                else
                {       
                        if(oCheckboxColl.checked)
                        {       
                                p_id = oCheckboxColl.value;
                                j++;
                        }
                }
        }
        if(j == 0)
        {
                alert("请选择需要进行样式列表的项目!");
                return;
        }
        var cgi_url =  "main.php?do=polym_list&id=" + p_id;
        window.self.open(cgi_url, "_self");
}
//-------------------------------------------------------------
//处理项目的删除
//-------------------------------------------------------------


function On_ProjectListForm_ProjectDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	var p_id;
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					p_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				p_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的项目!");
		return;
	}
	var pt = oForm.elements["pt"].value;
	var cgi_url =  "main.php?do=project_del&pt=" + pt + "&id=" + p_id;
	window.self.open(cgi_url, "_self");
}

function On_ProjectListForm_ResourceSyncClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	var p_id;
	var j = 0;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					p_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				p_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要配置同步的项目!");
		return;
	}
	var cgi_url = "resource/main.php?do=res_sync_list&id=" + p_id;
	window.self.open(cgi_url, "_self");
}

//==============================================================================================
//项目创建表单(main.php?do=project_add) JavaScript Function
//==============================================================================================

function On_ProjectCreateForm_AutoDatabaseNameClick(oForm ,oSender)
{
	if(oSender.checked)
	{
		oForm.elements['_PF_db_name'].disabled = true;
		oForm.elements['_PF_db_name'].style.display = "none";
		oForm.elements["_VF__NOTNULL_db_name"].value = 'FALSE';
	}
	else
	{
		oForm.elements['_PF_db_name'].disabled = false;
		oForm.elements['_PF_db_name'].style.display = "inline";
		oForm.elements["_VF__NOTNULL_db_name"].value = 'TRUE';
		oForm.elements['_PF_db_name'].focus();
	}
}

//-------------------------------------------------------------
//数据库连接超时
//-------------------------------------------------------------
function On_ProjectCreateForm_AutoDBTimeoutClick(oForm ,oSender)
{
	if(oSender.checked)
	{
		oForm.elements['_PF_db_timeout'].disabled = true;
		oForm.elements['_PF_db_timeout'].style.display = "none";
		oForm.elements["_VF__NOTNULL_db_timeout"].value = 'FALSE';
		oForm.elements["_VF__NUMERIC_db_timeout"].value = 'FALSE';
	}
	else
	{
		oForm.elements['_PF_db_timeout'].disabled = false;
		oForm.elements['_PF_db_timeout'].style.display = "inline";
		oForm.elements["_VF__NOTNULL_db_timeout"].value = 'TRUE';
		oForm.elements["_VF__NUMERIC_db_timeout"].value = 'TRUE';
		oForm.elements['_PF_db_timeout'].focus();
	}
}

//-------------------------------------------------------------
//使用默认系统数据库授权信息
//-------------------------------------------------------------
function On_ProjectCreateForm_DefaultDBAuthClick(oForm ,oSender)
{
	if(oSender.checked)
	{
		id_db_user_tr.style.display = 'none';
		id_db_pwd_tr.style.display = 'none';
		oForm.elements["_VF__NOTNULL_db_user"].value = 'FALSE';		
		oForm.elements["_VF__NOTNULL_db_pwd"].value = 'FALSE';		
		oForm.elements["_VF__NOTNULL_db_user"].disabled = true;	
		oForm.elements["_VF__NOTNULL_db_pwd"].disabled = true;	
	}
	else
	{
		id_db_user_tr.style.display = '';
		id_db_pwd_tr.style.display = '';
		oForm.elements["_VF__NOTNULL_db_user"].value = 'TRUE';		
		oForm.elements["_VF__NOTNULL_db_pwd"].value = 'TRUE';		
		oForm.elements["_VF__NOTNULL_db_user"].disabled = false;	
		oForm.elements["_VF__NOTNULL_db_pwd"].disabled = false;	
	}
}

//-------------------------------------------------------------
//使用root帐号创建指定的数据库账号
//-------------------------------------------------------------
function On_ProjectCreateForm_UseRootAccountClick(oForm ,oSender)
{
	if(oSender.checked)
	{
		id_row_root_user.style.display = '';
		id_row_root_pwd.style.display = '';
	}
	else
	{
		id_row_root_user.style.display = 'none';
		id_row_root_pwd.style.display = 'none';
	}
}

//==============================================================================================
//项目注册表单 JavaScript Function
//==============================================================================================


function On_ProjectRegisterForm_DBTimeoutClick(oForm ,oSender)
{
	if(oSender.checked)
	{
		oForm.elements['_PF_db_timeout'].disabled = true;
		oForm.elements['_PF_db_timeout'].style.display = "none";
		oForm.elements["_VF__NOTNULL_db_timeout"].value = 'FALSE';
		oForm.elements["_VF__NUMERIC_db_timeout"].value = 'FALSE';
	}
	else
	{
		oForm.elements['_PF_db_timeout'].disabled = false;
		oForm.elements['_PF_db_timeout'].style.display = "inline";
		oForm.elements["_VF__NOTNULL_db_timeout"].value = 'TRUE';
		oForm.elements["_VF__NUMERIC_db_timeout"].value = 'TRUE';
		oForm.elements['_PF_db_timeout'].focus();
	}
}

//==============================================================================================
//项目删除确认表单(main.php?do=project_del) JavaScript Function
//==============================================================================================
function On_ProjectDeleteForm_SubmitClick(oForm, oSender)
{
		if(confirm("请确认是否真的删除?"))
		{
			oForm.submit();
		}
}


//==============================================================================================
// 通用 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
// 数据库链接测试
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oTarget:数据回送对象
//-------------------------------------------------------------
function On_ProjectEditForm_ConnTestClick(oForm, oSender, Type)
{
	var DBHost;
	var DBName;
	var DBPort;
	var DBUser;
	var DBPassword;
	var DBTimeout;
	if(Type == "master")
	{
		DBHost = getValueByName(oForm, '_PF_db_host');
		DBName = getValueByName(oForm, '_PF_db_name');
		DBPort = getValueByName(oForm, '_PF_db_port');
		DBUser = getValueByName(oForm, '_PF_db_user');
		DBPassword = getValueByName(oForm, '_PF_db_pwd');
		DBTimeout = getValueByName(oForm, '_PF_db_timeout');
	}
	else
	{
		DBHost = getValueByName(oForm, '_PF_slave_db_host');
		DBName = getValueByName(oForm, '_PF_slave_db_name');
		DBPort = getValueByName(oForm, '_PF_slave_db_port');
		DBUser = getValueByName(oForm, '_PF_slave_db_user');
		DBPassword = getValueByName(oForm, '_PF_slave_db_pwd');
		DBTimeout = getValueByName(oForm, '_PF_slave_db_timeout');
	}
	var cgi = "/dpa/main.php?do=dbconn_test&db_host=" + DBHost + "&db_name=" + DBName + "&db_port=" + DBPort + "&db_user=" + DBUser + "&db_passowrd=" + DBPassword;
	window.showModalDialog(cgi, "","dialogHeight: 180px; dialogWidth: 240px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
}
