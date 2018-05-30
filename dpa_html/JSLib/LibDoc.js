//==============================================================================================
// Common JavaScript Function
//==============================================================================================


//-------------------------------------------------------------
//	选择图片上传时如果图片超出规范，提醒用户
//	图片规范请参考:
//	计算公式: 
//	--------------------------------------------------------------------------------
//	K=1.80 * (l * s/5800) (当l≥300) 许可误差9.0k
//	K=1.65 * (l * s/3200) (当200≤l<300) 许可误差4.0k
//	K=1.50 * (l * s/2000) (当100≤l<200) 许可误差2.0k
//	K=1.40 * (l * s/1800) (当50≤l<100) 许可误差1.0k
//	K=1.25 * (l * s/1300) (当l<50) 许可误差0.5k
//	
//	注：l=图片长边
//		s=图片短边
//		K=图片大小（k）
//-------------------------------------------------------------
function CheckImageSize(oForm, oSender)
{
	// 先判断当前的图片是否绝对链接
	var re = new RegExp(/^[a-zA-Z]+:\/\//i);
	var file = oSender.value;
	var img = null;
	if(!file.match(re)){
		img = document.createElement("img");
		img.style.position = "absolute";
		img.style.visibility = "hidden";
		img.attachEvent("onreadystatechange", ShowImageInfo);
		document.body.insertAdjacentElement("beforeend",img);
		img.src = file;
	}

	function ShowImageInfo()
	{
		var fileSize = parseInt(img.fileSize) / 1024.0;
		fileSize = fileSize.toFixed(1);
		var l;
		var s;
		var width = img.width;
		var height = img.height;
		var calFileSize;
		var valid = 1;
		if(fileSize > 0)
		{
			if(width > height){
				l = width;
				s = height;
			}
			else{
				l = height;
				s = width;
			}
			if(l < 50){
				calFileSize = 1.25 * (l * s/1300);
				if(fileSize - calFileSize > 0.5){
					valid = 0;
					calFileSize = calFileSize + 0.5;
				}
			}
			else if(l < 100){
				calFileSize = 1.40 * (l * s/1800);
				if(fileSize - calFileSize > 1.0){
					valid = 0;
					calFileSize = calFileSize + 1.0;
				}
			}
			else if(l < 200){
				calFileSize = 1.50 * (l * s/2000);
				if(fileSize - calFileSize > 2.0){
					valid = 0;
					calFileSize = calFileSize + 2.0;
				}
			}
			else if(l < 300){
				calFileSize = 1.65 * (l * s/3200);
				if(fileSize - calFileSize > 4.0){
					valid = 0;
					calFileSize = calFileSize + 4.0;
				}
			}
			else{
				calFileSize = 1.80 * (l * s/5800);
				if(fileSize - calFileSize > 9.0){
					valid = 0;
					calFileSize = calFileSize + 9.0;
				}
			}
			calFileSize = calFileSize.toFixed(1);
			if(valid == 0){
				alert("警告!图像尺寸(像素): " + img.width + " X " + img.height + "  文件大小: " + fileSize +"K" + " 规范尺寸:<=" + calFileSize + "K");
			}
		}
	}
}

function DisableAllRedundantElements(oForm)
{
	/*
	var length = oForm.elements.length;
	var field;
	var obj;
	var FIELD_NAME;
	var VF_NOTNULL_ELE_NAME;
	var VF_NOTNULL_ELE_OBJ;
	var VF_MIN_LENGTH_ELE_NAME;
	var VF_MIN_LENGTH_ELE_OBJ;
	var VF_MAX_LENGTH_ELE_NAME;
	var VF_MAX_LENGTH_ELE_OBJ;
	var VF_FTR_ELE_NAME;
	var VF_FTR_ELE_OBJ;
	var FCR_ELE_NAME;
	var FCR_ELE_OBJ;
	var FDV_ELE_NAME;
	var FDV_ELE_OBJ;
	for (var i=0; i<length; i++)
	{
		obj = oForm.elements[i];
		field = new FormField(oForm, obj);
		if (!field.isDictField())
		{
			continue;
		}
		FIELD_NAME = field.FieldName();
		VF_NOTNULL_ELE_NAME = DICT_VERIFY_NOT_NULL_PREFIX + FIELD_NAME;
		VF_NOTNULL_ELE_OBJ = oForm.elements[VF_NOTNULL_ELE_NAME];

		VF_MIN_LENGTH_ELE_NAME = DICT_VERIFY_MIN_LEN_PREFIX + FIELD_NAME;
		VF_MIN_LENGTH_ELE_OBJ = oForm.elements[VF_MIN_LENGTH_ELE_NAME];

		VF_MAX_LENGTH_ELE_NAME = DICT_VERIFY_MAX_LEN_PREFIX + FIELD_NAME;
		VF_MAX_LENGTH_ELE_OBJ = oForm.elements[VF_MAX_LENGTH_ELE_NAME];

		VF_FTR_ELE_NAME = DICT_VERIFY_TYPE_PREFIX + FIELD_NAME;
		VF_FTR_ELE_OBJ = oForm.elements[VF_FTR_ELE_NAME];

		FCR_ELE_NAME = DICT_FIELD_CNAME_PREFIX + FIELD_NAME;
		FCR_ELE_OBJ = oForm.elements[FCR_ELE_NAME];

		FDV_ELE_NAME = DICT_FIELD_DEFAULT_VALUE_PREFIX + FIELD_NAME;
		FDV_ELE_OBJ = oForm.elements[FDV_ELE_NAME];

		alert(FIELD_NAME);	

		alert(VF_NOTNULL_ELE_NAME);
		alert(VF_NOTNULL_ELE_OBJ);

		alert(VF_MIN_LENGTH_ELE_NAME);
		alert(VF_MIN_LENGTH_ELE_OBJ);

		alert(VF_MAX_LENGTH_ELE_NAME);
		alert(VF_MAX_LENGTH_ELE_OBJ);

		alert(VF_FTR_ELE_NAME);
		alert(VF_FTR_ELE_OBJ);

		alert(FCR_ELE_NAME);
		alert(FCR_ELE_OBJ);

		alert(FDV_ELE_NAME);
		alert(FDV_ELE_OBJ);
		break;
	}
	*/
}

//==============================================================================================
// 文档列表表单(main.php?do=document_list) JavaScript Function
//==============================================================================================


function On_DocumentListForm_PlusQueryClick(oForm, oSender)
{
	var iTotalSearchCount = oForm.elements["_search_field_total_count"].value;
	var iStartIdx = parseInt(oSender.value);
	var oEntryObj;
	var search_field;
	var search_method;
	var search_value;
	var search_concat;
	var plus_query;
	if(oSender.checked)
	{
			oForm.elements["_search_field_count"].value = iStartIdx;
			oEntryObj = "ID_QUERY_UNIT_" + iStartIdx;
			oForm.all(oEntryObj).style.display = "inline";
			search_field = "_search_field_" + iStartIdx;
			search_method = "_search_method_" + iStartIdx;
			search_value = "_search_value_" + iStartIdx;
			search_concat = "_search_concat_" + iStartIdx;
			oForm.elements[search_field].disabled = false;
			oForm.elements[search_method].disabled = false;
			oForm.elements[search_method].disabled = false;
			oForm.elements[search_concat].disabled = false;
	}
	else
	{
		oForm.elements["_search_field_count"].value = iStartIdx - 1;
		for(var i = iStartIdx; i <= iTotalSearchCount; i++)
		{
			oEntryObj = "ID_QUERY_UNIT_" + i;
			oForm.all(oEntryObj).style.display = "none";
			search_field = "_search_field_" + i;
			search_method = "_search_method_" + i;
			search_value = "_search_value_" + i;
			search_concat = "_search_concat_" + i;
			oForm.elements[search_field].disabled = true;
			oForm.elements[search_method].disabled = true;
			oForm.elements[search_method].disabled = true;
			oForm.elements[search_concat].disabled = true;
		}
		for(var i = iStartIdx; i < iTotalSearchCount; i++)
		{
			plus_query = "_plus_query_" + i;
			oForm.elements[plus_query].checked = false;
		}
	}
}

//-------------------------------------------------------------
//文档全选
//-------------------------------------------------------------
function On_DocumentListForm_SelectAllClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
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
//	选择文档修改时调用
//-------------------------------------------------------------
function On_DocumentListForm_EditClick(oForm, oSender)
{
	var p_id = oForm.elements["p_id"].value;
	var t_id = oForm.elements["t_id"].value;
	var oCheckboxColl = oForm.elements["d_id"];
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要编辑的文档!");
		return;
	}
	if(j > 1)
	{
		alert("一次只能修改一条文档!");
		return;
	}
	var cgi_url =  "main.php?do=document_edit&p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	window.self.open(cgi_url, "_self");
}


