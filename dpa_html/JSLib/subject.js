//-------------------------------------------------------------
//处理模板库的编辑
//-------------------------------------------------------------
function On_TemphtmlListForm_TemphtmlEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["h_id"];
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
		alert("请选择需要编辑的模板库样式!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板库样式!");
		return;
	}
	oForm.action = "main.php?do=tmpl_html_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理模板库的删除
//-------------------------------------------------------------
function On_TemphtmlListForm_TemphtmlDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["h_id"];
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
		alert("请选择需要删除的模板库样式!");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=tmpl_html_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理模板库的默认样式
//-------------------------------------------------------------
function On_TemphtmlListForm_TemphtmlDefaultClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["h_id"];
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
		alert("请选择需要设置为默认样式的模板库!");
		return;
	}
	oForm.action = "main.php?do=tmpl_html_list";
	oForm.elements["_action"].value = "default";
	oForm.submit();
}

//-------------------------------------------------------------
//处理模板库的全选
//-------------------------------------------------------------
function On_TemphtmlListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["h_id"];
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

//-------------------------------------------------------------
//处理模板库的提交预览
//-------------------------------------------------------------
function On_TmplHtmlForm_SubmitClick(oForm, oSender)
{
	if(!ifPreviewed)
	{
		if(confirm("你还没有预览模板!确定提交吗?") == false)
		{
			return false;
		}
	}
	oForm.action = "main.php?do=tmpl_html_list";
	oForm.submit();
}



//==============================================================================================
//PART IV(专题样式相关)
//==============================================================================================

//==============================================================================================
//样式列表表单(main.php?do=skin_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理样式的编辑
//-------------------------------------------------------------
function On_SubjectSkinListForm_PolymEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ss_id"];
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
function On_SubjectSkinListForm_PolymDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ss_id"];
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
function On_SubjectSkinListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ss_id"];
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
//处理专题的删除
//-------------------------------------------------------------
function On_SubjectListForm_PublishClick(oForm, oSender)
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
		alert("请选择需要发布的专题!");
		return;
	}
	if(confirm("确定发布吗?") == false)
	{
		return false;
	}
	alert("还不能发!");
	return false;
	oForm.action = "main.php?do=subject_list";
	oForm.elements["_action"].value = "publish";
	oForm.submit();
}

//-------------------------------------------------------------
//处理专题的编辑
//-------------------------------------------------------------
function On_SubjectListForm_EditClick(oForm, oSender)
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
		alert("请选择需要编辑的专题!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个专题!");
		return;
	}
	oForm.action = "main.php?do=subject_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//处理专题的删除
//-------------------------------------------------------------
function On_SubjectListForm_DeleteClick(oForm, oSender)
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
		alert("请选择需要删除的专题!");
		return;
	}
	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=subject_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理专题的全选
//-------------------------------------------------------------
function On_SubjectListForm_SelectAllClick(oForm, oSender)
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

//-------------------------------------------------------------
//处理专题栏目的全选
//-------------------------------------------------------------
function On_SubjectCategoryListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["sc_id"];
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
//处理专题文档导入的全选
//-------------------------------------------------------------
function On_SubjectDocImportForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["udid"];
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
//处理专题文档导入的导入
//-------------------------------------------------------------
function On_SubjectDocImportForm_ImportClick(oForm, oSender)
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
			bChecked = oCheckboxColl.checked;
		}
	}
	if(!bChecked)
	{
		alert("请选择需要导入的文档!");
		return;
	}	
	oForm._action.value="import";
	oForm.submit();
}

//-------------------------------------------------------------
//提示文档导入的数量
//-------------------------------------------------------------
function printLoadDocSum(sum)
{
	if(sum > 0)
	{
		alert("成功导入"+sum+"条文档！");
	}
}

