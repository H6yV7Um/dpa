//==============================================================================================
//PART I(模板管理) JavaScript Function
//==============================================================================================

	
//保存当前的模板顺序属性值
var vTemplateList_Origin_Orders = new Array();


//----------------------------------------------------------------------------------------------
//提交设置的模板列表顺序
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_SetOrderClick(oForm, oSender, action)
{
	oForm.elements["_action"].value = action;
	oForm.submit();
}



//----------------------------------------------------------------------------------------------
//处理模板的编辑
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要编辑的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板!");
		return;
	}
	oForm.action = "main.php?do=template_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}



//----------------------------------------------------------------------------------------------
//处理模板的设计
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateDesignClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要编辑的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板!");
		return;
	}
	oForm.action = "main.php?do=tmpl_design";
	oForm.elements["_action"].value = "";
	oForm.submit();
}



//----------------------------------------------------------------------------------------------
//处理模板的设计
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateCallClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要编辑的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板!");
		return;
	}
	oForm.action = "main.php?do=tmpl_call_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板的优化
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateOptimizeClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要优化的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能操作一个模板!");
		return;
	}
	oForm.action = "main.php?do=template_optimize";
	oForm.elements["_action"].value = "";
	oForm.submit();
}



//----------------------------------------------------------------------------------------------
//模板创建向导JS
//----------------------------------------------------------------------------------------------
function On_TemplateCreateWizard_NextStepClick(oForm, oSender)
{
	var type = getValueByName(oForm, "type");
	if(type == "new")
	{
		oForm.action = "main.php?do=template_add";
		oForm.submit();
	}
	else if(type == "project")
	{
		oForm.action = "main.php?do=template_import";
		oForm.submit();
	}
	else if(type == "db")
	{
		oForm.action = "#";
		oForm.submit();
	}
	else if(type == "file")
	{
		oForm.action = "main.php?do=template_import_from_file";
		oForm.submit();
	}
}

//----------------------------------------------------------------------------------------------
//处理模板域的管理
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TempdefSetupClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要管理的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能管理一个模板!");
		return;
	}
	oForm.action = "main.php?do=tempdef_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板定制发布
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateCustomizeClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要定制发布的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能定制发布一个模板!");
		return;
	}
	oForm.action = "main.php?do=customization_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理样式的管理
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_PolymSetupClick(oForm, oSender)
{
	oForm.action = "main.php?do=polym_list";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板的导出
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateExportClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
			if( oCheckboxColl.checked )
			{
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要导出的模板!");
		return;
	}
	else if( j != 1 )
	{
		alert("一次只能导出一个模板!");
		return;
	}
	oForm.action = "main.php?do=template_export";
	oForm.elements["_action"].value = "export";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//处理模板的删除
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_TemplateDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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
		alert("请选择需要删除的模板!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能删除一个模板!");
		return;
	}
	oForm.action = "main.php?do=template_delsetup";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//处理模板的全选
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["t_id"];
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

//----------------------------------------------------------------------------------------------
//设置为自然顺序
//----------------------------------------------------------------------------------------------
function On_TemplateListForm_NatureOrderClick(oForm, oSender)
{
	var len = oForm.elements.length;
	var name;
	var prefix;
	var value;
	var j=1;
	var ss;
	for(var i=0; i<len; i++)
	{
		name = oForm.elements[i].name;
		prefix = name.substr(0, "order__of__".length);
		if(prefix == "order__of__")
		{
			if(oSender.checked)
			{
				vTemplateList_Origin_Orders[j] = oForm.elements[i].name + "=" + oForm.elements[i].value;
				oForm.elements[i].value = j;
			}
			else
			{
				ss = vTemplateList_Origin_Orders[j].split("=");
				oForm.elements[ss[0]].value = ss[1];
			}
			j++;
		}
	}
}