//-------------------------------------------------------------
//	选择文档拒签时调用
//	杨明辉2004-06-09，用途：拒签文档时需登记原因
//-------------------------------------------------------------
function On_DocumentListForm_RejectClick(oForm, oSender)
{
	//取消拒签
	if(oSender.name == "_rej_cancel")
	{
		oForm.all("ID_REJECT_REASON").style.display = "none";
		oForm.elements["_rej_reason"].disabled = true;
		return;
	}
			
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要拒签的文档!");
		return;
	}
	
	//拒签确认
	if(oSender.name == "_rej_confirm")
	{
		if(oForm.elements["_rej_reason"].value == "")
		{
			alert("请填写拒签原因!");
			oForm.elements["_rej_reason"].focus();
			return;
		}
		oForm.action = "main.php?do=document_publish";
		oForm.elements["_action"].value = "reject";
		oForm.submit();
		return;
	}
	
	//填写拒签原因
	oForm.all("ID_REJECT_REASON").style.display = "inline";
	oForm.elements["_rej_reason"].disabled = false;
}


//-------------------------------------------------------------
//	选择文档删除时调用
//	杨明辉2004-2-24修改，用途：删除文档时需登记原因
//	原函数备份为‘On_DocumentListForm_DeleteClick_BAK’
//-------------------------------------------------------------
function On_DocumentListForm_DeleteClick(oForm, oSender)
{
	//取消删除
	if(oSender.name == "_del_cancel")
	{
		document.getElementById("ID_DELETE_REASON").style.display = "none";
		oForm.elements["_del_reason"].disabled = true;
		return;
	}
			
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	
	//删除确认
	if(oSender.name == "_del_confirm")
	{
		if(oForm.elements["_del_reason"].value == "")
		{
			alert("请填写删除原因!");
			oForm.elements["_del_reason"].focus();
			return;
		}
		//-------------------------------------------------------------------
		//删除文档时不发布
		//-------------------------------------------------------------------
		if(! oForm.elements["_del_not_publish"].checked)
		{
			//---------------------------------------------------------------
			//采用标记删除方式
			//---------------------------------------------------------------
			if(oForm.elements["_marked_delete_mode"].checked)
			{
				oForm.elements["_action"].value = "marked_delete_publish";
			}
			else
			{
				oForm.elements["_action"].value = "delete_publish";
			}
			oForm.action = "main.php?do=document_publish";
		}
		else
		{
			oForm.elements["_action"].value = "delete";
		}
		oForm.submit();
		return;
	}
	
	//填写删除原因
	document.getElementById("ID_DELETE_REASON").style.display = "inline";
	oForm.elements["_del_reason"].disabled = false;
	oForm.elements["_del_terminal"].disabled = false;
}

//-------------------------------------------------------------
//	选择文档删除时调用BAK
//-------------------------------------------------------------
function On_DocumentListForm_DeleteClick_BAK(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	if(prompt("请确定是否删除！(yes/no)", "no") == "yes")
	{

		//-------------------------------------------------------------------
		//删除文档时不发布
		//-------------------------------------------------------------------
		if(! oForm.elements["_del_not_publish"].checked)
		{
			//---------------------------------------------------------------
			//采用标记删除方式
			//---------------------------------------------------------------
			if(oForm.elements["_marked_delete_mode"].checked)
			{
				oForm.elements["_action"].value = "marked_delete_publish";
			}
			else
			{
				oForm.elements["_action"].value = "delete_publish";
			}
			oForm.action = "main.php?do=document_publish";
		}
		else
		{
			oForm.elements["_action"].value = "delete";
		}
		oForm.submit();
	}
}



