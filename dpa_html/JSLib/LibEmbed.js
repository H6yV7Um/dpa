//==============================================================================================
//插件列表表单 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_EmbedDataListForm_FirstPageClick(oForm, oSender)
{
	oForm.elements["_goto_page"].value = "0";
	oForm.submit();
}

//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_EmbedDataListForm_Init(oForm, oSender)
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
function On_EmbedDataListForm_PrevPageClick(oForm, oSender)
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
function On_EmbedDataListForm_NextPageClick(oForm, oSender)
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

//------------------------------
//处理系统模板插件编辑
//------------------------------
function On_EmbedDataListForm_SysDataEditClick(form)
{
	var obj = form.elements["id"];
	if(obj == null)
	{
		alert("无插件!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=embeddata_edit&id="+id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个插件!");
		}
		else
		{
			alert("一次只能编辑一个插件!");
		}
	}
	return false;		
}

//---------------------------
//处理系统模板插件删除
//---------------------------

function On_EmbedDataListForm_SysDataDeleteClick(oForm,oSender)
{
	var oCheckboxColl = oForm.elements["id"];
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
		alert("请选择需要删除的插件");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=embeddata_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//------------------------------
//处理频道模板插件编辑
//------------------------------
function On_EmbedDataListForm_ProDataEditClick(form)
{
	var obj = form.elements["id"];
	var p_id = form.elements["p_id"].value;
	if(obj == null)
	{
		alert("无插件!");
	}
	else
	{
		var len = obj.length;
		var check_count = 0;
		var id;
		if(len != null)
		{
			for(var i=0;i<len;i++)
			{
				if(obj[i].checked)
				{
					id = obj[i].value;
					check_count++;
				}
			}
		}
		else
		{
			if(obj.checked)
			{
				id = obj.value;
				check_count++;
			}
		}
		if(check_count == 1)
		{
			location.href = "main.php?do=project_embeddata_edit&p_id=" + p_id + "&id=" + id;
		  	return true;
		}
		else if(check_count == 0)
		{
			alert("至少得选中一个插件!");
		}
		else
		{
			alert("一次只能编辑一个插件!");
		}
	}
	return false;		
}

//---------------------------
//处理频道模板插件删除
//---------------------------

function On_EmbedDataListForm_ProDataDeleteClick(oForm,oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	alert("here");
	var p_id = oForm.elements["p_id"].value;
	alert(p_id);
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
		alert("请选择需要删除的插件");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=project_embeddata_list&p_id=" + p_id;
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理模板插件的拷贝
//-------------------------------------------------------------
function On_TmplDesignForm_EmbedDataCopyClick(oForm, oSender)
{
		oForm.elements["embeddata_copy"].value = oSender.options[oSender.selectedIndex].value;
}

//-------------------------------------------------------------
// 处理更多插件列表
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oTarget:数据回送对象
//		p_id:项目ID号，为“”表示调用系统模板插件
//-------------------------------------------------------------
function On_EmbedData_ListClick(oForm, oSender, oTarget, p_id)
{
	if (p_id != "")
	{
		var cgi= "/dpa/main.php?do=proembeddata_dialoglist&p_id=" + p_id;
	}
	else
	{
		var cgi= "/dpa/main.php?do=sysembeddata_dialoglist";
	}
	
	var property = "scrollbars=yes,height=250,width=400,toolbar=yes,menubar=no,location=no,screenX=50,screenY=50";
	window.open(cgi,null, property);
}
