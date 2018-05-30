//-------------------------------------------------------------
//处理关键字编辑
//-------------------------------------------------------------
function On_KeywordListForm_KeywordEditClick(form)
{
	var obj = form.elements["k_id"];
	var p_id = form.elements["p_id"].value;
		if(obj == null)
		{
			alert("无关键字!");
		}
		else
		{
			var len = obj.length;
			var check_count = 0;
			var k_id;
			if(len != null)
			{
				for(var i=0;i<len;i++)
				{
					if(obj[i].checked)
					{
						k_id = obj[i].value;
						check_count++;
					}
				}
			}
			else
			{
				if(obj.checked)
				{
					k_id = obj.value;
					check_count++;
				}
			}
			if(check_count == 1)
			{
				location.href = "main.php?do=keyword_edit&p_id="+p_id+"&k_id="+k_id;
			  	return true;
			}
			else if(check_count == 0)
			{
				alert("至少得选中一个关键字!");
			}
			else
			{
				alert("一次只能编辑一个关键字!");
			}
		}
		return false;		
}

//-------------------------------------------------------------
//处理关键字删除
//-------------------------------------------------------------
function On_KeywordListForm_KeywordDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["k_id"];
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
		alert("请选择需要删除关键字!");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=keyword_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//-------------------------------------------------------------