//-------------------------------------------------------------
//	回收删除的文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_ReclaimClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要回收的文档!");
		return;
	}
	oForm.elements["_action"].value = "reclaim";
	oForm.submit();
}




//-------------------------------------------------------------
//	同步文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_SyncClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要同步的文档!");
		return;
	}
	oForm.action = "main.php?do=document_sync";
	oForm.submit();
}

//-------------------------------------------------------------
//	预览文档时调用
//-------------------------------------------------------------
function On_DocumentListForm_PreviewClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
	
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要预览的文档!");
		return;
	}
	oForm.elements["_action"].value = "preview_publish";
	oForm.action = "main.php?do=document_publish";
	oForm.submit();
}


//-------------------------------------------------------------
//	选择文档发布时调用
//-------------------------------------------------------------
function On_DocumentListForm_PublishClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要发布的文档!");
		return;
	}
	oForm.elements["_action"].value = "publish";
	oForm.action = "main.php?do=document_publish";
	oForm.submit();
}

//-------------------------------------------------------------
//	出来文档跨项目发往新闻中心button
//-------------------------------------------------------------
function On_DocumentListForm_CrossClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["d_id"];
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
					d_id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				d_id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要发布的文档!");
		return;
	}
	oForm.elements["_action"].value = "list";
	oForm.action = "main.php?do=jczs2dailynews";
	//oForm.target = "_blank";
	oForm.submit();
}

//----------------------------------------
//响应"文档列表"Button
//----------------------------------------
function checkDocumentListForm_SearchSubmit(form, obj)
{
	if(event.keyCode == 13)
	{
		obj.click();
	}
}

//-------------------------------------------------------------
//开始检索
//-------------------------------------------------------------
function On_DocumentListForm_SearchClick(oForm, oSender)
{
	oForm.elements["_search_type"].value = "publish_db";
	return On_DocumentListForm_FirstPageClick(oForm, oSender);
}

//-------------------------------------------------------------
//开始快速检索
//-------------------------------------------------------------
function On_DocumentListForm_quickSearchClick(oForm, oSender)
{
	oForm.elements["_search_type"].value = "search_engine";
	return On_DocumentListForm_FirstPageClick(oForm, oSender);
}

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_DocumentListForm_FirstPageClick(oForm, oSender)
{
	oForm.elements["_goto_page"].value = "0";
	oForm.submit();
}


//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_DocumentListForm_Init(oForm, oSender)
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
function On_DocumentListForm_PrevPageClick(oForm, oSender)
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
function On_DocumentListForm_NextPageClick(oForm, oSender)
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

//==============================================================================================
// 文档创建表单(main.php?do=document_add) JavaScript Function
//==============================================================================================


//-------------------------------------------------------------
//添加表单时手工指定文档创建日期及时间时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_SpecCreateTimeClick(oForm, oSender)
{
	var oIfSpecialCreateTime = oForm.elements["special_createtime"];
	if(oSender.checked)
	{
		var now  =  new  Date();  
		var year =  now.getYear();  
		var mon  =  now.getMonth()+1;
		var day  =  now.getDate();

		var  hh  =  now.getHours();  
		var  mm  =  now.getMinutes();  
		var  ss  =  now.getTime()  %  60000;  
		ss  =  (ss  -  (ss  %  1000))  /  1000;  
		var  clock  =  hh+':';  
		if  (mm  <  10)  clock  +=  '0';  
		clock  +=  mm+':';  
		if  (ss  <  10)  clock  +=  '0';  
		clock  +=  ss;  

		var cur_dt = year + '-' + mon + '-' + day + ' ' + clock;

		oIfSpecialCreateTime.style.backgroundColor = "";
		oIfSpecialCreateTime.value = cur_dt;
		oIfSpecialCreateTime.disabled = false;
		oIfSpecialCreateTime.focus();
	}
	else
	{
		oIfSpecialCreateTime.style.backgroundColor = "darkgray";
		oIfSpecialCreateTime.value = "";
		oIfSpecialCreateTime.disabled = true;
	}
}


//-------------------------------------------------------------
//添加表单时手工指定文档的到期日期及时间时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_SpecExpiredDateClick(oForm, oSender)
{
	var oIfSpecialExpiredDate = oForm.elements["special_expireddate"];
	if(oSender.checked)
	{
		oIfSpecialExpiredDate.style.backgroundColor = "";
		oIfSpecialExpiredDate.value = "";
		oIfSpecialExpiredDate.disabled = false;
		oIfSpecialExpiredDate.focus();
	}
	else
	{
		oIfSpecialExpiredDate.style.backgroundColor = "darkgray";
		oIfSpecialExpiredDate.value = "";
		oIfSpecialExpiredDate.disabled = true;
	}
}




//-------------------------------------------------------------
//仅仅新建文档时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_InsertClick(oForm, oSender)
{
	/*if (AlertKeywords(oForm))
	{
		return false;
	}
	if(PassFormVerify(oForm, oSender))
	*/
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	//if(PassFormVerify(oForm, oSender, true))
	else
	{
		oForm.elements["_action"].value = "insert";

		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);

		oForm.submit();
		return false;
	}
}


