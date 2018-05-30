//==============================================================================================
//处理关键字相关的 JavaScript Function
//==============================================================================================

//-------------------------------------------------------------
//导航记录到第一页
//-------------------------------------------------------------
function On_KeywordListForm_FirstPageClick(oForm, oSender)
{
	oForm.elements["_goto_page"].value = "0";
	oForm.submit();
}

//-------------------------------------------------------------
//表单加载时初始化
//-------------------------------------------------------------
function On_KeywordListForm_Init(oForm, oSender)
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
function On_KeywordListForm_PrevPageClick(oForm, oSender)
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
function On_KeywordListForm_NextPageClick(oForm, oSender)
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
	len = oForm.elements.length;
	var index = 0;
	for( index=0; index < len; index++ )
	{
	  if( oForm.elements[index].name == Element )
	  {
       		oForm.elements[index].checked = oSender.checked;
	  }
	}
	return true;		
	/*var oCheckboxColl = oForm.elements[Element];
	var bChecked = false;
	if(oCheckboxColl != null)
	{
		var len = oCheckboxColl.length;
		for(var i=0; i<len; i++)
		{
			oCheckboxColl[i].checked = oSender.checked;
		}	
	
	}*/
}

//-------------------------------------------------------------
//处理告警关键字编辑
//-------------------------------------------------------------
function On_AlertKeywordListForm_KeywordEditClick(form)
{
	var obj = form.elements["ak_id"];
	var p_id = form.elements["p_id"].value;
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
				location.href = "main.php?do=alert_keyword_edit&ak_id="+ak_id + "&p_id=" + p_id;
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
	if (oForm.elements["ifAutoFormat"].checked == true)
	{
		textContent = formattext_key(textContent,1);	
	}	
	//textContent = formattext_key(textContent,1);
	var keywordbox = oForm.elements[Keyword];
	var replaceString;
	var keywords;
	var href;

	if (keywordbox.length == null)
	{
		if (keywordbox.checked)
		{
			keywords=keywordbox.value;
			if (textContent.indexOf(keywords) <0)
			{
				return textContent;
			}
			href=getValueByName(oForm, keywords);
			replaceString="<a href='" + href +"' class=akey  target=_blank>" + keywords + "</a>";
			/*var regExp = new RegExp(keywords, "ig");
			textContent = textContent.replace(regExp, replaceString);*/
			var arrayContent = textContent.split(keywords);
			textContent = "";
			for (var i=0; i<arrayContent.length; i++)
			{
				if (i == 0)
				{
					textContent += arrayContent[i] + replaceString;
				}
				else if (i != (arrayContent.length-1))
				{
					textContent += arrayContent[i] + keywords;
				}
				else
				{
					textContent += arrayContent[i];
				}
			}
			
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
				if (textContent.indexOf(keywords) <0)
				{
					continue;
				}
				href=getValueByName(oForm, keywords);
				replaceString="<a href='" + href +"' class=akey target=_blank>" + keywords + "</a>";
				/*var regExp = new RegExp(keywords, "ig");
				textContent = textContent.replace(regExp, replaceString);*/
				var arrayContent = textContent.split(keywords);
				textContent = "";
				for (var p=0; p<arrayContent.length; p++)
				{
					if (p == 0)
					{
						textContent += arrayContent[p] + replaceString;
					}
					
					else if (p != (arrayContent.length-1))
					{
						textContent += arrayContent[p] + keywords;
					}
					else
					{
						textContent += arrayContent[p];
					}
				}
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

//--------------------------
//处理关键字的链接替换
//--------------------------
function On_Click_NewKeyword_Replace(oForm,oTextName,Keyword)
{
	var textContent = getValueByName(oForm, oTextName);
	/*if (oForm.elements["ifAutoFormat"].checked == true)
	{
		textContent = formattext_key(textContent,1);	
	}*/	
	//textContent = formattext_key(textContent,1);
	var keywordbox = oForm.elements[Keyword];
	var replaceString;
	var keywordvalue;
	var keyword;
	var hrefsTr;

	if (keywordbox.length == null)
	{
		if (keywordbox.checked)
		{
			keywordvalue=keywordbox.value;
			keyword = keywordvalue.substr(0,keywordvalue.indexOf("__"));
			hrefsTr=getValueByName(oForm, keywordvalue);
			replaceString="<a href='" + hrefsTr +"' class=akey  target=_blank>" + keyword + "</a>";
			if (textContent.indexOf(keyword) <0)
			{
				return textContent;
			}
			var arrayContent = textContent.split(keyword);
			textContent = "";
			for (var i=0; i<arrayContent.length; i++)
			{
				if (i == 0)
				{
					textContent += arrayContent[i] + replaceString;
				}
				else if (i != (arrayContent.length-1))
				{
					textContent += arrayContent[i] + keyword;
				}
				else
				{
					textContent += arrayContent[i];
				}
			}
			
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
				keywordvalue=keywordbox[i].value;
				keyword = keywordvalue.substr(0,keywordvalue.indexOf("__"));
				hrefStr=getValueByName(oForm, keywordvalue);
				replaceString="<a href='" + hrefStr +"' class=akey target=_blank>" + keyword + "</a>";
				if (textContent.indexOf(keyword) <0)
				{
					continue;
				}
				var arrayContent = textContent.split(keyword);
				textContent = "";
				for (var p=0; p<arrayContent.length; p++)
				{
					if (p == 0)
					{
						textContent += arrayContent[p] + replaceString;
					}
					
					else if (p != (arrayContent.length-1))
					{
						textContent += arrayContent[p] + keyword;
					}
					else
					{
						textContent += arrayContent[p];
					}
				}
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


//--------------------------
//处理关键字的链接替换
//--------------------------
function On_Click_StockKeyword_Replace(oForm,oTextName,Keyword)
{
        var textContent = getValueByName(oForm, oTextName);
        //textContent = formattext_key(textContent,1);
        var keywordbox = oForm.elements[Keyword];
        var replaceString;
        var keywords;
        var keyvalue;
        var stockarea;
        var stockcode;
        var zx_href, hq_href, bbs_href;
        var sub_href,forum_href;
        var  keytype;

        if (keywordbox.length == null)
        {
                if (keywordbox.checked)
                {
                        keywords=keywordbox.value;
                        keyvalue=getValueByName(oForm, keywords);
                        keytype=keywords.substr(keywords.lastIndexOf("_")+1);
                        keywords=keywords.substr(0,keywords.lastIndexOf("_"));
                                                
                        if (keytype == 1)
                        {
                        	 if (keyvalue.indexOf("-")>0)
                       		{
														stockarea = keyvalue.substr(0, keyvalue.indexOf("-"));
														stockcode = keyvalue.substr(keyvalue.indexOf("-")+1);                       
                        	}
                        	else
                        	{
                        		stockarea = "";
                        		stockcode = keyvalue;
                        	}
                                                	
                        	zx_href = "<a href='http://finance.domain.com/" + stockarea + "/" + stockcode +"' target=_blank >资讯</a>"; 
                        	hq_href = "<a href='http://finance.domain.com/" + stockarea + stockcode + "' target=_blank >行情</a>";
                        	bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank >论坛</a>";
                       		replaceString = keywords + "(" + zx_href + " " + hq_href + " " + bbs_href + ")";
                        }
                	else if (keytype == 2)
                	{
                		sub_href = "<a href='" + getValueByName(oForm, keywords+"_url") + "' target=_blank >资讯</a>";
                		forum_href = "<a href='http://bbs.domain.com/" + getValueByName(oForm, keywords+"_id") + "' target=_blank >论坛</a>";
                		replaceString = keywords + "(" + sub_href + " " + forum_href + ")";
                		
                	}
                	else if (keytype == 3)
                        {
                        	hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        	bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        	replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                	}
									else if (keytype == 4)
									{
                        	hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        	bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        	replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                	}
                	else if (keytype == 5)
                        {
                        	hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        	bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        	replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                	}
                	
                	//var pattern = new RegExp(keywords);
                	//textContent=textContent.replace(pattern, replaceString);
                	
			if (textContent.indexOf(keywords) <0)
			{
				return textContent;
			}
                        var arrayContent = textContent.split(keywords);
                        textContent = "";
                        for (var i=0; i<arrayContent.length; i++)
                        {
                                if (i == 0)
                                {
                                        textContent += arrayContent[i] + replaceString;
                                }
                                else if (i != (arrayContent.length-1))
                                {
                                        textContent += arrayContent[i] + keywords;
                                }
                                else
                                {
                                        textContent += arrayContent[i];
                                }
                        }
			
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
                        	j++;
                        	keywords=keywordbox[i].value;
                                keyvalue=getValueByName(oForm, keywords);
                                keytype=keywords.substr(keywords.lastIndexOf("_")+1);
                        	keywords=keywords.substr(0,keywords.lastIndexOf("_"));
                        	
                        		if (keytype == 1)
														{
															if (keyvalue.indexOf("-")>0)
                        			{
																stockarea = keyvalue.substr(0, keyvalue.indexOf("-"));
																stockcode = keyvalue.substr(keyvalue.indexOf("-")+1);                       
	                        		}
	                        		else
	                        		{
	                        			stockarea = "";
	                        			stockcode = keyvalue;
	                        		}
	                        		zx_href = "<a href='http://finance.domain.com/" + stockarea + "/" + stockcode +"' target=_blank >资讯</a>"; 
	                        		hq_href = "<a href='http://finance.domain.com/" + stockarea + stockcode + "' target=_blank >行情</a>";
	                        		bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank >论坛</a>";
	                        		replaceString = keywords + "(" + zx_href + " " + hq_href + " " + bbs_href + ")";
                      			}
                        	else if (keytype ==2)
                        	{
                        		sub_href = "<a href='" + getValueByName(oForm, keywords+"_url") + "' target=_blank >资讯</a>";
                						forum_href = "<a href='http://bbs.domain.com/" + getValueByName(oForm, keywords+"_id") + "' target=_blank >论坛</a>";
                						replaceString = keywords + "(" + sub_href + " " + forum_href + ")";
                					}
                        	else if (keytype == 3)
                        	{
                        		hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        		bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        		replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                					}
                        	else if (keytype == 4)
                        	{
                        		hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        		bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        		replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                					}
                					else if (keytype == 5)
                        	{
                        		hq_href = "<a href='http://stock.domain.com/" + keyvalue + "' target=_blank>行情</a>";
                        		bbs_href = "<a href='http://bbs.domain.com/" + keyvalue + "' target=_blank>论坛</a>";
                        		replaceString = keywords + "(" + hq_href + " " + bbs_href + ")";
                					}
                		
                        	//var pattern = new RegExp(keywords);
                					//textContent = textContent.replace(pattern, replaceString);

				if (textContent.indexOf(keywords) < 0)
				{
					continue;
				}
                                var arrayContent = textContent.split(keywords);
                                textContent = "";
                                for (var p=0; p<arrayContent.length; p++)
                                {
                                        if (p == 0)
                                        {
                                                textContent += arrayContent[p] + replaceString;
                                        }

                                        else if (p != (arrayContent.length-1))
                                        {
                                                textContent += arrayContent[p] + keywords;
                                        }
                                        else
                                        {
                                                textContent += arrayContent[p];
                                        }
                                }
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

//--------------------------
//处理手机关键字的链接替换
//--------------------------
function On_Click_CellPhoneKeyword_Replace(oForm,oTextName,oKeyword)
{
	var textContent = getValueByName(oForm, oTextName);
	var keywordbox = oForm.elements[oKeyword];
	var replaceString;
	var keyword,keywordValue;
	var phoneurl;
	var phoneid;
	var keyword_href,brief_href, xg_href, forum_href;
	var digi_href,pic_url,digi_bbs;
	var nb_config_url,nb_pic_url,nb_forum_url;
	var nb_name,nb_fid;
	var key_type;
        
	if (keywordbox.length == null)
	{
	        if (keywordbox.checked)
	        {
	                keywordValue = keywordbox.value;
	                key_type = keywordValue.substr(keywordValue.indexOf("_")+1);
			keyword = keywordValue.substr(0,keywordValue.indexOf("_"));
			//根据关键词的类型,分别去手机或数码的各个信息
			if (key_type == "1")
	                {
				keyword = keyword.substr(keywordValue.indexOf(" ") + 1);
	                	phoneurl = getValueByName(oForm, keywordValue);
	                	phoneid = getValueByName(oForm, keywordValue+"_id");
				keyword_href = "<a href='http://tech.domain.com" + phoneurl + "' target=_blank >" + keyword + "</a>";
				brief_href = "<a href='http://tech.domain.com" + phoneurl + "' target=_blank >机型介绍</a>"; 
				xg_href = "<a href='http://tech.domain.com" + phoneurl + "' target=_blank >图片&文章</a>";
				forum_href = "<a href='http://comment.domain.com/" + phoneid + "' target=_blank >热评</a>";
				replaceString = keyword_href + "(" + brief_href + " " + xg_href + " " + forum_href + ")";
	                }
	                else if (key_type == "2")
			{
				digi_href = "<a href='http://tech.domain.com" + getValueByName(oForm, keywordValue) + "' target=_blank>资料</a>" ;
				digi_bbs = "<a href='http://comment.domain.com/" + getValueByName(oForm, keywordValue+"_id") + "' target=_blank>评价</a>";
				pic_url = "<a href='http://tech.domain.com" + getValueByName(oForm, keywordValue+"_picurl") + "' target=_blank>图片</a>";
				replaceString = keyword + "(" + digi_href + " " + digi_bbs + " " + pic_url + ")";
			}
			else if (key_type == "3")
			{
				nb_name = getValueByName(oForm, keywordValue);
				nb_fid = getValueByName(oForm, keywordValue+"_id");	
				nb_config_url = "<a href='http://tech.domain.com/" + nb_name + "' target=_blank>多种配置</a>";
				nb_pic_url = "<a href='http://tech.domain.com/" + nb_name + "' target=_blank>多图赏析</a>";
				nb_forum_url = "<a href='http://comment.domain.com/" + nb_fid + "' target=_blank>参与评论</a>";

				replaceString = keyword + "(" + nb_config_url + " " + nb_pic_url + " " + nb_forum_url + ")"; 
			}

	                //判断文本中，是否还存在对应的关键词
	                if (textContent.indexOf(keyword) <0)
			{
				return textContent;
			}
	                var arrayContent = textContent.split(keyword);
	                textContent = "";
	                for (var i=0; i<arrayContent.length; i++)
	                {
	                        if (i == 0)
				{
					textContent += arrayContent[i] + replaceString;
				}
				else if (i != (arrayContent.length-1))
				{
					textContent += arrayContent[i] + keyword;
				}
				else
				{
					textContent += arrayContent[i];
				}
			}
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
	                        keywordValue = keywordbox[i].value;
				key_type = keywordValue.substr(keywordValue.indexOf("_")+1);
				keyword = keywordValue.substr(0,keywordValue.indexOf("_"));
				if (key_type == "1")
				{
					keyword = keyword.substr(keywordValue.indexOf(" ")+1);
					phoneurl = getValueByName(oForm, keywordValue);
					phoneid = getValueByName(oForm, keywordValue+"_id");
					keyword_href = "<a href='http://tech.domain.com/" + phoneurl + "' target=_blank >" + keyword + "</a>";
					brief_href = "<a href='http://tech.domain.com/" + phoneurl + "' target=_blank >机型介绍</a>"; 
					xg_href = "<a href='http://tech.domain.com/" + phoneurl + "' target=_blank >图片&文章</a>";
					forum_href = "<a href='http://comment.domain.com/" + phoneid + "' target=_blank >热评</a>";
					replaceString = keyword_href + "(" + brief_href + " " + xg_href + " " + forum_href + ")";
				}
				else if (key_type == "2")
				{
					digi_href = "<a href='http://tech.domain.com/" + getValueByName(oForm, keywordValue) + "' target=_blank>资料</a>" ;
					digi_bbs = "<a href='http://comment.domain.com/" + getValueByName(oForm, keywordValue+"_id") + "' target=_blank>评价</a>";
					pic_url = "<a href='http://tech.domain.com/" + getValueByName(oForm, keywordValue+"_picurl") + "' target=_blank>图片</a>";
					replaceString = keyword + "(" + digi_href + " " + digi_bbs + " " + pic_url + ")";
				}
				else if (key_type == "3")
				{
					nb_name = getValueByName(oForm, keywordValue);
					nb_fid = getValueByName(oForm, keywordValue+"_id"); 
					nb_config_url = "<a href='http://tech.domain.com/" + nb_name + "' target=_blank>多种配置</a>";
					nb_pic_url = "<a href='http://tech.domain.com/" + nb_name + "' target=_blank>多图赏析</a>";
					nb_forum_url = "<a href='http://comment.domain.com/" + nb_fid + "' target=_blank>参与评论</a>";

					replaceString = keyword + "(" + nb_config_url + " " + nb_pic_url + " " + nb_forum_url + ")";
				}

				if (textContent.indexOf(keyword) < 0)
				{
					continue;
				}
	                        var arrayContent = textContent.split(keyword);
	                        textContent = "";
	                        for (var p=0; p<arrayContent.length; p++)
	                        {
					if (p == 0)
					{
						textContent += arrayContent[p] + replaceString;
					}
					
					else if (p != (arrayContent.length-1))
					{
						textContent += arrayContent[p] + keyword;
					}
					else
					{
						textContent += arrayContent[p];
					}
	                        }
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

function On_NewKeywordListForm_KeywordEditClick(form)  
{
	var obj = form.elements["k_id"]; 
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
			location.href = "main.php?do=keyword_edit&k_id="+k_id;
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

function On_NewKeywordListForm_KeywordDeleteClick(oForm, oSender)
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
function On_NewKeyword_CategoryEditClick(form)
{
	var obj = form.elements["kc_id"];
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
			location.href = "main.php?do=keyword_category_edit&kc_id="+kc_id;
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
function On_NewKeyword_CategoryDeleteClick(oForm, oSender)
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

	if(confirm("分类对应的所有关键字将被置为'紧用'状态,确定删除吗?") == false)
	{
		return false;
	}
	oForm.action = "main.php?do=keyword_category_list";
	oForm.elements["_action"].value = "delete";
	oForm.submit();
}

function search_valueChange(oForm)
{
	oForm.elements["_goto_page"].value=0;
}

