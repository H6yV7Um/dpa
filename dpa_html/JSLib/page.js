//==============================================================================================
//PART IV(分页模块相关)
//==============================================================================================

//-------------------------------------------------------------
//处理分页引擎的发布
//-------------------------------------------------------------
function On_PageListForm_PublishClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["s_id"];
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
		alert("请选择需要发布的分页!");
		return;
	}
	if(confirm("确定发布吗?") == false)
	{
		return false;
	}
	alert("还不能发!");
	return false;
	oForm.action = "main.php?do=page_list";
	oForm.elements["_action"].value = "publish";
	oForm.submit();
}

//-------------------------------------------------------------
//处理分页的编辑
//-------------------------------------------------------------
function On_PageListForm_EditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["s_id"];
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
		alert("请选择需要编辑的分页!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个分页!");
		return;
	}
	oForm.action = "main.php?do=page_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理分页的删除
//-------------------------------------------------------------
function On_PageListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["s_id"];
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
		alert("请选择需要删除的分页!");
		return;
	}
	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=page_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理分页的全选
//-------------------------------------------------------------
function On_PageListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["s_id"];
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