//-------------------------------------------------------------
//新建并发布文档时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_PublishClick(oForm, oSender)
{
	//初始化发往专题、栏目信息
	if(oForm.t_type.value == '01')
	{
		//alert("In Get_PublishToTarget!Return !");
		//是文章模板,处理发往...
		//Get_PublishToTarget();
	}	
	dateObj1 = new Date();
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
        {
                return false;
        }
	//else if(PassJSVerify(oForm, oSender))
	else
	{
		if (PassFormVerifyValue == 1)
		{
			oForm.elements["_action"].value = "insert";
		}
		else 
		{
			oForm.elements["_action"].value = "insert_publish";
		}

		if(PassJSVerify(oForm, oSender))
		{
			//禁止发送所有服务器CGI不关心的数据元素
			DisableAllRedundantElements(oForm);

			// 检查URL看是否符合规则
			if(CheckURL(oForm, oSender))
			{
				oForm.submit();
			}
		}

	}
}


//-------------------------------------------------------------
//新建文档并发布预览时调用
//-------------------------------------------------------------
function On_DocumentCreateForm_PreviewClick(oForm, oSender)
{
	
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);	
	if (PassFormVerifyValue == 2)
        {
                return false;
        }
	else 
	{
		if (PassFormVerifyValue == 1)
		{
			oForm.elements["_action"].value = "insert";
		}
		else 
		{
			oForm.elements["_action"].value = "insert_publish_preview";
		}
		//oForm.elements["_action"].value = "insert_publish_preview";

		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);

		oForm.submit();
	}
}



//-------------------------------------------------------------
// 用途:在默认URL模板和引用外部文章外部文章之间切换
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		pm_id：当前样式ID
//-------------------------------------------------------------

function On_DocumentCreateForm_PolymSelectClick(oForm, oSender, pm_id)
{
	var value = oSender.value;
	var inner_url;
	var outer_url;
	var ref_url;
	ref_url = "ref_url" + "_" + pm_id;
	inner_url = "inner_url" + "_" + pm_id;
	outer_url = "outer_url" + "_" + pm_id;
	if(value == "inner")
	{
		oForm.elements[inner_url].disabled = false;
		oForm.elements[inner_url].style.backgroundColor = "";
		oForm.elements[outer_url].disabled = true;
		oForm.elements[outer_url].style.backgroundColor = "darkgray";
		oForm.elements[inner_url].focus();
	}
	else if(value == "outer")
	{
		oForm.elements[inner_url].disabled = true;
		oForm.elements[inner_url].style.backgroundColor = "darkgray";
		oForm.elements[outer_url].disabled = false;
		oForm.elements[outer_url].style.backgroundColor = "";
		oForm.elements[outer_url].focus();
	}
}

//-------------------------------------------------------------
// 用途:样式发布切换时调用
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		pm_id：当前样式ID
//-------------------------------------------------------------
function On_DocumentCreateForm_IfPublishClick(oForm, oSender, pm_id)
{
	var inner_url;
	var outer_url;
	var ref_url;
	ref_url = "ref_url" + "_" + pm_id;
	inner_url = "inner_url" + "_" + pm_id;
	outer_url = "outer_url" + "_" + pm_id;
	if(oSender.checked)
	{
		oForm.elements[ref_url](0).disabled = false;
		oForm.elements[ref_url](1).disabled = false;

		if(oForm.elements[ref_url](0).checked)
		{
			oForm.elements[inner_url].disabled = false;
			oForm.elements[inner_url].style.backgroundColor = "";
		}

		if(oForm.elements[ref_url](1).checked)
		{
			oForm.elements[outer_url].disabled = false;
			oForm.elements[outer_url].style.backgroundColor = "";
		}
	}
	else
	{
		oForm.elements[ref_url](0).disabled = true;
		oForm.elements[ref_url](1).disabled = true;
		oForm.elements[inner_url].disabled = true;
		oForm.elements[outer_url].disabled = true;
		oForm.elements[inner_url].style.backgroundColor = "darkgray";
		oForm.elements[outer_url].style.backgroundColor = "darkgray";
	}
}

//-------------------------------------------------------------
// 用途:文档栏目、专题等选择发往时调用
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//-------------------------------------------------------------
var PublishTo_win = null;

function On_DocumentEditForm_SendToClick(oForm, oSender)
{
	//不支持修改文档时的操作
	alert("不支持编辑文档时的发往操作");
	return;
}


function On_DocumentCreateForm_SendToClick(oForm, oSender)
{
	if(oForm.t_type.value != '01')
	{
		//不是文章模板
		return;
	}	
	Get_PublishToTargetModal();
	return;
	
	//parent.room.cols = "145,*,145";
	//调用父窗口方法以保留全局变量
	if(top.frmTop != null){
		top.frmTop.On_DocumentCreateForm_SendToClick2();
	}else{
		//open Modal window
		Get_PublishToTargetModal();
	}
	return;	

}

//top窗口变量PublishTo_win
var PublishTo_win = null;
function On_DocumentCreateForm_SendToClick2()
{
	var cgi = "/gsps/frmPublishTo.html";
	if(!PublishTo_win || PublishTo_win.closed){
		var property = "scrollbars=yes,height=550,width=300,toolbar=no,menubar=no,location=no,left=300,top=60,screenX=10,screenY=10";
		PublishTo_win = window.open(cgi, "PublishToWin", property);
	}
	else
	{
		PublishTo_win.moveTo(300,60)
		PublishTo_win.focus();
		PublishTo_win.nullText();
	}
}

//-------------------------------------------------------------
// 用途:获取发往的栏目、专题信息
// 参数:
//-------------------------------------------------------------
function Get_PublishToTarget()
{
	//调用父窗口方法以保留全局变量
	if(top.frmTop != null){
		top.frmTop.Get_PublishToTarget2();
	}else{
		//Get_PublishToTargetModal();
	}
}

function Get_PublishToTarget2()
{
	//top窗口变量PublishTo_win
	if(!PublishTo_win || PublishTo_win.closed){
	}
	else
	{
		top.frmPanel.frmCenter.document.myform.elements['subject_target'].value = PublishTo_win.getSubjectTarget();
		top.frmPanel.frmCenter.document.myform.elements['column_target'].value = PublishTo_win.getColumnTarget();
		top.frmPanel.frmCenter.document.myform.elements['daemon'].value = PublishTo_win.getDaemonFlag();
		//只使用一次,但必须使用一次
		PublishTo_win.nullText();
	}
}

