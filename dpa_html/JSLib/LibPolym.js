//==============================================================================================
//样式列表表单(main.php?do=polym_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式的编辑
//-------------------------------------------------------------
function On_PolymListForm_PolymEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["pm_id"];
	var bChecked = false;
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
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要编辑的样式!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个样式!");
		return;
	}
	oForm.action = "main.php?do=polym_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//-------------------------------------------------------------
//处理样式的删除
//-------------------------------------------------------------
function On_PolymListForm_PolymDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["pm_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					bChecked = true;
					break;
				}
			}
		}
		else
		{
			bChecked = oCheckboxColl.checked;
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的样式!");
		return;
	}
	oForm.action = "main.php?do=polym_delsetup";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理样式的版本列表入口
//-------------------------------------------------------------
function On_PolymListForm_PolymTranscodeListClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["pm_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					bChecked = true;
					break;
				}
			}
		}
		else
		{
			bChecked = oCheckboxColl.checked;
		}
	}
	if(!bChecked)
	{
		alert("请选择一个样式!");
		return;
	}
	oForm.action = "main.php?do=transcode_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理样式的全选
//-------------------------------------------------------------
function On_PolymListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["pm_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		for(var i=0; i<len; i++)
		{
			oCheckboxColl[i].checked = oSender.checked;
		}
	}
}



//==============================================================================================
//样式创建表单(main.php?do=polym_add) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式路径属性
//-------------------------------------------------------------

function On_PolymCreateForm_DefaultPathClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		oForm.elements['_PF_pm_path'].disabled = true;
		oForm.elements['_PF_pm_path'].style.display = "none";
		oForm.elements["_VF__NOTNULL_pm_path"].value = 'FALSE';
	}
	else
	{
		oForm.elements['_PF_pm_path'].disabled = false;
		oForm.elements['_PF_pm_path'].style.display = "inline";
		oForm.elements["_VF__NOTNULL_pm_path"].value = 'TRUE';
		oForm.elements['_PF_pm_path'].focus();
	}
}

//-------------------------------------------------------------
//处理样式基本属性
//-------------------------------------------------------------

function On_PolymCreateForm_BaseSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_Base.style.display = "none";
	}
	else
	{
		ID_Base.style.display = "inline";
	}
}

//-------------------------------------------------------------
//处理样式高级属性
//-------------------------------------------------------------

function On_PolymCreateForm_AdvancedSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_Advanced.style.display = "inline";
	}
	else
	{
		ID_Advanced.style.display = "none";
	}
}

//-------------------------------------------------------------
//处理图片归档属性
//-------------------------------------------------------------

function On_PolymCreateForm_ImageSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_ImageSetup.style.display = "inline";
	}
	else
	{
		ID_ImageSetup.style.display = "none";
	}
}

//==============================================================================================
//样式编辑表单 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理基本属性
//-------------------------------------------------------------
function On_PolymEditForm_BaseSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_Base.style.display = "none";
	}
	else
	{
		ID_Base.style.display = "inline";
	}
}

//-------------------------------------------------------------
//处理高级属性
//-------------------------------------------------------------
function On_PolymEditForm_AdvancedSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_Advanced.style.display = "inline";
	}
	else
	{
		ID_Advanced.style.display = "none";
	}
}


//-------------------------------------------------------------
//处理图片归档属性
//-------------------------------------------------------------
function On_PolymEditForm_ImageSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_ImageSetup.style.display = "inline";
	}
	else
	{
		ID_ImageSetup.style.display = "none";
	}
}

//==============================================================================================
//样式删除表单(main.php?do=polym_delsetup) JavaScript Function
//==============================================================================================

//处理样式的删除操作确认
function On_PolymDeleteForm_SubmitClick(oForm)
{
	if(confirm("请确认是否真的删除?"))
	{
		oForm.submit();
	}
}

//==============================================================================================
//PART IV(样式同步相关)
//==============================================================================================

//==============================================================================================
//样式同步列表表单(main.php?do=polym_sync_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式同步的编辑
//-------------------------------------------------------------
function On_PolymSyncListForm_PolymSyncEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ps_id"];
	var bChecked = false;
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
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要编辑的同步配置!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个同步配置!");
		return;
	}
	oForm.action = "main.php?do=polym_sync_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//-------------------------------------------------------------
//处理样式同步的删除
//-------------------------------------------------------------
function On_PolymSyncListForm_PolymSyncDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ps_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				if(oCheckboxColl[i].checked)
				{
					bChecked = true;
					break;
				}
			}
		}
		else
		{
			bChecked = oCheckboxColl.checked;
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的样式同步同步配置!");
		return;
	}
	if(confirm("请确认是否真的删除?"))
	{
		oForm.action = "main.php?do=polym_sync_list";
		oForm.elements["_action"].value = "delete";
		oForm.submit();
	}
}