//处理关键字分类编辑
//-------------------------------------------------------------
function On_Keyword_CategoryEditClick(form)
{
	var obj = form.elements["kc_id"];
	var p_id = form.elements["p_id"].value;
		if(obj == null)
		{
			alert("没有选定分类!");
		}
		else
		{
			var len = obj.length;
			var check_count = 0;
			var kc_id;
			if(len != null)
			{
				for(var i=0;i<len;i++)
				{
					if(obj[i].checked)
					{
						kc_id = obj[i].value;
						check_count++;
					}
				}
			}
			else
			{
				if(obj.checked)
				{
					kc_id = obj.value;
					check_count++;
				}
			}
			if(check_count == 1)
			{
				location.href = "main.php?do=keyword_categoryedit&p_id="+p_id+"&kc_id="+kc_id;
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

//-------------------------------------------------------------
//处理关键字分类删除
//-------------------------------------------------------------
function On_Keyword_CategoryDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["kc_id"];
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
		alert("请选择需要删除分类!");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=keyword_categorylist";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

function On_Click_KeywordSelectAll(oForm,oSender,Element)
{
	var oCheckboxColl = oForm.elements[Element];
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
//处理告警关键字编辑
//-------------------------------------------------------------
function On_AlertKeywordListForm_KeywordEditClick(form)
{
	var obj = form.elements["ak_id"];
		if(obj == null)
		{
			alert("无关键字!");
		}
		else
		{
			var len = obj.length;
			var check_count = 0;
			var ak_id;
			if(len != null)
			{
				for(var i=0;i<len;i++)
				{
					if(obj[i].checked)
					{
						ak_id = obj[i].value;
						check_count++;
					}
				}
			}
			else
			{
				if(obj.checked)
				{
					ak_id = obj.value;
					check_count++;
				}
			}
			if(check_count == 1)
			{
				location.href = "main.php?do=alert_keyword_edit&ak_id="+ak_id;
			  	return true;
			}
			else if(check_count == 0)
			{
				alert("至少得选中一个关键字!");
			}
			else
			{
				alert("一次只能编辑一个关键字!");
			}
		}
		return false;		
}

//-------------------------------------------------------------
//处理告警关键字删除
//-------------------------------------------------------------
function On_AlertKeywordListForm_KeywordDeleteClick(oForm, oSender)
{
	var oCheckboxColl = oForm.elements["ak_id"];
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
		alert("请选择需要删除关键字!");
		return;
	}

	if(confirm("确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=alert_keyword_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

//--------------------------
//处理关键字的链接替换
//--------------------------
function On_Click_Keyword_Replace(oForm,oTextName,Keyword)
{
	var textContent = getValueByName(oForm, oTextName);
	var keywordbox = oForm.elements[Keyword];
	var replaceString;
	var keywords;
	var href;

	if (keywordbox.length == null)
	{
		if (keywordbox.checked)
		{
			keywords=keywordbox.value;
			href=getValueByName(oForm, keywords);
			replaceString="<a href=" + href +">" + keywords + "</a>";
			var regExp = new RegExp(keywords, "ig");
			textContent = textContent.replace(regExp, replaceString);
		}
		else
		{
			alert("没有要替换的关键字");
			return;
		}
	}
	else
	{
		var j=0;
		for (var i=0; i<keywordbox.length; i++)
		{
			if (keywordbox[i].checked)
			{
				keywords=keywordbox[i].value;
				href=getValueByName(oForm, keywords);
				replaceString="<a href=" + href +">" + keywords + "</a>";
				var regExp = new RegExp(keywords, "ig");
				textContent = textContent.replace(regExp, replaceString);
				j++;
			}
		}
		if (j == 0)
		{
			alert("没有要替换的关键字");
			return;
		}
	}
	setValueByRef(oForm,oForm.elements[oTextName],textContent);
}

function do_Rel_Result(agent,cgi,form,target,title,pause)
{
		var collection= form.elements;
		var browse;
		var strName;
		var url;
		var keyArray = new Array();
		url = cgi;
		regExp = new RegExp(target+"_","ig");
		if(collection != null)
		{
			var j = 0;
		    for(var i=0;i<collection.length; i++)
		    {
		    	var ele = form.elements[i];
		    	strName = ele.name;
		  		if((strName.indexOf("_rel_result_") != -1) && (strName.indexOf(target) != -1))
		  		{
		  			if(ele.value == "")
		  			{
		  				alert("请完成输入!");
		  				ele.focus();
		  				return false;
		  			}
		  			keyArray[j] = strName;
		  			j++;
			    }
			}
		}
		var newwin;
		var key_element;
		var screen_width = window.screen.width;
		var screen_height = window.screen.height;
		var left = (screen_width - 600)/2;
		var top = (screen_height - 400)/2;
		var property = "scrollbars=yes,height=400,width=600,status=yes,resizable=yes,toolbar=yes,menubar=no,location=no";
		property = property + ",top="+top+",left="+left;
		var myname;
		if(navigator.appName.indexOf("Netscape") != -1)
		{
			newwin=window.open("",null,property);
	  	}
		else
		{ 
			if(self.newwin != null)
			{
				self.newwin.close();
				newwin = null;
				newwin=window.open("",null,property);
			}
			else
			{
				newwin=window.open("",null,property);
			} 
		}
		b_agent=navigator.appName;
		if(b_agent == 'Netscape')
		{
			title = getEscapeValue(title);
			browse = "Netscape";
		}
		else
		{
			browse = "IE";
		}
		newwin.document.open("text/html");
		newwin.document.writeln("<html>");
		newwin.document.writeln("<head>");
		newwin.document.writeln("<title>" + title + "</title>");
		newwin.document.writeln("<meta http-equiv=\"pragma\" content=\"no-cache\">");
		newwin.document.writeln("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">");
		newwin.document.writeln("</head>");
		newwin.document.writeln("<body>"); 
		newwin.document.writeln("<form  enctype=\"multipart/form-data\" method=post name=this_form action=\""+ agent +"\">");
		newwin.document.writeln("Please Waiting.....");
		
		for(i=0;i<keyArray.length;i++)
		{
			var current_object;
			key_element = new String(keyArray[i]);
			key = key_element.replace(/_rel_result_/,"");
			key = key.replace(regExp,"");
			current_object = form.elements[key_element];
			type = current_object.type;
			if(type == "select-one")
			{
				key_value = current_object.options[current_object.selectedIndex].value;
			}
			else
			{
				key_value = current_object.value;
				if(type == "hidden")
				{
					var form_element = "";
					if(b_agent == 'Netscape')
					{
						var start_pos = key_value.indexOf("${");
						var end_pos = key_value.indexOf("}");
						if((start_pos != -1) && (end_pos != -1) && (end_pos > start_pos + 2))
						{
							form_element = key_value.substring(start_pos+2,end_pos);
						}
					}
					else
					{
						var re = new RegExp("^\\${(.*)}$");
						var arr = re.exec(key_value);
						form_element = RegExp.$1;
					}
					if(form_element != "")
					{
						form_element = "_fieldvalue_"+form_element;
						var my_current_object = form.elements[form_element];
						var form_element_type = my_current_object.type;
						if(form_element_type == "select-one")
						{
							key_value = my_current_object.options[my_current_object.selectedIndex].value;
						}
						else
						{
							key_value = my_current_object.value;
						}
					}
				}
			}
			if(b_agent == "Netscape")
			{
				key_value = key_value.replace(/\"/g,"&quot;");
				key_value = getEscapeValue(key_value);
			}
			else
			{
				if(key_value != "")
				{
					key_value = key_value.replace(/·/gi,".");
					//key_value = document.myApplet.getEncodeValue(key_value);
					key_value = UrlEncode(key_value);
				}
			}
			if(pause && type == "hidden" && key_value == "FILE")
			{
				newwin.document.writeln("<br>File:<input type=file name=\"" + key +  "\"<br>");
			}
			else
			{
				newwin.document.writeln("<input type=hidden name=\"" + key +  "\" value=\"" + key_value + "\">");
			}
	  }
		target_value = form.elements[target].value;
		target_value = target_value.replace(/镕/gi,"#Rong#");		
		target_value = target_value.replace(/—/gi,"#Squote#");
		newwin.document.writeln("<input type=hidden name=\"cgi\"" + " value=\"" + cgi + "\">");	
		newwin.document.writeln("<input type=hidden name=\"target\"" + " value=\"" + target + "\">");
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
				//target_value = document.myApplet.getEncodeValue(target_value);
				target_value = UrlEncode(target_value);
			}
		}
		newwin.document.writeln("<input type=hidden name=\"" + target +  "\" value=\"" + target_value+ "\">");
		if(b_agent == "Netscape")
		{
			cgi =  getEscapeValue(cgi);
		}
		newwin.document.writeln("<input type=hidden name=\"browse\" value=\"" + browse+ "\">");
		if(pause)
		{
			newwin.document.writeln("<input type=submit value=\"submit\">");
			newwin.document.writeln("</form>");
			newwin.document.writeln("</body>"); 
			newwin.document.writeln("</html>");
		}
		else
		{
			newwin.document.writeln("</form>");
			newwin.document.writeln("</body>"); 
			newwin.document.writeln("</html>");
			newwin.document.close();
			newwin.document.this_form.submit();
		}
		return true;
}

