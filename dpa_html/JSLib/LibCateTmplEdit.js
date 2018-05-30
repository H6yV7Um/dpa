//==============================================================================================
//处理编辑分类字符串模板的相关 JavaScript Function
//==============================================================================================

//页面初始化
function On_CategoryTmplEdit_Init(oForm)
{
	var category_tmpl=oForm.category_tmpl.value;
	oForm.result.value=category_tmpl;
	if (category_tmpl == "")
	{
		return;
	}
	category_tmpl = category_tmpl.substr(1,category_tmpl.length-2);
	var fieldIDs = oForm.fields_id;
	var selectFields = oForm.selected_fields;
	var fieldArray;
	fieldArray = category_tmpl.split("|");
	var fieldIDArray = new Array();
	var i;
	var fieldID;
	var position;
	for (i=0; i<fieldArray.length; i++)
	{
		position = fieldArray[i].indexOf(":");
		fieldID = fieldArray[i].substr(0, position);
		fieldIDArray.push(fieldID);
	}
	var selectSize=0;
	for (i=0; i<fieldIDArray.length; i++)
	{
		for (var j=0; j<fieldIDs.length; j++)
		{
			if (fieldIDArray[i] == fieldIDs.options[j].value)
			{
				selectFields.length = selectSize+1;
				selectFields.options[selectSize].value = fieldIDs.options[j].value;
				selectFields.options[selectSize].text = fieldIDs.options[j].text;
				selectSize++;
				break;
			}
		}
	}
}

//添加模板域到分类字符串
function On_CategoryTmplEdit_AddFieldClick(oForm)
{
        var fields = oForm.fields_id;
	var selectFields = oForm.selected_fields;
	var arraySize=0;
	var selectSize;
	var ifNeedAdd;
	var fieldsValueArray = new Array();
	var fieldsTextArray = new Array();
	for (var i=0; i<fields.length; i++)
	{
		if (fields.options[i].selected)
		{
		 	fieldsValueArray.push(fields.options[i].value);
		 	fieldsTextArray.push(fields.options[i].text);
		}
	}
	
	arraySize = fieldsValueArray.length;
	   
	if (selectFields.length == 0)
	{
		selectFields.length = arraySize;
		for (var i=0; i<arraySize; i++)
		{
			selectFields.options[i].value = fieldsValueArray[i];
			selectFields.options[i].text = fieldsTextArray[i];
		}	
	}
	else
	{
		for (var i=0; i<arraySize; i++)
		{
			ifNeedAdd = true;
			for (var k=0; k<selectFields.length; k++)
			{
				if (selectFields.options[k].value == fieldsValueArray[i])
				{
					ifNeedAdd = false;
					break;
				}
			}
			if (ifNeedAdd)
			{
				selectSize = selectFields.length;
				selectFields.length = selectSize+1;
				selectFields.options[selectSize].value = fieldsValueArray[i];
				selectFields.options[selectSize].text = fieldsTextArray[i];
			}
		}
	}
	On_CategoryTmplEdit_DisplayCategoryinfo(oForm);		
}

//从分类字符串中删除模板域
function On_CategoryTmplEdit_DeleteFieldClick(oForm)
{
        var selectFields = oForm.selected_fields;
        var selectSize;
        var fieldsValueArray = new Array();
        var fieldsTextArray = new Array();
        var i;
        for (i=0; i<selectFields.length; i++)
        {
        	if (!selectFields.options[i].selected)
        	{
        		fieldsValueArray.push(selectFields.options[i].value);
        		fieldsTextArray.push(selectFields.options[i].text);	
        	}
        }
	var arraySize = fieldsValueArray.length;
	oForm.selected_fields.length = arraySize;
	for (i=0; i<arraySize; i++)
	{
		oForm.selected_fields.options[i].value=fieldsValueArray[i];
		oForm.selected_fields.options[i].text=fieldsTextArray[i];
		oForm.selected_fields.options[i].selected=false;
	}
	On_CategoryTmplEdit_DisplayCategoryinfo(oForm);
}