function Get_PublishToTargetModal()
{
        var out;
        out = window.showModalDialog("/document/tech/pc/subject/list/index.html",out,"dialogHeight: 550px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
        
        if(out != null && out[0] == "true")
        {
                self.document.myform.subject_target.value = out[1];
                self.document.myform.column_target.value = out[2];
                self.document.myform.daemon.value = out[3];
        }       
}


//==============================================================================================
// 文档修改表单(main.php?do=document_edit) JavaScript Function
//==============================================================================================



//-------------------------------------------------------------
//修改表单时手工指定文档创建日期及时间时调用
//-------------------------------------------------------------
function On_DocumentEditForm_SpecCreateTimeClick(oForm, oSender)
{
	return  On_DocumentCreateForm_SpecCreateTimeClick(oForm, oSender);
}


//-------------------------------------------------------------
//修改表单时手工指定文档的到期日期及时间时调用
//-------------------------------------------------------------
function On_DocumentEditForm_SpecExpiredDateClick(oForm, oSender)
{
	return On_DocumentCreateForm_SpecExpiredDateClick(oForm, oSender);
}




//-------------------------------------------------------------
//仅仅修改文档时调用
//-------------------------------------------------------------
function On_DocumentEditForm_UpdateClick(oForm, oSender)
{
	/*if (AlertKeywords(oForm))
	{
		return false;
	}
	if(PassFormVerify(oForm, oSender))
	*/
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
        if (PassFormVerifyValue == 2)
        {
                return false;
        }
	else
	{
		oForm.elements["_action"].value = "update";

		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);


		oForm.submit();
	}
}

//-------------------------------------------------------------
//修改并发布文档时调用
//-------------------------------------------------------------
function On_DocumentEditForm_PublishClick(oForm, oSender)
{
	//if(PassJSVerify(oForm, oSender))
	//if(PassFormVerify(oForm, oSender) && PassJSVerify(oForm, oSender))
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
	{
		return false;
	}
	//else if (PassFormVerifyValue != 2 && PassJSVerify(oForm, oSender))
	else if (PassFormVerifyValue != 2)
	{
		if (PassFormVerifyValue == 1)
		{
			oForm.elements["_action"].value = "update";
		}
		else
		{
			oForm.elements["_action"].value = "update_publish";
		}
		
		if(PassJSVerify(oForm, oSender))
		{
			//禁止发送所有服务器CGI不关心的数据元素
			DisableAllRedundantElements(oForm);

			// 检查URL看是否符合规则
			if(CheckURL(oForm, oSender))
			{
				oForm.submit();
			}
		}
	}
}



//-------------------------------------------------------------
// 开始检查文档URL是否符合规则
//-------------------------------------------------------------
function CheckURL(oForm, oSender)
{
	var pm_id;
	var ref_url;
	var chk_pm_id = oForm.elements["pm_id"];
	if(chk_pm_id != null)
	{
		var len = chk_pm_id.length;
		if(len != null)
		{
			for(var i=0; i<len; i++)
			{
				pm_id = chk_pm_id[i].value;
				ref_url = "ref_url_" + pm_id;
				if(!CheckRefURL(oForm, oForm.elements[ref_url], pm_id))
				{
					return false;
				}
			}
		}
		else
		{
			pm_id = chk_pm_id.value;
			ref_url = "ref_url_" + pm_id;
			if(!CheckRefURL(oForm, oForm.elements[ref_url], pm_id))
			{
				return false;
			}
		}
	}
	return true;
}



//-------------------------------------------------------------
// 开始检查文档URL是否符合规则
//-------------------------------------------------------------
function CheckRefURL(oForm, oRefUrl, pm_id)
{
	var ref_url;
	var url_name;
	for(var i=0; i<oRefUrl.length; i++)
	{
		if(oRefUrl[i].checked)
		{
			ref_url = oRefUrl[i].value;
			if(ref_url == "inner")
			{
				url_name = "inner_url_" + pm_id;
			}
			else
			{
				url_name = "outer_url_" + pm_id;
			}
			if(!CheckURLRule(oForm, url_name))
			{
				alert("错误:指定的URL不合法!请参照正确的语法填写!\n规范:URL只能由[a-zA-Z0-9.-_/]等字符组成,不能包含汉字,空格等非法字符!");
				oForm.elements[url_name].focus();
				oForm.elements[url_name].select();
				return false;
			}
		}
	}
	return true;

}


//-------------------------------------------------------------
// 检查文档的URL是否符合规则
// URL规则定义如下：
//	URL只能由如下字符构成	
//		a-zA-Z0-9
//		-_./
//		${XXX}
//		
//-------------------------------------------------------------
function CheckURLRule(oForm, url_name)
{
	var i;
	var c;
	var code;
	var Url = oForm.elements[url_name].value;
	
	//先检查给定的URL是否是绝对链接
	var re = new RegExp("^[a-zA-Z]{3,}:\\/\\/.*", "i");
	if(Url.match(re))
	{
		return true;
	}
	
	//先将${XXX}替换为空串,不参与检查
	Url = Url.replace(/\$\{[^\}]*\}/g, "");

	for(i=0; i<Url.length; i++)
	{
		code = Url.charCodeAt(i);
		if(code > 127)
		{
			return false;
		}
		c = Url.charAt(i);
		if(!isalpha(c) 
			&& !isdigit(c)
			&& c != '.'
			&& c != ','
			&& c != '-'
			&& c != '_'
			&& c != '/')
		{
			return false;
		}
	}
	return true;
}