//----------------------------------------------------------------------------------------------
//处理是否分配模板数据表名称
//----------------------------------------------------------------------------------------------
function On_TemplateCreateForm_AutoTable(oFrom ,oSender) 
{
	if(oSender.checked)
	{
		oFrom.elements['_PF_t_name'].disabled = true;
		oFrom.elements['_PF_t_name'].style.display = "none";
		oFrom.elements["_VF__NOTNULL_t_name"].value = 'FALSE';
	}
	else
	{
		oFrom.elements['_PF_t_name'].disabled = false;
		oFrom.elements['_PF_t_name'].style.display = "inline";
		oFrom.elements["_VF__NOTNULL_t_name"].value = 'TRUE';
		oFrom.elements['_PF_t_name'].focus();
	}
}


//----------------------------------------------------------------------------------------------
//处理模板编辑表单的高级属性设置
//----------------------------------------------------------------------------------------------
function On_TemplateEditForm_AdvancedPropertyClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_Advanced_Setup.style.display = "inline";
	}
	else
	{
		ID_Advanced_Setup.style.display = "none";
	}
}


//----------------------------------------------------------------------------------------------
//处理模板编辑表单的文档属性设置
//----------------------------------------------------------------------------------------------
function On_TemplateEditForm_DocumentPropertyClick(oForm, oSender)
{
	if(oSender.checked)
	{
		ID_Document_Setup.style.display = "inline";
	}
	else
	{
		ID_Document_Setup.style.display = "none";
	}
}

//----------------------------------------------------------------------------------------------
//处理模板导入表单的文档属性设置
//----------------------------------------------------------------------------------------------
function On_TemplateImportForm_AdvancedPropertyClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		id_as.style.display = "inline";
	}
	else
	{
		id_as.style.display = "none";
	}
}


//----------------------------------------------------------------------------------------------
//处理是否删除模板对应的数据表事件
//----------------------------------------------------------------------------------------------
function On_TemplateDeleteForm_DeleteTmplDataTableClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		id_tmpl_backup_ui_1.style.display = "inline";
		id_tmpl_backup_ui_2.style.display = "inline";
	}
	else
	{
		id_tmpl_backup_ui_1.style.display = "none";
		id_tmpl_backup_ui_2.style.display = "none";
	}
}


//----------------------------------------------------------------------------------------------
//处理是否删除模板表单提交事件
//----------------------------------------------------------------------------------------------
function On_TemplateDeleteForm_SubmitClick(oForm, oSender)
{
	if(confirm("请确认是否真的删除?"))
	{
		oForm.submit();
	}
}


//----------------------------------------------------------------------------------------------
//处理模板域表单提交校验事件
//----------------------------------------------------------------------------------------------
function On_TemplateDef_FormVerifySubmitClick(oForm, oSender)
{
	if(PassFormVerify(oForm, oSender, false)==0)
	{
		var f_cn_name = getValueByName(oForm, "_FORM_PF_f_cn_name");
		if(f_cn_name == "项目号"
			|| f_cn_name == "模板号"
			|| f_cn_name == "文档号"
			|| f_cn_name == "文档URL"
			|| f_cn_name == "p_id"
			|| f_cn_name == "t_id"
			|| f_cn_name == "d_id"
			|| f_cn_name == "url"

			|| f_cn_name == "创建者"
			|| f_cn_name == "创建日期"
			|| f_cn_name == "创建年"
			|| f_cn_name == "创建月"
			|| f_cn_name == "创建日"
			
			|| f_cn_name == "创建时间"
			|| f_cn_name == "创建时"
			|| f_cn_name == "创建分"
			|| f_cn_name == "创建秒"
			
			|| f_cn_name == "修改者"
			|| f_cn_name == "修改日期"
			|| f_cn_name == "修改年"
			|| f_cn_name == "修改月"
			|| f_cn_name == "修改日"
			
			|| f_cn_name == "修改时间"
			|| f_cn_name == "修改时"
			|| f_cn_name == "修改分"
			|| f_cn_name == "修改秒"
			
			|| f_cn_name == "发布者"
			|| f_cn_name == "系统当前日期"
			|| f_cn_name == "系统当前时间"
			
			|| f_cn_name == "creator"
			|| f_cn_name == "createdate"
			|| f_cn_name == "createyear"
			|| f_cn_name == "createmonth"
			|| f_cn_name == "createday"
			
			|| f_cn_name == "createtime"
			|| f_cn_name == "createhour"
			|| f_cn_name == "createminute"
			|| f_cn_name == "createsecond"
			
			|| f_cn_name == "mender"
			|| f_cn_name == "menddate"
			|| f_cn_name == "mendyear"
			|| f_cn_name == "mendmonth"
			|| f_cn_name == "mendday"
			
			|| f_cn_name == "mendtime"
			|| f_cn_name == "mendhour"
			|| f_cn_name == "mendminute"
			|| f_cn_name == "mendsecond"
			
			|| f_cn_name == "publisher"
			|| f_cn_name == "publishdate"
			|| f_cn_name == "publishtime")
		{
				alert("提示:模板域名称不能为系统字段！请选择其它名称!");
				oForm.elements["_FORM_PF_f_cn_name"].focus();
				return;
		}
		oForm.submit();
	}
}



