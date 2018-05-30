//==============================================================================================
//版本列表表单(main.php?do=transcode_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理版本的编辑
//-------------------------------------------------------------
function On_TranscodeListForm_TranscodeEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tc_id"];
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
		alert("请选择需要编辑的版本!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个版本!");
		return;
	}
	oForm.action = "main.php?do=transcode_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//-------------------------------------------------------------
//处理版本的删除
//-------------------------------------------------------------
function On_TranscodeListForm_TranscodeDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tc_id"];
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
		alert("请选择需要删除的版本!");
		return;
	}
	oForm.action = "main.php?do=transcode_delsetup";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理版本的全选
//-------------------------------------------------------------
function On_TranscodeListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tc_id"];
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
//版本创建表单(main.php?do=transcode_add) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理版本路径属性
//-------------------------------------------------------------

function On_TranscodeCreateForm_DefaultPathClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		oForm.elements['_PF_tc_path'].disabled = true;
		oForm.elements['_PF_tc_path'].style.display = "none";
		oForm.elements["_VF__NOTNULL_tc_path"].value = 'FALSE';
	}
	else
	{
		oForm.elements['_PF_tc_path'].disabled = false;
		oForm.elements['_PF_tc_path'].style.display = "inline";
		oForm.elements["_VF__NOTNULL_tc_path"].value = 'TRUE';
		oForm.elements['_PF_tc_path'].focus();
	}
}

//-------------------------------------------------------------
//处理版本基本属性
//-------------------------------------------------------------

function On_TranscodeCreateForm_BaseSetupClick(oForm, oSender) 
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
//处理版本高级属性
//-------------------------------------------------------------

function On_TranscodeCreateForm_AdvancedSetupClick(oForm, oSender) 
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

function On_TranscodeCreateForm_ImageSetupClick(oForm, oSender) 
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
//版本编辑表单 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理基本属性
//-------------------------------------------------------------
function On_TranscodeEditForm_BaseSetupClick(oForm, oSender) 
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
function On_TranscodeEditForm_AdvancedSetupClick(oForm, oSender) 
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
function On_TranscodeEditForm_ImageSetupClick(oForm, oSender) 
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
//版本删除表单(main.php?do=transcode_delsetup) JavaScript Function
//==============================================================================================

//处理版本的删除操作确认
function On_TranscodeDeleteForm_SubmitClick(oForm)
{
	if(confirm("请确认是否真的删除?"))
	{
		oForm.submit();
	}
}

//==============================================================================================
//PART IV(版本同步相关)
//==============================================================================================

//==============================================================================================
//版本同步列表表单(main.php?do=transcode_sync_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理版本同步的编辑
//-------------------------------------------------------------
function On_TranscodeSyncListForm_TranscodeSyncEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ts_id"];
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
	oForm.action = "main.php?do=transcode_sync_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//-------------------------------------------------------------
//处理版本同步的删除
//-------------------------------------------------------------
function On_TranscodeSyncListForm_TranscodeSyncDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ts_id"];
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
		alert("请选择需要删除的版本同步同步配置!");
		return;
	}
	if(confirm("请确认是否真的删除?"))
	{
		oForm.action = "main.php?do=transcode_sync_list";
		oForm.elements["_action"].value = "delete";
		oForm.submit();
	}
}

//-------------------------------------------------------------
//处理版本同步的全选
//-------------------------------------------------------------
function On_TranscodeSybcListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ts_id"];
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
//版本同步创建表单(main.php?do=transcode_sync_add) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理版本同步的全选
//-------------------------------------------------------------
function On_TranscodeSyncCreateForm_SyncMethodChange(oForm, oSender)
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

function On_TranscodeSyncCreateForm_SyncAuthChange(oForm, oSender)
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

function On_TranscodeCreateForm_FTPAdvancedSetupClick(oForm, oSender)
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

function On_TranscodeCreateForm_FTPFirewallSetupClick(oForm, oSender)
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