//-------------------------------------------------------------
//检查c是否字母
//-------------------------------------------------------------
function isalpha(c)
{
	if((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z'))
	{
		return true;
	}
	return false;
}

//-------------------------------------------------------------
//检查c是否数字
//-------------------------------------------------------------
function isdigit(c)
{
	if(c >= '0' && c <= '9')
	{
		return true;
	}
	return false;
}


//-------------------------------------------------------------
//修改文档并发布预览时调用
//-------------------------------------------------------------
function On_DocumentEditForm_PreviewClick(oForm, oSender)
{
	var PassFormVerifyValue = PassFormVerify(oForm, oSender, true);
	if (PassFormVerifyValue == 2)
        {
                return false;
        }
	else
	{
		if (PassFormVerifyValue == 1)
		{
			oForm.elements["_action"].value = "update";
		}
		else
		{
			oForm.elements["_action"].value = "update_preview_publish";
		}
		//禁止发送所有服务器CGI不关心的数据元素
		DisableAllRedundantElements(oForm);

		oForm.submit();
	}
}



//-------------------------------------------------------------
// 处理文本的排版工作
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oTarget:需要排版的表单对象
//		oStyle:包含排版样式的表单对象
//-------------------------------------------------------------
function On_Text_TypesetClick(oForm, oSender, oTarget, oStyle)
{
	var old_caption = getValueByRef(oForm, oSender);
	var value = getValueByRef(oForm, oTarget);
	setValueByRef(oForm, oSender, "正在排版...");
	value = formattext(value, 1);
	setValueByRef(oForm, oTarget, value);
	setValueByRef(oForm, oSender, old_caption);
}


//-------------------------------------------------------------
// 关键词热链接处理
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oCgi:目标cgi
//		oTargetName:需要进行关键字处理的文本框名称
//-------------------------------------------------------------
function doKeywordHotLink(oForm, oSender, oCgi, oTargetName)
{
	//-----------------------
	//当前项目ID
	//-----------------------
	var p_id = getValueByName(oForm, "p_id");

	//-----------------------
	//当前模板ID
	//-----------------------
	var t_id = getValueByName(oForm, "t_id");

	//-----------------------
	//当前文本框中的值
	//-----------------------
	var txtContent = getValueByName(oForm, oTargetName);
	//alert("p_id=" + p_id);
	//alert("t_id=" + t_id);
	//alert("txtContent=" + txtContent);
	if (txtContent == "")
	{
		alert("没有内容！");
		return false;
	}
	doKeywordReplace('/dpa/main.php?do=www_agent_keyword',
		         oCgi,
	oForm,oTargetName,false);
}

//-------------------------------------------------------------
// 关键词热链接处理(新)
// 参数:
//		oForm:当前的表单对象
//		oSender:事件发送者
//		oCgi:目标cgi
//		oTargetName:需要进行关键字处理的文本框名称
//-------------------------------------------------------------
function doNewKeywordHotLink(oForm, oSender, oCgi, oTargetName)
{
	//-----------------------
	//当前文本框中的值
	//-----------------------
	var txtContent = getValueByName(oForm, oTargetName);
	if (txtContent == "")
	{
		alert("没有内容！");
		return false;
	}
	doNewKeywordReplace('/dpa/main.php?do=www_agent_keyword',
		         oCgi,
	oForm,oTargetName,false);
}

//==============================================================================================
//处理关键字相关的 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
// 转发关键字链接排版的cgi处理
// 参数:
//		agent:调用的代理cgi名称
//		cgi:目标cgi
//		form:当前的表单对象
//		target:事件发送者
//		pause:是否自动提交
//-------------------------------------------------------------
function doKeywordReplace(agent,cgi,form,target,pause)
{
		var browse;
		var url;
		url = cgi;
		var p_id = getValueByName(form, "p_id");
		var target_value = getValueByName(form, target);
		//var newwin;
		var screen_width = window.screen.width;
		var screen_height = window.screen.height;
		var left = (screen_width - 600)/2;
		var top = (screen_height - 400)/2;
		var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
		property = property + ",top="+top+",left="+left;
		if(navigator.appName.indexOf("Netscape") != -1)
		{
			subReplaceWin=window.open("",null,property);
	  	}
		else
		{      
			if(subReplaceWin != null)
			{
				subReplaceWin.close();
				subReplaceWin = null;
				subReplaceWin=window.open("","",property);
			}
			else
			{
				subReplaceWin=window.open("","",property);
			} 
		}
		b_agent=navigator.appName;
		if(b_agent == 'Netscape')
		{
			browse = "Netscape";
		}
		else
		{
			browse = "IE";
		}
		subReplaceWin.focus();
		subReplaceWin.document.open("text/html");
		subReplaceWin.document.writeln("<html>");
		subReplaceWin.document.writeln("<head>");
		subReplaceWin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		subReplaceWin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">");
		subReplaceWin.document.writeln("</head>");
		subReplaceWin.document.writeln("<body>"); 
		subReplaceWin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
		subReplaceWin.document.writeln("Please Waiting.....");
		
		target_value = target_value.replace(/镕/gi,"#Rong#");		
		target_value = target_value.replace(/—/gi,"#Squote#");
		target_value = target_value.replace(/喆/gi,"#Zhe#");
		target_value = target_value.replace(/·/gi,"#zhPoint#");
		subReplaceWin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
		subReplaceWin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + target + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"_p_id\" value=\"" + p_id +"\">");
		if(b_agent == "Netscape")
		{
			target_value = target_value.replace(/\"/g,"&quot;");
			target_value = getEscapeValue(target_value);
		}
		else
		{
			if(target_value != "")
			{
				//target_value = target_value.replace(/&nbsp;/gi, " ");
				//target_value = document.myApplet.getEncodeValue(target_value);
				target_value = UrlEncode(target_value);
			}
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"" + target +  "\" value=\"" + target_value+ "\">");
		if(b_agent == "Netscape")
		{
			cgi =  getEscapeValue(cgi);
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
		if(pause)
		{
			subReplaceWin.document.writeln("<input type=submit value=\"submit\">");
			subReplaceWin.document.writeln("</form>");
			subReplaceWin.document.writeln("</body>"); 
			subReplaceWin.document.writeln("</html>");
		}
		else
		{
			subReplaceWin.document.writeln("</form>");
			subReplaceWin.document.writeln("</body>"); 
			subReplaceWin.document.writeln("</html>");
			subReplaceWin.document.close();
			subReplaceWin.document.this_form.submit();
		}
		return true;
}

//-------------------------------------------------------------
// 转发关键字链接排版的cgi处理
// 参数:
//		agent:调用的代理cgi名称
//		cgi:目标cgi
//		form:当前的表单对象
//		target:事件发送者
//		pause:是否自动提交
//-------------------------------------------------------------
function doNewKeywordReplace(agent,cgi,oform,otarget,pause)
{
		var browse;
		var url;
		url = cgi;
		var p_id = getValueByName(oform, "p_id");
		var target_value = getValueByName(oform, otarget);
		var elementName = otarget.substr(9);
		
		var ifUseGlobalKeyword;		
		if (oform.elements[elementName + "_ifUseGKeyword"].checked == true)
		{
			ifUseGlobalKeyword = "Y";	
		}
		else
		{
			ifUseGlobalKeyword = "N";	
		}
		
		var kcIDList = "";
		var usekcID;
		if (oform.elements[elementName+"_usekcID"] == null || oform.elements[elementName+"_usekcID"].checked == false)
		{
			kcIDList = "0";	
		}
		else if (oform.elements[elementName+"_usekcID"].checked == true)
		{
			var chooseKcIDList = oform.elements[elementName + "_choose_kcid"];
			for(var i=0; i<chooseKcIDList.length; i++)
			{
				if (chooseKcIDList[i].checked == true)
				{
					if (kcIDList == "")
					{
						kcIDList = chooseKcIDList[i].value;	
					}
					else
					{
						kcIDList += "," + chooseKcIDList[i].value;
					}	
				}	
			}
		}
		
		if (kcIDList == "")
		{
			kcIDList = "0";	
		}		
		
		//var newwin;
		var screen_width = window.screen.width;
		var screen_height = window.screen.height;
		var left = (screen_width - 600)/2;
		var top = (screen_height - 400)/2;
		var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
		property = property + ",top="+top+",left="+left;
		if(navigator.appName.indexOf("Netscape") != -1)
		{
			subReplaceWin=window.open("",null,property);
	  	}
		else
		{ 
			if(subReplaceWin != null)
			{
				subReplaceWin.close();
				subReplaceWin = null;
				subReplaceWin=window.open("","",property);
			}
			else
			{
				subReplaceWin=window.open("","",property);
			} 
		}
		b_agent=navigator.appName;
		if(b_agent == 'Netscape')
		{
			browse = "Netscape";
		}
		else
		{
			browse = "IE";
		}
		subReplaceWin.focus();
		subReplaceWin.document.open("text/html");
		subReplaceWin.document.writeln("<html>");
		subReplaceWin.document.writeln("<head>");
		subReplaceWin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		subReplaceWin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">");
		subReplaceWin.document.writeln("</head>");
		subReplaceWin.document.writeln("<body>"); 
		subReplaceWin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
		subReplaceWin.document.writeln("Please Waiting.....");
		
		target_value = target_value.replace(/镕/gi,"#Rong#");		
		target_value = target_value.replace(/—/gi,"#Squote#");
		target_value = target_value.replace(/喆/gi,"#Zhe#");
		subReplaceWin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
		subReplaceWin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + otarget + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"_p_id\" value=\"" + p_id +"\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"kc_id\" value=\"" + kcIDList + "\">");
		subReplaceWin.document.writeln("<input type=hidden name=\"ifUseGlobalKeyword\" value=\"" + ifUseGlobalKeyword + "\">");
		
		if(b_agent == "Netscape")
		{
			target_value = target_value.replace(/\"/g,"&quot;");
			target_value = getEscapeValue(target_value);
		}
		else
		{
			if(target_value != "")
			{
				target_value = target_value.replace(/·/gi,".");
				//target_value = target_value.replace(/&nbsp;/gi, " ");
				//target_value = document.myApplet.getEncodeValue(target_value);
				target_value = UrlEncode(target_value);
			}
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"" + otarget +  "\" value=\"" + target_value+ "\">");
		if(b_agent == "Netscape")
		{
			cgi =  getEscapeValue(cgi);
		}
		subReplaceWin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
		if(pause)
		{
			subReplaceWin.document.writeln("<input type=submit value=\"submit\">");
			subReplaceWin.document.writeln("</form>");
			subReplaceWin.document.writeln("</body>"); 
			subReplaceWin.document.writeln("</html>");
		}
		else
		{
			subReplaceWin.document.writeln("</form>");
			subReplaceWin.document.writeln("</body>"); 
			subReplaceWin.document.writeln("</html>");
			subReplaceWin.document.close();
			subReplaceWin.document.this_form.submit();
		}
		return true;
}

//==============================================================================================
// 新闻回收库文档列表表单(admin_doc/main.php?do=document_list) JavaScript Function
// 2004-02-25 by minghui
//==============================================================================================

//-------------------------------------------------------------
//文档全选
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_SelectAllClick(oForm, oSender)
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
//	选择文档查看时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_ViewClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
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
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要查看的文档!");
		return;
	}
	if(j > 1)
	{
		alert("一次只能查看一条文档!");
		return;
	}
	var cgi_url =  "main.php?do=doc_view&id=" + id;
	window.self.open(cgi_url, "_blank");
}