//==============================================================================================
//PART I(模板域管理) JavaScript Function
//==============================================================================================


//保存当前的模板域顺序属性值
var vTempdefList_Origin_Orders = new Array();


//----------------------------------------------------------------------------------------------
//提交设置的模板域顺序
//----------------------------------------------------------------------------------------------
function On_TempdefListForm_SetOrderClick(oForm, oSender, action)
{
	oForm.elements["_action"].value = action;
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//将当前模板域表单顺序设置为自然顺序
//----------------------------------------------------------------------------------------------
function On_TempdefListForm_NatureOrderClick(oForm, oSender)
{
	var len = oForm.elements.length;
	var name;
	var prefix;
	var value;
	var j=1;
	var ss;
	for(var i=0; i<len; i++)
	{
		name = oForm.elements[i].name;
		prefix = name.substr(0, "order__of__".length);
		if(prefix == "order__of__")
		{
			if(oSender.checked)
			{
				vTempdefList_Origin_Orders[j] = oForm.elements[i].name + "=" + oForm.elements[i].value;
				oForm.elements[i].value = j;
			}
			else
			{
				ss = vTempdefList_Origin_Orders[j].split("=");
				oForm.elements[ss[0]].value = ss[1];
			}
			j++;
		}
	}
}

//----------------------------------------------------------------------------------------------
//处理模板域的编辑
//----------------------------------------------------------------------------------------------
function On_TempdefListForm_TempdefEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["f_id"];
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
		alert("请选择需要编辑的模板域!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板域!");
		return;
	}
	oForm.action = "main.php?do=tempdef_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//处理模板域的删除
//----------------------------------------------------------------------------------------------
function On_TempdefListForm_TempdefDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["f_id"];
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
		alert("请选择需要删除的模板域!");
		return;
	}
	oForm.action = "main.php?do=tempdef_delsetup";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//处理模板域的全选
//----------------------------------------------------------------------------------------------
function On_TempdefListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["f_id"];
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


//----------------------------------------------------------------------------------------------
//处理是否自动分配模板域字段名
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_AutoFieldName(oFrom ,oSender) 
{
	if(oSender.checked)
	{
		oFrom.elements['_FORM_PF_f_name'].disabled = true;
		oFrom.elements['_FORM_PF_f_name'].style.display = "none";
		oFrom.elements["_FORM_VF__NOTNULL_f_name"].value = 'FALSE';
	}
	else
	{
		oFrom.elements['_FORM_PF_f_name'].disabled = false;
		oFrom.elements['_FORM_PF_f_name'].style.display = "inline";
		oFrom.elements["_FORM_VF__NOTNULL_f_name"].value = 'TRUE';
		oFrom.elements['_FORM_PF_f_name'].focus();
	}
}

//----------------------------------------------------------------------------------------------
//基本属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_BaseSetupClick(oForm, oSender) 
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

//----------------------------------------------------------------------------------------------
//高级属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_AdvancedSetupClick(oForm, oSender) 
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