//在选中字段列表中，将某个字段位置向上移动
function On_CategoryTmplEdit_UpFieldClick(oForm)
{
	var selectFields = oForm.selected_fields;
	var count=0;
	for (var i=0; i<selectFields.length; i++)
	{
		if (selectFields.options[i].selected)
		{
			count++;
		}	
	}
	if (count > 1)
	{
		alert("一次只能移动一个模板域");
		return false;	
	}
	var index = selectFields.selectedIndex;
	
	var originValue = selectFields.options[index].value;
	var originText = selectFields.options[index].text;
	selectFields.options[index].value = selectFields.options[index-1].value;
	selectFields.options[index].text = selectFields.options[index-1].text;
	selectFields.options[index-1].value = originValue;
	selectFields.options[index-1].text = originText;
	selectFields.options[index-1].selected=true;
	selectFields.options[index].selected=false;
	On_CategoryTmplEdit_DisplayCategoryinfo(oForm);	
}

//在选中字段列表中，将某个字段位置向下移动
function On_CategoryTmplEdit_DownFieldClick(oForm)
{
	var selectFields = oForm.selected_fields;
	var count=0;
	for (var i=0; i<selectFields.length; i++)
	{
		if (selectFields.options[i].selected)
		{
			count++;
		}	
	}
	if (count > 1)
	{
		alert("一次只能移动一个模板域");
		return false;	
	}
	var index = selectFields.selectedIndex;
	var originValue = selectFields.options[index].value;
	var originText = selectFields.options[index].text;
	selectFields.options[index].value = selectFields.options[index+1].value;
	selectFields.options[index].text = selectFields.options[index+1].text;
	selectFields.options[index+1].value = originValue;
	selectFields.options[index+1].text = originText;
	selectFields.options[index+1].selected=true;
	selectFields.options[index].selected=false;
	On_CategoryTmplEdit_DisplayCategoryinfo(oForm);
}

//在选中字段列表中，交换俩个字段位置
function On_CategoryTmplEdit_SwitchFieldClick(oForm)
{
	var selectFields = oForm.selected_fields;
	var count=0;
	var indexArray = new Array(0);
	for (var i=0; i<selectFields.length; i++)
	{
		if (selectFields.options[i].selected)
		{
			indexArray.push(i);
			count++;
		}	
	}
	if (count != 2)
	{
		alert("请选择俩个模板域");
		return false;	
	}
	var index = indexArray[0];
	var index2 = indexArray[1];
	var originValue = selectFields.options[index].value;
	var originText = selectFields.options[index].text;
	selectFields.options[index].value = selectFields.options[index2].value;
	selectFields.options[index].text = selectFields.options[index2].text;
	selectFields.options[index2].value = originValue;
	selectFields.options[index2].text = originText;
	selectFields.options[index2].selected=true;
	selectFields.options[index].selected=true;
	On_CategoryTmplEdit_DisplayCategoryinfo(oForm);
}

//将选中字段列表的内容，拼成最终的字符串并显示
function On_CategoryTmplEdit_DisplayCategoryinfo(oForm)
{
	var selectFields = oForm.selected_fields;
	var categoryinfo;
	categoryinfo = "[";
	var fieldValue;
	for (var i=0; i<selectFields.length; i++)
	{
		fieldValue = selectFields.options[i].value;
		if (fieldValue.indexOf("sp_f") == 0)
		{
			fieldValue = fieldValue.substr(4);
		}
		if (i != 0)
		{
			categoryinfo += "|";
		}
		categoryinfo += fieldValue + ":*";
	}
	categoryinfo += "]";
	if (selectFields.length == 0)
	{
		categoryinfo="";	
	}
	oForm.result.value = categoryinfo;
}

//
function On_CategoryTmplEdit_SaveClick(oForm)
{
	oForm._action.value = "update";
	oForm.submit();
}