//-------------------------------------------------------------
//	选择文档删除时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_DeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	
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
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要删除的文档!");
		return;
	}
	if(prompt("请确定是否删除！(yes/no)", "no") == "yes")
	{
		oForm.elements["_action"].value = "delete";
		oForm.submit();
	}
}



//-------------------------------------------------------------
//	回收删除的文档时调用
//-------------------------------------------------------------
function On_ReclaimedDocumentListForm_ReclaimClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["id"];
	
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
					id = oCheckboxColl[i].value;
					j++;
				}
			}
		}
		else
		{
			if(oCheckboxColl.checked)
			{
				id = oCheckboxColl.value;
				j++;
			}
		}
	}
	if(j == 0)
	{
		alert("请选择需要回收的文档!");
		return;
	}
	oForm.elements["_action"].value = "reclaim";
	oForm.submit();
}


//-------------------------------------------------------------
//	列表对象快速检索
//-------------------------------------------------------------
function On_QuickFindPress(oSender, oReceiver)
{
	if(event.keyCode == 13)
	{
		var found = On_QuickFindClick(oSender, oReceiver);
		if(found == 1)
		{
			oSender.focus();
		}
	}
}



//-------------------------------------------------------------
//	列表对象快速检索
//-------------------------------------------------------------
function On_QuickFindClick(oTextObj, oSelectObj)
{
	var key = oTextObj.value;
	var len = oSelectObj.length;
	var selectedIndex = oSelectObj.selectedIndex;
	var value;
	var text;
	var i;

	//先查找下半部分
	for(i = selectedIndex + 1; i < len; i++)
	{
		value = oSelectObj.options[i].value;
		text = oSelectObj.options[i].text;
		if(text.indexOf(key) >= 0 || value.indexOf(key) >= 0)
		{
			oSelectObj.value = value;
			oSelectObj.fireEvent("onchange");
			return 1;
		}
	}

	//再查找上半部分
	for(i = 0; i < selectedIndex; i++)
	{
		value = oSelectObj.options[i].value;
		text = oSelectObj.options[i].text;
		if(text.indexOf(key) >= 0 || value.indexOf(key) >= 0)
		{
			oSelectObj.value = value;
			oSelectObj.fireEvent("onchange");
			return 1;
		}
	}

	//如果都没找到
	oSelectObj.selectedIndex = selectedIndex;
	value = oSelectObj.options[selectedIndex].value;
	text = oSelectObj.options[selectedIndex].text;
	if(text.indexOf(key) >= 0 || value.indexOf(key) >= 0)
	{
		return 1;
	}
	alert("对不起，没找到！");
	return 0;
}