//----------------------------------------------------------------------------------------------
//数据库高级属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_DBSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_DB.style.display = "inline";
	}
	else
	{
		ID_DB.style.display = "none";
		ID_DBDetail.display = "none";
	}
}

//----------------------------------------------------------------------------------------------
//数据库字段具体属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_DBDetailSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_DBDetail.style.display = "none";
	}
	else
	{
		ID_DBDetail.style.display = "inline";
	}
}

//----------------------------------------------------------------------------------------------
//数据库字段类型事件
//----------------------------------------------------------------------------------------------
function On_TempdefCreateForm_DBTypeChange(oForm, oSender)
{
	var selectedValue = oSender.options[oSender.selectedIndex].value;
	var oDBLength = oForm.elements["_DB_Length"];
	var oDBDefault = oForm.elements["_DB_Default"];
	var oDBValue = oForm.elements["_DB_Value"];
	var oDBUnsigned = oForm.elements["_DB_Unsigned"];
	var oDBBinary = oForm.elements["_DB_Binary"];
	var oDBZeroFill = oForm.elements["_DB_ZeroFill"];

	if(selectedValue == "tinyint"
		|| selectedValue == "smallint"
		|| selectedValue == "mediumint"
		|| selectedValue == "int"
		|| selectedValue == "integer"
		|| selectedValue == "bigint"
		|| selectedValue == "real"
		|| selectedValue == "double"
		|| selectedValue == "float"
		|| selectedValue == "decimal"
		|| selectedValue == "numeric")
	{
		if(selectedValue == "tinyint")
		{
			oDBLength.value = "4";
		}
		else if(selectedValue == "smallint")
		{
			oDBLength.value = "6";
		}
		else if(selectedValue == "mediumint")
		{
			oDBLength.value = "9";
		}
		else if(selectedValue == "int")
		{
			oDBLength.value = "11";
		}
		else if(selectedValue == "integer")
		{
			oDBLength.value = "11";
		}
		else if(selectedValue == "bigint")
		{
			oDBLength.value = "20";
		}
		else if(selectedValue == "real")
		{
			oDBLength.value = "";
		}
		else if(selectedValue == "double")
		{
			oDBLength.value = "";
		}
		else if(selectedValue == "float")
		{
			oDBLength.value = "";
		}
		else if(selectedValue == "decimal")
		{
			oDBLength.value = "10,0";
		}
		else if(selectedValue == "numeric")
		{
			oDBLength.value = "10,0";
		}
		oDBLength.style.backgroundColor = "";
		oDBLength.disabled = false;

		oDBDefault.style.backgroundColor = "";
		oDBDefault.value = "0";
		oDBDefault.disabled = false;

		oDBValue.style.backgroundColor = "darkgray";
		oDBValue.value = "";
		oDBValue.disabled = true;

		oDBUnsigned.disabled = false;
		oDBBinary.disabled = true;
		oDBZeroFill.disabled = false;
	}
	else if(selectedValue == "char")
	{
		oDBLength.value = "100";
		oDBLength.style.backgroundColor = "";
		oDBLength.disabled = false;

		oDBDefault.style.backgroundColor = "";
		oDBDefault.value = "";
		oDBDefault.disabled = false;

		oDBValue.style.backgroundColor = "darkgray";
		oDBValue.value = "";
		oDBValue.disabled = true;

		oDBUnsigned.disabled = true;
		oDBBinary.disabled = false;
		oDBZeroFill.disabled = true;
	}
	else if(selectedValue == "date"
		|| selectedValue == "time"
		|| selectedValue == "datetime"
		|| selectedValue == "year")
	{
		if(selectedValue == "date")
		{
			oDBDefault.value = "0000-00-00";
		}
		else if(selectedValue == "time")
		{
			oDBDefault.value = "00:00:00";
		}
		else if(selectedValue == "datetime")
		{
			oDBDefault.value = "0000-00-00 00:00:00";
		}
		else if(selectedValue == "year")
		{
			oDBDefault.value = "0000";
		}
		oDBLength.value = "";
		oDBLength.style.backgroundColor = "darkgray";
		oDBLength.disabled = true;

		oDBDefault.style.backgroundColor = "";
		oDBDefault.disabled = false;

		oDBValue.style.backgroundColor = "darkgray";
		oDBValue.value = "";
		oDBValue.disabled = true;

		oDBUnsigned.disabled = true;
		oDBBinary.disabled = true;
		oDBZeroFill.disabled = true;
	}
	else if(selectedValue == "timestamp")
	{
		oDBLength.value = "";
		oDBLength.style.backgroundColor = "darkgray";
		oDBLength.disabled = true;

		oDBDefault.style.backgroundColor = "darkgray";
		oDBDefault.value = "";
		oDBDefault.disabled = true;

		oDBValue.style.backgroundColor = "darkgray";
		oDBValue.value = "";
		oDBValue.disabled = true;

		oDBUnsigned.disabled = true;
		oDBBinary.disabled = true;
		oDBZeroFill.disabled = true;
	}
	else if(selectedValue == "tinytext"
		|| selectedValue == "text"
		|| selectedValue == "mediumtext"
		|| selectedValue == "longtext"
		|| selectedValue == "tinyblob"
		|| selectedValue == "blob"
		|| selectedValue == "mediumblob"
		|| selectedValue == "longblob")
	{
		oDBLength.value = "";
		oDBLength.style.backgroundColor = "darkgray";
		oDBLength.disabled = true;

		oDBDefault.style.backgroundColor = "darkgray";
		oDBDefault.value = "";
		oDBDefault.disabled = true;

		oDBValue.style.backgroundColor = "darkgray";
		oDBValue.value = "";
		oDBValue.disabled = true;

		oDBUnsigned.disabled = true;
		oDBBinary.disabled = true;
		oDBZeroFill.disabled = true;
	}
	else if(selectedValue == "enum"
		|| selectedValue == "set")
	{
		oDBLength.value = "";
		oDBLength.style.backgroundColor = "darkgray";
		oDBLength.disabled = true;

		oDBDefault.style.backgroundColor = "";
		oDBDefault.value = "";
		oDBDefault.disabled = false;

		oDBValue.style.backgroundColor = "";
		oDBValue.value = "";
		oDBValue.disabled = false;

		oDBUnsigned.disabled = true;
		oDBBinary.disabled = true;
		oDBZeroFill.disabled = true;
	}
}