//-------------------------------------------------------------
//处理样式同步的全选
//-------------------------------------------------------------
function On_PolymSybcListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ps_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		for(var i=0; i<len; i++)
		{
			oCheckboxColl[i].checked = oSender.checked;
		}
	}
}

//==============================================================================================
//样式同步创建表单(main.php?do=polym_sync_add) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式同步的全选
//-------------------------------------------------------------
function On_PolymSyncCreateForm_SyncMethodChange(oForm, oSender)
{
	var method = oSender.options[oSender.selectedIndex].value;
	if(method == "Rsync")
	{
		ID_Rsync_Method.style.display = "inline";
		oForm.elements["_VF__NOTNULL_rsync_host"].value = "TRUE";
		oForm.elements["_VF__NOTNULL_rsync_module"].value = "TRUE";

		oForm.elements["_VF__NOTNULL_ftp_host"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_ftp_user"].value = "FALSE";

		oForm.elements["_VF__NOTNULL_local_copy_path"].value = "FALSE";

		ID_FTP_Method.style.display = "none";
		ID_Copy_Method.style.display = "none";
	}
	else if(method == "FTP")
	{
		ID_Rsync_Method.style.display = "none";
		oForm.elements["_VF__NOTNULL_rsync_host"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_rsync_module"].value = "FALSE";

		oForm.elements["_VF__NOTNULL_ftp_host"].value = "TRUE";
		oForm.elements["_VF__NOTNULL_ftp_user"].value = "TRUE";

		oForm.elements["_VF__NOTNULL_local_copy_path"].value = "FALSE";

		ID_FTP_Method.style.display = "inline";
		ID_Copy_Method.style.display = "none";
	}
	else if(method == "Copy")
	{
		ID_Rsync_Method.style.display = "none";
		oForm.elements["_VF__NOTNULL_rsync_host"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_rsync_module"].value = "FALSE";

		oForm.elements["_VF__NOTNULL_ftp_host"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_ftp_user"].value = "FALSE";

		oForm.elements["_VF__NOTNULL_local_copy_path"].value = "TRUE";

		ID_FTP_Method.style.display = "none";
		ID_Copy_Method.style.display = "inline";
	}
}

function On_PolymSyncCreateForm_SyncAuthChange(oForm, oSender)
{
	var needauth = oSender.options[oSender.selectedIndex].value;
	if(needauth == "TRUE")
	{
		ID_Rsync_Auth_User.style.display = "inline";
		ID_Rsync_Auth_Pwd.style.display = "inline";
	}
	else
	{
		ID_Rsync_Auth_User.style.display = "none";
		ID_Rsync_Auth_Pwd.style.display = "none";
	}
}

function On_PolymCreateForm_FTPAdvancedSetupClick(oForm, oSender)
{
	if(oSender.checked)
	{
		ID_FTP_Advanced_Setup.style.display = "inline";
	}
	else
	{
		ID_FTP_Advanced_Setup.style.display = "none";
	}
}

function On_PolymCreateForm_FTPFirewallSetupClick(oForm, oSender)
{
	if(oSender.value == "00")
	{
		oForm.elements["_PF_ftp_proxy_host"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_host"].disabled = true;
		oForm.elements["_PF_ftp_proxy_port"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_port"].disabled = true;
		oForm.elements["_PF_ftp_proxy_user"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_user"].disabled = true;
		oForm.elements["_PF_ftp_proxy_pwd"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_pwd"].disabled = true;
	}
	else if(oSender.value == "01")
	{
		oForm.elements["_PF_ftp_proxy_host"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_host"].disabled = false;
		oForm.elements["_PF_ftp_proxy_port"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_port"].disabled = false;
		oForm.elements["_PF_ftp_proxy_user"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_user"].disabled = true;
		oForm.elements["_PF_ftp_proxy_pwd"].style.backgroundColor = "darkgray";
		oForm.elements["_PF_ftp_proxy_pwd"].disabled = true;
	}
	else if(oSender.value == "02")
	{
		oForm.elements["_PF_ftp_proxy_host"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_host"].disabled = false;
		oForm.elements["_PF_ftp_proxy_port"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_port"].disabled = false;
		oForm.elements["_PF_ftp_proxy_user"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_user"].disabled = false;
		oForm.elements["_PF_ftp_proxy_pwd"].style.backgroundColor = "";
		oForm.elements["_PF_ftp_proxy_pwd"].disabled = false;
	}
}
