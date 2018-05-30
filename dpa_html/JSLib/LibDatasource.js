//==============================================================================================
// 处理datasource JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_DatasourceListForm_FirstPageClick(oForm, oSender)
{
	oForm.elements["_goto_page"].value = "0";
	oForm.submit();
}

//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_DatasourceListForm_Init(oForm, oSender)
{
	var cur_page = parseInt(oForm.elements["_goto_page"].value);
	var def_page_size = parseInt(oForm.elements["_page_size"].value);
	var cur_page_size = parseInt(oForm.elements["_cur_page_size"].value);
	if(cur_page == 0)
	{
		oForm.elements["goto_first_page"].disabled = true;
		oForm.elements["goto_prev_page"].disabled = true;
	}
	if(cur_page_size < def_page_size)
	{
		oForm.elements["goto_next_page"].disabled = true;
	}
}

//-------------------------------------------------------------
//导航记录到上一页
//-------------------------------------------------------------
function On_DatasourceListForm_PrevPageClick(oForm, oSender)
{
	var cur_page = parseInt(oForm.elements["_goto_page"].value);
	var page_size = parseInt(oForm.elements["_page_size"].value);
	if(isNaN(cur_page))
		cur_page = 0;
	if(isNaN(page_size))
		page_size = 30;
	var prev_page = cur_page - page_size;
	if(prev_page < 0)
	{
		prev_page = 0;
	}
	oForm.elements["_goto_page"].value = prev_page;
	oForm.submit();
}

//-------------------------------------------------------------
//导航记录到下一页
//-------------------------------------------------------------
function On_DatasourceListForm_NextPageClick(oForm, oSender)
{
	var cur_page = parseInt(oForm.elements["_goto_page"].value);
	var page_size = parseInt(oForm.elements["_page_size"].value);
	if(isNaN(cur_page))
		cur_page = 0;
	if(isNaN(page_size))
		page_size = 30;
	var next_page = cur_page + page_size;
	oForm.elements["_goto_page"].value = next_page;
	oForm.submit();
}


//-----------------------------------
//处理编辑数据源
//-----------------------------------
function On_DataSourceListForm_EditClick(oForm)
{
	var obj = oForm.elements["dsn_id"];
	if(obj == null)
	{
		alert("无数据源!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var dsn_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					dsn_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				dsn_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=datasource_edit&dsn_id="+dsn_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个数据源!");
		}
		else
		{
			alert("一次只能编辑一个数据源!");
		}
	}
	return false;		
}

//-----------------------------------
//处理删除数据源
//-----------------------------------
function On_DataSourceListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["dsn_id"];
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的数据源");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=datasource_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-----------------------------------
//处理数据源全选
//-----------------------------------
function On_DataSourceListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["dsn_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

//-----------------------------------
//处理数据源列表转到表映射列表
//-----------------------------------
function On_DataSourceListForm_ToTabMapClick(oForm, oSender)
{
	var obj = oForm.elements["dsn_id"];
	if(obj == null)
	{
		alert("没有指定数据源!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var dsn_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					dsn_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				dsn_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=tab_map_list&dsn_id="+dsn_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个数据源!");
		}
		else
		{
			alert("一次只能选择一个数据源!");
		}
	}
	return false;		
}

//-------------------------------------
//处理添加数据表映射，由表列表转到添加表映射
//-------------------------------------
function On_AddDSTabMap_NextStepClick(oForm, oSender)
{
	var obj = oForm.elements["table_name"];
	var dsn_id = getValueByName(oForm, "dsn_id");
	if(obj == null)
	{
		alert("无表!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var table_name;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					table_name = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				table_name = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=tab_map_add&dsn_id="+dsn_id + "&table_name="+table_name;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个表!");
		}
	}
	return false;		
}

//-----------------------------------
//处理表映射全选
//-----------------------------------
function On_DSTabMapListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tm_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

//-----------------------------------
//处理编辑数据源表映射
//-----------------------------------
function On_DSTabMapListForm_EditClick(oForm)
{
	var obj = oForm.elements["tm_id"];
	var dsn_id = oForm.elements["dsn_id"].value;
	if(obj == null)
	{
		alert("没有表映射!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var tm_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					tm_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				tm_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=tab_map_edit&tm_id=" + tm_id + "&dsn_id=" + dsn_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个表映射!");
		}
		else
		{
			alert("一次只能编辑一个表映射!");
		}
	}
	return false;		
}

//-----------------------------------
//处理删除数据源表映射
//-----------------------------------
function On_DSTabMapListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tm_id"];
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的表映射");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=tab_map_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------
//处理数据源表映射列表页面，转到字段映射列表
//-------------------------------------

function On_DSTabMapListForm_ToFieldListClick(oForm)
{
	var obj = oForm.elements["tm_id"];
	if(obj == null)
	{
		alert("没有指定数据源表映射!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var tm_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					tm_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				tm_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=field_map_list&tm_id="+tm_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个数据源表映射!");
		}
		else
		{
			alert("一次只能选择一个数据源表映射!");
		}
	}
	return false;		
}

//-----------------------------------——--------
//处理添加数据源字段映射，由字段列表页面转到添加页面
//---------------------------------------------
function On_AddDSFieldMap_NextStepClick(oForm, oSender)
{
	var obj = oForm.elements["field_name"];
	var tm_id = getValueByName(oForm, "tm_id");
	if(obj == null)
	{
		alert("无字段!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var field_name;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					field_name = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				field_name = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=field_map_add&tm_id="+tm_id + "&field_name="+field_name;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个字段!");
		}
	}
	return false;		
}

//-----------------------------------
//处理编辑数据源字段映射
//-----------------------------------
function On_DSFieldMapListForm_EditClick(oForm)
{
	var obj = oForm.elements["fm_id"];
	var tm_id = oForm.elements["tm_id"].value;
	if(obj == null)
	{
		alert("没有字段映射!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var fm_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					fm_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				fm_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=field_map_edit&fm_id=" + fm_id + "&tm_id=" + tm_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个字段!");
		}
		else
		{
			alert("一次只能编辑一个字段!");
		}
	}
	return false;		
}

//-----------------------------------
//处理删除数据源字段映射
//-----------------------------------
function On_DSFieldMapListForm_DeleteClick(oForm)
{
	var oCheckboxColl = oForm.elements["fm_id"];
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的字段");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=field_map_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-----------------------------------
//处理数据源字段映射列表全选
//-----------------------------------
function On_DSFieldMapListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["fm_id"];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}