//----------------------------------------------------------------------------------------------
//基本属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefEditForm_BaseSetupClick(oForm, oSender) 
{
	On_TempdefCreateForm_BaseSetupClick(oForm, oSender);
}

//----------------------------------------------------------------------------------------------
//高级属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefEditForm_AdvancedSetupClick(oForm, oSender) 
{
	On_TempdefCreateForm_AdvancedSetupClick(oForm, oSender);
}

//----------------------------------------------------------------------------------------------
//数据库高级属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefEditForm_DBSetupClick(oForm, oSender) 
{
	On_TempdefCreateForm_DBSetupClick(oForm, oSender);
}

//----------------------------------------------------------------------------------------------
//数据库字段具体属性事件
//----------------------------------------------------------------------------------------------
function On_TempdefEditForm_DBDetailSetupClick(oForm, oSender) 
{
	if(oSender.checked)
	{
		ID_DBDetail.style.display = "inline";
	}
	else
	{
		ID_DBDetail.style.display = "none";
	}
}

//----------------------------------------------------------------------------------------------
//数据库字段类型事件
//----------------------------------------------------------------------------------------------
function On_TempdefEditForm_DBTypeChange(oForm, oSender)
{
	On_TempdefCreateForm_DBTypeChange(oForm, oSender);
}


//----------------------------------------------------------------------------------------------
//模板域删除
//----------------------------------------------------------------------------------------------
function On_TempdefDeleteForm_SubmitClick(oForm, oSender)
{
	if(confirm("请确认是否真的删除?"))
	{
		oForm.submit();
	}
}


//==============================================================================================
//PART III(模板设计相关) JavaScript Function
//==============================================================================================

	
//是否预览过改模板
var ifPreviewed = false;

