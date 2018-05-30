//=============================================================================
//数据中心管理界面相关JS function
//=============================================================================

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_GDMSListForm_FirstPageClick(oForm, oSender)
{
	oForm.elements["_goto_page"].value = "0";
	oForm.submit();
}

//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_GDMSListForm_Init(oForm, oSender)
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
function On_GDMSListForm_PrevPageClick(oForm, oSender)
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
function On_GDMSListForm_NextPageClick(oForm, oSender)
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


//-------------------------------------
//专题管理相关
//-------------------------------------

function On_GdmsSubjectListForm_SelectAllClick(oForm, oSender)
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
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

function On_GdmsSubjectListForm_Edit(oForm)
{
	var obj = oForm.elements["s_id"];
	var gdid = oForm.elements["gdid"].value;
	if(obj == null)
	{
		alert("无专题!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var s_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					s_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				s_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=subject_edit&gdid=" + gdid + "&s_id=" + s_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个专题!");
		}
		else
		{
			alert("一次只能编辑一个专题!");
		}
	}
	return false;
}

function On_GdmsSubjectListForm_Delete(oForm)
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的ID");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//---------------------
//
//---------------------
function On_GdmsCategoryListForm_Edit(oForm)
{
	var obj = oForm.elements["c_id"];
	var gdid = oForm.elements["gdid"].value;
	var c_pid = oForm.elements["c_pid"].value;
	if(obj == null)
	{
		alert("无分类!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var c_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					c_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				c_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=category_edit&gdid=" + gdid + "&c_id=" + c_id + "&c_pid=" + c_pid;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个分类!");
		}
		else
		{
			alert("一次只能编辑一个分类!");
		}
	}
	return false;
}

function On_GdmsCategoryListForm_Delete(oForm)
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的分类");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.elements["_action"].value = "delete";
	oForm.submit();
	
}

function On_GdmsCategoryListForm_AddSubCategory(oForm)
{
	var obj = oForm.elements["c_id"];
	var gdid = oForm.elements["gdid"].value;
	if(obj == null)
	{
		alert("无分类!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var c_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					c_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				c_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=category_add&gdid=" + gdid + "&c_pid=" + c_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("请选择一个分类!");
		}
		else
		{
			alert("只能选择一个分类!");
		}
	}
	return false;

}

function On_GdmsCategoryListForm_SelectAllClick(oForm, oSender)
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
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

function On_GdmsDetailCategoryListForm_Edit(oForm)
{
	var obj = oForm.elements["dc_id"];
	var gdid = oForm.elements["gdid"].value;
	var dc_pid = oForm.elements["dc_pid"].value;
	if(obj == null)
	{
		alert("无分类!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var dc_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					dc_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				dc_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=detailcategory_edit&gdid=" + gdid + "&dc_id=" + dc_id + "&dc_pid=" + dc_pid;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个分类!");
		}
		else
		{
			alert("一次只能编辑一个分类!");
		}
	}
	return false;
}

function On_GdmsDetailCategoryListForm_Delete(oForm)
{
	var oCheckboxColl = oForm.elements["dc_id"];
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
		alert("请选择需要删除的分类");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

function On_GdmsDetailCategoryListForm_AddSubDetailCategory(oForm)
{
	var obj = oForm.elements["dc_id"];
	var gdid = oForm.elements["gdid"].value;
	if(obj == null)
	{
		alert("无分类!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var dc_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					dc_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				dc_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=detailcategory_add&gdid=" + gdid + "&dc_pid=" + dc_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("请选择一个分类!");
		}
		else
		{
			alert("只能选择一个分类!");
		}
	}
	return false;

}

function On_GdmsDetailCategoryListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["dc_id"];
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

//----------------------
//
//----------------------

function On_GdmsCategoryMapListForm_Edit(oForm)
{
	var obj = oForm.elements["c_id"];
	var gdid = oForm.elements["gdid"].value;
	if(obj == null)
	{
		alert("无分类!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var c_id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					c_id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				c_id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=category_map_edit&gdid=" + gdid + "&c_id=" + c_id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个分类!");
		}
		else
		{
			alert("一次只能编辑一个分类!");
		}
	}
	return false;
}

function On_GdmsCategoryMapListForm_Delete(oForm)
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
			if(oCheckboxColl.checked)
			{
				bChecked = true;
			}
		}
	}
	if(!bChecked)
	{
		alert("请选择需要删除的映射");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

function On_GdmsCategoryMapListForm_SelectAllClick(oForm, oSender)
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
				oCheckboxColl[i].checked = oSender.checked;
			}
		}
		else
		{
			oCheckboxColl.checked = oSender.checked;
		}
	}
}

function On_GdmsArticleListForm_Edit(oForm)
{
	var obj = oForm.elements["udid"];
	var gdid = oForm.elements["gdid"].value;
	var tabletype = oForm.elements["type"].value;
	if(obj == null)
	{
		alert("无文档!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var udid;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					udid = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				udid = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=article_edit&gdid=" + gdid + "&udid=" + udid + "&type=" + tabletype;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个文档!");
		}
		else
		{
			alert("一次只能编辑一个文档!");
		}
	}
	return false;
}

function On_GdmsArticleListForm_Delete(oForm)
{
	var oCheckboxColl = oForm.elements["udid"];
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
		alert("请选择需要删除的文档");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

function On_GdmsArticleListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["udid"];
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