//==============================================================================================
//PART IV(栏目样式相关)
//==============================================================================================

//==============================================================================================
//栏目样式列表表单(main.php?do=skin_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式的编辑
//-------------------------------------------------------------
function On_ColumnSkinListForm_PolymEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["cs_id"];
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
	oForm.action = "main.php?do=skin_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理样式的删除
//-------------------------------------------------------------
function On_ColumnSkinListForm_PolymDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["cs_id"];
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
	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=skin_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理样式的全选
//-------------------------------------------------------------
function On_ColumnSkinListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["cs_id"];
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

//-------------------------------------------------------------
//处理栏目的发布
//-------------------------------------------------------------
function On_ColumnListForm_PublishClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["c_id"];
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
		alert("请选择需要发布的栏目!");
		return;
	}
	if(confirm("确定发布吗?") == false)
	{
		return false;
	}
	alert("还不能发!");
	return false;
	oForm.action = "main.php?do=column_list";
	oForm.elements["_action"].value = "publish";
	oForm.submit();
}

//-------------------------------------------------------------
//处理栏目的编辑
//-------------------------------------------------------------
function On_ColumnListForm_EditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["c_id"];
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
		alert("请选择需要编辑的栏目!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个栏目!");
		return;
	}
	oForm.action = "main.php?do=column_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理栏目的删除
//-------------------------------------------------------------
function On_ColumnListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["c_id"];
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
		alert("请选择需要删除的栏目!");
		return;
	}
	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=column_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理栏目的全选
//-------------------------------------------------------------
function On_ColumnListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["c_id"];
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

//-------------------------------------------------------------
//处理子栏目的全选
//-------------------------------------------------------------
function On_ColumnCategoryListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["cc_id"];
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



//-------------------------------------------------------------
//处理栏目管理命令菜单
//-------------------------------------------------------------
function On_ColumnMenuClick(comm)
{
	var p_id = parent.form.p_id.value;
	var c_id = parent.form.c_id.value;
	var tURL1, tURL2;
	if(comm == "column_list"){
		tURL1 = '/dpa/main.php?do=column_list&p_id=' + p_id;
		parent.location.href = tURL1;
	}else if(comm == "delete_column"){

	}else if(comm == "cate_list"){
		if (c_id == "")
		{
			//alert("正在创建新栏目！");
			return;
		}
		tURL1 = '/dpa/main.php?do=category_list&p_id=' + p_id + '&c_id=' + c_id;
		tURL2 = '/dpa/main.php?do=category_edit&p_id=' + p_id + '&c_id=' + c_id;
		parent.CATE.location.href = tURL1;
		parent.WORK.location.href = tURL2;
	}else if(comm == "create_column"){
		tURL1 = '/dpa/main.php?do=category_list&p_id=' + p_id;
		tURL2 = '/dpa/main.php?do=category_add&p_id=' + p_id;
		parent.CATE.location.href = tURL1;
		parent.WORK.location.href = tURL2;
	}else if(comm == "create_cate"){
		alert('不提供此功能！');
		//tURL2 = '/dpa/main.php?do=_add&p_id=' + p_id + '&c_id=' + c_id;
		//parent.WORK.location.href = tURL2;
	}else if(comm == "edit_cate"){
		return;
		var cc_id;
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["cc_id"];
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
						cc_id = oCheckboxColl[i].value;
						j++;
					}
				}
			}
			else
			{
				if(oCheckboxColl.checked)
				{
					cc_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		if(j == 0)
		{
			alert("请选择需要编辑的栏目!");
			return;
		}
		else if(j != 1)
		{
			alert("一次只能编辑一个栏目!");
			return;
		}
		tURL = '/dpa/main.php?do=_edit&p_id=' + p_id + '&c_id=' + c_id + '&cc_id=' + cc_id;
		parent.WORK.location.href = tURL;
		
	}else if(comm == "publish_cate"){
		//发布专题栏目
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["cc_id"];
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
			alert("请选择需要发布的栏目!");
			return;
		}
		oForm.elements["_action"].value = "publish";
		oForm.action = "main.php?do=document_publish";
		oForm.target = "WORK";		
		oForm.submit();
				
	}else if(comm == "delete_cate"){		
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["cc_id"];
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
			alert("请选择需要删除的栏目!");
			return;
		}
		if(confirm("确定删除吗?") == false)
		{
			return false;
		}		
		oForm.elements["_action"].value = "delete";
		oForm.target = "_self";		
		oForm.submit();

	}else if(comm == "doc_list"){
		tURL2 = '/dpa/main.php?do=document_list&p_id=' + p_id + '&c_id=' + c_id + '&t_flag=1';
		parent.WORK.location.href = tURL2;
	}else if(comm == "doc_load"){
		tURL2 = '/dpa/main.php?do=doc_load&p_id=' + p_id + '&c_id=' + c_id;
		parent.WORK.location.href = tURL2;
	}
}

//-------------------------------------------------------------
//处理栏目编辑
//-------------------------------------------------------------
function On_ColumnEditCategoryClick(tURL)
{
	tURL = '/dpa/' + tURL
	parent.WORK.location.href = tURL;
}

//-------------------------------------------------------------
//处理由创建栏目到编辑栏目的跳转
//-------------------------------------------------------------
function FromColumnAddToEdit(p_id, c_id)
{
	tURL = "main.php?do=column_edit&p_id=" + p_id + "&c_id=" + c_id;
	location.href = tURL;
}

//-------------------------------------------------------------
//刷新栏目列表
//-------------------------------------------------------------
function ReloadColumnCateList(p_id, c_id)
{
	tURL = "main.php?do=category_list&p_id=" + p_id + "&c_id=" + c_id;
	parent.CATE.location.href = tURL;
}