//----------------------------------------------------------------------------------------------
//处理宏的拷贝
//----------------------------------------------------------------------------------------------
function On_TmplDesignForm_MacroCopyClick(oForm, oSender)
{
		oForm.elements["copy_to"].value = oSender.options[oSender.selectedIndex].value;
}


//----------------------------------------------------------------------------------------------
//处理模板编辑时宏的拷贝
//----------------------------------------------------------------------------------------------
function On_TmplEditForm_MacroCopyClick(oForm, oSender)
{
	var f_name = oSender.options[oSender.selectedIndex].value;
	var f_object = "document.myform.elements['_FORM_PF_" + f_name + "']";
	oForm.elements["copy_to"].value = f_object;
}



//----------------------------------------------------------------------------------------------
//处理模板的提交预览
//----------------------------------------------------------------------------------------------
function On_TmplDesignForm_SubmitClick(oForm, oSender)
{
	if(!ifPreviewed)
	{
		if(confirm("你还没有预览模板!确定提交吗?") == false)
		{
			return false;
		}
	}
	oForm.submit();
}


//==============================================================================================
//PART IV(模板调用相关)
//==============================================================================================


//----------------------------------------------------------------------------------------------
//处理调用列表的选择
//----------------------------------------------------------------------------------------------
function On_TmplCallListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["call_id"];
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

//----------------------------------------------------------------------------------------------
//处理调用顺序的设置
//----------------------------------------------------------------------------------------------
function On_TmplCallListForm_SetOrderClick(oForm, oSender, action)
{
	oForm.elements["_action"].value = action;
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板调用的编辑
//----------------------------------------------------------------------------------------------
function On_TmplCallListForm_CallEditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["call_id"];
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
		alert("请选择需要编辑的模板调用!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板调用!");
		return;
	}
	oForm.action = "main.php?do=tmpl_call_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板调用的删除
//----------------------------------------------------------------------------------------------
function On_TmplCallListForm_CallDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["call_id"];
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
		alert("请选择需要删除的模板调用!");
		return;
	}
	if(confirm("确认是否真的删除?"))
	{
		oForm.elements["_action"].value = "delete";
		oForm.submit();
	}
}