//-------------------------------------------------------------
//处理专题管理命令菜单
//-------------------------------------------------------------
function On_SubjectMenuClick(comm)
{
	var p_id = parent.form.p_id.value;
	var s_id = parent.form.s_id.value;
	var tURL1, tURL2;
	if(comm == "subject_list"){
		tURL1 = '/dpa/main.php?do=subject_list&p_id=' + p_id;
		parent.location.href = tURL1;
	}else if(comm == "delete_subject"){

	}else if(comm == "cate_list"){
		if (s_id == "")
		{
			//alert("正在创建新专题！");
			return;
		}
		tURL1 = '/dpa/main.php?do=category_list&p_id=' + p_id + '&s_id=' + s_id;
		tURL2 = '/dpa/main.php?do=category_edit&p_id=' + p_id + '&s_id=' + s_id;
		parent.CATE.location.href = tURL1;
		parent.WORK.location.href = tURL2;
	}else if(comm == "create_subject"){
		tURL1 = '/dpa/main.php?do=category_list&p_id=' + p_id;
		tURL2 = '/dpa/main.php?do=category_add&p_id=' + p_id;
		parent.CATE.location.href = tURL1;
		parent.WORK.location.href = tURL2;
	}else if(comm == "create_cate"){
		alert('不提供此功能！');
		//tURL2 = '/dpa/main.php?do=_add&p_id=' + p_id + '&s_id=' + s_id;
		//parent.WORK.location.href = tURL2;
	}else if(comm == "edit_cate"){
		return;
		var sc_id;
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["sc_id"];
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
						sc_id = oCheckboxColl[i].value;
						j++;
					}
				}
			}
			else
			{
				if(oCheckboxColl.checked)
				{
					sc_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		if(j == 0)
		{
			alert("请选择需要编辑的专题栏目!");
			return;
		}
		else if(j != 1)
		{
			alert("一次只能编辑一个专题栏目!");
			return;
		}
		tURL = '/dpa/main.php?do=document_edit&p_id=' + p_id + '&s_id=' + s_id + '&sc_id=' + sc_id;
		parent.WORK.location.href = tURL;
		
	}else if(comm == "publish_cate"){
		//发布专题栏目
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["sc_id"];
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
			alert("请选择需要发布的专题栏目!");
			return;
		}
		oForm.elements["_action"].value = "publish";
		oForm.action = "main.php?do=document_publish";
		oForm.target = "WORK";
		oForm.submit();

	}else if(comm == "delete_cate"){		
		oForm = parent.CATE.form;
		var oCheckboxColl = oForm.elements["sc_id"];
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
			alert("请选择需要删除的专题栏目!");
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
		tURL2 = '/dpa/main.php?do=document_list&p_id=' + p_id + '&s_id=' + s_id + '&t_flag=1';
		parent.WORK.location.href = tURL2;
	}else if(comm == "doc_list2"){
		tURL2 = '/dpa/main.php?do=document_list&p_id=' + p_id + '&s_id=' + s_id + '&sc_id=' + sc_id + '&t_flag=1';
		parent.WORK.location.href = tURL2;
	}else if(comm == "doc_load"){
		tURL2 = '/dpa/main.php?do=doc_load&p_id=' + p_id + '&s_id=' + s_id;
		parent.WORK.location.href = tURL2;
	}
}

//-------------------------------------------------------------
//处理专题栏目编辑
//-------------------------------------------------------------
function On_SubjectEditCategoryClick(tURL)
{
	tURL = '/dpa/' + tURL
	parent.WORK.location.href = tURL;
}

//-------------------------------------------------------------
//处理由创建专题到编辑专题的跳转
//-------------------------------------------------------------
function FromSubjectAddToEdit(p_id, s_id)
{
	tURL = "main.php?do=subject_edit&p_id=" + p_id + "&s_id=" + s_id;
	location.href = tURL;
}

//-------------------------------------------------------------
//刷新栏目列表
//-------------------------------------------------------------
function ReloadSubjectCateList(p_id, s_id)
{
	tURL = "main.php?do=category_list&p_id=" + p_id + "&s_id=" + s_id;
	parent.CATE.location.href = tURL;
}





//==============================================================================================
//PART IV(分页模块相关)
//==============================================================================================

//==============================================================================================
//分页列表表单(main.php?do=page_list) JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//处理分页的编辑
//-------------------------------------------------------------
function On_PageListForm_EditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["page_id"];
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
		alert("请选择需要编辑的分页应用!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个分页应用!");
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
	var oCheckboxColl = oForm.elements["page_id"];
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
		alert("请选择需要删除的分页应用!");
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
	var oCheckboxColl = oForm.elements["page_id"];
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
//处理分页的状态查看
//-------------------------------------------------------------
function On_PageListForm_StatusClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["page_id"];
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
		alert("请选择一个分页应用!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能查看一个分页应用的状态!");
		return;
	}
	oForm.action = "main.php?do=status_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//-------------------------------------------------------------
//分页的状态相关
//-------------------------------------------------------------
//-------------------------------------------------------------
//处理分页的执行
//-------------------------------------------------------------
function On_PageListForm_ExecuteClick(oForm, oSender)
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
		alert("请选择需要执行的分页!");
		return;
	}
	else if(j != 1)
	{
		//alert("一次只能执行一个分页!");
		//return;
	}
	if(confirm("该操作可能很慢，建议在系统空闲时执行。\n\n确定执行吗?") == false)
	{
		return false;
	}	
	oForm.action = "main.php?do=status_list";
	oForm.elements["_action"].value = "execute";
	oForm.submit();
}

//-------------------------------------------------------------
//处理分页的删除
//-------------------------------------------------------------
function On_PageStatusListForm_DeleteClick(oForm, oSender)
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
		alert("请选择需要删除的分页状态!");
		return;
	}
	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=status_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理分页的全选
//-------------------------------------------------------------
function On_PageStatusListForm_SelectAllClick(oForm, oSender)
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