//-------------------------------------------------------------
//	跨项目表单显示/隐藏切换
//-------------------------------------------------------------
function On_CrossPublishFormSwitch(oForm, oCrossTable, oSender)
{
	if(oSender.checked)
	{
		oCrossTable.style.display = "inline";
	}
	else
	{
		oCrossTable.style.display = "none";
	}
}



//==============================================================================================
// 文档添加、编辑表单之CGICall算法 JavaScript Function
//==============================================================================================
function On_DocumentForm_CGICallClick(oForm, oSender, _data_input, url, params)
{
	if (url != "")
	{
		var _action = oForm._action.value;
		params += "&_action=" + _action;
		params += "&_p_id=" + oForm.p_id.value;
		params += "&_t_id=" + oForm.t_id.value;
		if (oForm.elements["d_id"] != null)
			params += "&_d_id=" + oForm.d_id.value;
		var get_input_value = "oForm." + _data_input + ".value";
		params += "&_data=" + eval(get_input_value);
		/*
			_action=<action>
			_p_id=<p_id>
			_t_id=<t_id>
			_d_id=<d_id>
			_data=<data>
			_new_data=<new_data>
			param_name1=&<param_value1>
			param_name2=&<param_value2>
		*/

		if (params)
			url += "?" + params;
		//alert(url);
		
	        var out;
	        out = window.showModalDialog(url,out,"dialogHeight: 550px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
	        if(out != null && out[0] == "true")
	        {
	        	if (_action == "insert")
	        	{
	        		//每次都视为重新选择
	                	var return_value = "self.document.myform." + _data_input + ".value = '" + out[1] + "'";
	                	eval(return_value);
	                }
	        	else if (_action == "update")
	        	{
	        		//保留原始值和重新选择的值,连接###
	        		var return_value = "self.document.myform." + _data_input + ".value = '" + out[1] + "'";
	        		eval(return_value);
	        	}
	        	else
        		{
        			alert("操作无效[非添加、编辑表单]！");
        		}
	        }
	}
	else
	{
		alert("算法配置错误：from_cgi空！");
	}
		
	return;
}


//==============================================================================================
// 文档列表文档拒签原因查看 JavaScript Function
//==============================================================================================
function On_DocumentForm_RejectReasonClick(p_id, t_id, d_id)
{

	if (p_id != "" && t_id != "" && d_id != "")
	{
	        var out;
	        var url = "main.php?do=document_reject_reason&p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	        out = window.showModalDialog(url,out,"dialogHeight: 350px; dialogWidth: 300px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
	}
	else
	{
		alert("参数错误：p_id,t_id,d_id！");
	}
		
	return;
}

//=====================================
// 预览水印
//====================================
function doPreviewWM(oForm, target,flag)
{
	if (flag == 'U')
	{
		var changeradio = oForm.elements["change_"+target][0].checked;
		if(changeradio)
		{
			alert("选择需要原始文件!");
			return;
		}
	}
	else
	{
		if (oForm.elements[target].value == "")
		{
			alert("没有选择文件");
			return;
		}
	
		var wmField = target.replace("PF", "WM");
		if (oForm.elements[wmField].value == "0")
		{
			alert("没有选择水印图");
			return;
		}
	}
	oForm.target="_blank";
	oForm.action = "/dpa/main.php?do=watermark_preview_keyword";
	oForm.elements["wm_previewField"].value = target;
	oForm.submit();
}

function On_Show_KeywordCategory(oForm,oSender,elementName)
{
	var list = document.getElementById(elementName+"_keyword_category_list");
	if (oSender.checked == true)
	{
		list.style.display = "inline";
	}
	else	
	{
		list.style.display = "none";
	}
}

function onClick_SwitchUrl(oForm,pm_id)
{
	var defaultUrl = oForm.elements["default_url_" + pm_id].value;
	oForm.elements["inner_url_"+pm_id].value=defaultUrl;
}

function on_Click_ShowUrlHistoryModal(oForm)
{
	var p_id=oForm.elements["p_id"].value;
	var t_id=oForm.elements["t_id"].value;
	var d_id=oForm.elements["d_id"].value;
	var url = "main.php?do=check_urlHistory&p_id=" + p_id + "&t_id=" + t_id + "&d_id=" + d_id;
	window.showModalDialog(url);
	return;
}	