//----------------------------------------------------------------------------------------------
//处理模板调用类型的切换
//----------------------------------------------------------------------------------------------
function On_TmplCallCreateForm_CallMethodChange(oForm, oSender)
{
	var method = oSender.options[oSender.selectedIndex].value;
	if(method == "00")
	{
		ID_CGI_CALL.style.display = "inline";
		ID_EMAIL_CALL.style.display = "none";

		oForm.elements["_VF__NOTNULL_call_cgi"].value = "TRUE";
		oForm.elements["_VF__NOTNULL_email_list"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_subject"].value = "FALSE";
	}
	else if(method == "01")
	{
		ID_CGI_CALL.style.display = "none";
		ID_EMAIL_CALL.style.display = "inline";
		
		oForm.elements["_VF__NOTNULL_call_cgi"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_email_list"].value = "TRUE";
		oForm.elements["_VF__NOTNULL_subject"].value = "TRUE";
	}
}

//----------------------------------------------------------------------------------------------
//处理是否需要HTTP授权切换
//----------------------------------------------------------------------------------------------
function On_TmplCallCreateForm_IfAuthChange(oForm, oSender)
{
	var if_need_auth = oSender.options[oSender.selectedIndex].value;
	if(if_need_auth == "TRUE")
	{
		ID_AUTH_USER.style.display = "inline";
		ID_AUTH_PWD.style.display = "inline";

		oForm.elements["_VF__NOTNULL_auth_user"].value = "TRUE";
		oForm.elements["_VF__NOTNULL_auth_pwd"].value = "TRUE";
	}
	else if(if_need_auth == "FALSE")
	{
		ID_AUTH_USER.style.display = "none";
		ID_AUTH_PWD.style.display = "none";

		oForm.elements["_VF__NOTNULL_auth_user"].value = "FALSE";
		oForm.elements["_VF__NOTNULL_auth_pwd"].value = "FALSE";
	}
}


//----------------------------------------------------------------------------------------------
//编辑页面刷新调用
//----------------------------------------------------------------------------------------------
function On_TmplCallEditForm_RefreshClick(oForm, oSender, cgi)
{
	self.open(cgi, "_self");
}


//----------------------------------------------------------------------------------------------
//增加CGI参数时调用
//----------------------------------------------------------------------------------------------
function On_TmplCallEditForm_CGIParamAddClick(oForm, oSender)
{
	var txtParamName = oForm.elements["_ParamName"];
	var txtParamValue = oForm.elements["_ParamValue"];
	if(txtParamName.value == "")
	{
		alert("请填写参数名称!");
		txtParamName.focus();
		return;
	}
	if(txtParamValue.value == "")
	{
		alert("请填写参数值!");
		txtParamValue.focus();
		return;
	}
	oForm.elements["_action"].value = "AddParam";
	oForm.action = "main.php?do=tmpl_call_edit";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//更新CGI参数时调用
//----------------------------------------------------------------------------------------------
function On_TmplCallEditForm_CGIParamUpdateClick(oForm, oSender)
{
	oForm.elements["_action"].value = "UpdateParams";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//删除CGI参数时调用
//----------------------------------------------------------------------------------------------
function On_TmplCallEditForm_CGIParamDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["param_id"];
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
		alert("请选择需要删除的参数列表!");
		return;
	}
	oForm.elements["_action"].value = "DeleteParams";
	oForm.submit();
}

//----------------------------------------------------------------------------------------------
//选择(全选)CGI参数时调用
//----------------------------------------------------------------------------------------------
function On_TmplCallEditForm_SelectAllParamClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["param_id"];
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

//----------------------------------------------------------------------------------------------
//处理模板分级别表的选择
//----------------------------------------------------------------------------------------------
function On_TmplMergeListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tmd_id"];
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

//----------------------------------------------------------------------------------------------
//处理模板分级表分级顺序的设置
//----------------------------------------------------------------------------------------------
function On_TmplMergeListForm_SetOrderClick(oForm, oSender, action)
{
	oForm.elements["_action"].value = action;
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模分级逻辑表的创建工作
//----------------------------------------------------------------------------------------------
function On_TmplMergeListForm_CreateClick(oForm, oSender)
{
	oForm.elements["_action"].value = "create_merge_tbl";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板分级表的编辑
//----------------------------------------------------------------------------------------------
function On_TmplMergeListForm_EditClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tmd_id"];
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
		alert("请选择需要编辑的模板分级表!");
		return;
	}
	else if(j != 1)
	{
		alert("一次只能编辑一个模板分级表!");
		return;
	}
	oForm.action = "main.php?do=tmpl_merge_edit";
	oForm.elements["_action"].value = "";
	oForm.submit();
}


//----------------------------------------------------------------------------------------------
//处理模板分级表的删除
//----------------------------------------------------------------------------------------------
function On_TmplMergeListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["tmd_id"];
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
		alert("请选择需要删除的模板分级表!");
		return;
	}
	if(confirm("确认是否真的删除?"))
	{
		oForm.elements["_action"].value = "delete";
		oForm.submit();
	}
}


//----------------------------------------------------------------------------------------------
// 处理模板发布视图的预览
// 参数:
//		oForm:当前的表单对象
//		idTextArea:需要预览的对象ID
//		idIFrame:提供预览的内部帧对象ID
//		oSender:事件发送者
//----------------------------------------------------------------------------------------------
function On_TemplateView_Click(oForm, idTextArea, idIFrame, p_id, t_id, pm_id, oSender)
{
	if(oSender.checked)
	{
		var url;
		url = "main.php?do=tmpl_pubview&p_id=" + p_id +"&t_id=" + t_id + "&pm_id="+ pm_id;
		oForm.elements[idTextArea].style.display = "none";
		document.all(idIFrame).style.display = "block";
		document.frames(idIFrame).location = url;
	}
	else
	{
		oForm.elements[idTextArea].style.display = "inline";
		document.all(idIFrame).style.display = "none";
	}
}
