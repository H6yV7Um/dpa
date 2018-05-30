//===========================
//工作统计模块设计的JS函数
//===========================

function On_WorkQueryForm_FormLoad(oForm)
{
	var checkmode = oForm.elements["check_mode"].value;
	if (checkmode == 0)
	{
		var date = oForm.elements["date_start"].value;
                var year = date.substr(0,4);
                var month = date.substr(4,2);
                var day = date.substr(6,2);
                On_StatForm_SelectLoad(oForm, 'start_year',year);
                On_StatForm_SelectLoad(oForm,'start_month',month);
                On_StatForm_SelectLoad(oForm,'start_day',day);
		var enddate = oForm.elements["date_end"].value;
                var endyear = enddate.substr(0,4);
                var endmonth = enddate.substr(4,2);
                var endday = enddate.substr(6,2);
                On_StatForm_SelectLoad(oForm,'end_year',endyear);
                On_StatForm_SelectLoad(oForm,'end_month',endmonth);
                On_StatForm_SelectLoad(oForm,'end_day',endday);
		return;
	}
	else if (checkmode == 1)
	{
		oForm.all("date1").style.display = "inline";
		oForm.datetype[0].checked=true; 
		var date = oForm.elements["date_start"].value;
		var year = date.substr(0,4);
                var month = date.substr(4,2);
                var day = date.substr(6,2);
		On_StatForm_SelectLoad(oForm, 'start_year',year);
		On_StatForm_SelectLoad(oForm,'start_month',month);
		On_StatForm_SelectLoad(oForm,'start_day',day);
		return;
	}
	else if (checkmode == 2)
	{
		oForm.all("date1").style.display = "inline";
                oForm.all("date2").style.display = "inline";
		oForm.datetype[1].checked=true;
		var startdate = oForm.elements["date_start"].value;
		var startyear = startdate.substr(0,4);
		var startmonth = startdate.substr(4,2);
		var startday = startdate.substr(6,2);
		On_StatForm_SelectLoad(oForm, 'start_year',startyear);
                On_StatForm_SelectLoad(oForm,'start_month',startmonth);
                On_StatForm_SelectLoad(oForm,'start_day',startday);
		var enddate = oForm.elements["date_end"].value;
		var endyear = enddate.substr(0,4);
		var endmonth = enddate.substr(4,2);
		var endday = enddate.substr(6,2);
		On_StatForm_SelectLoad(oForm,'end_year',endyear);
		On_StatForm_SelectLoad(oForm,'end_month',endmonth);
		On_StatForm_SelectLoad(oForm,'end_day',endday);
		return;
	} 
}

function On_WorkQueryListForm_QueryClick(oForm)
{
	if (oForm.datetype[0].checked == false && oForm.datetype[1].checked == false)
	{
		alert ("请选择日期");
		return false;	
	}
	var startYear = getValueByName(oForm, "start_year");
	var startMonth = getValueByName(oForm, "start_month");
	var startDay = getValueByName(oForm, "start_day");
	var startDate = startYear + startMonth + startDay;
	
	if (oForm.datetype[0].checked)
	{
		oForm.elements["check_mode"].value =1;	
	}
	else if (oForm.datetype[1].checked)
	{
		oForm.elements["check_mode"].value =2;
		var endYear = getValueByName(oForm, "end_year");
		var endMonth = getValueByName(oForm, "end_month");
		var endDay = getValueByName(oForm, "end_day");
		var endDate = endYear + endMonth + endDay;
		oForm.elements["date_end"].value = endDate;
	}
	var userlist = "";
	var usercount =0;
	var ifstatPriv = oForm.elements["if_statpriv"].value;
	if (ifstatPriv == "n")
	{
		userlist = oForm.elements["current_user_list"].value;
		usercount = 1;
	}
	else
	{
		var users = oForm.elements["users"];
		for (var i=0; i<users.length; i++)
       		{
                	if (users.options[i].selected)
                	{
                        	if (userlist != "")
                        	{
                                	userlist += ",";
                        	}
                        	userlist += users.options[i].value;
                        	usercount++;
                	}
        	}
	}
	if (usercount == 0)
	{
		alert("至少选择一个用户");
		return;
	}
	oForm.elements["current_user_list"].value = userlist;
	oForm.elements["date_start"].value = startDate;
	oForm.submit();	
}

function On_WorkStatisitcsListForm_FormLoad(oForm, oSender)
{
	On_StatForm_SelectLoad(oForm, 'current_user','users');
	var year; 
	var month;
	var day;
	var dateStart = oForm.elements["date_start"].value;
	var dateEnd = oForm.elements["date_end"].value;
	if (dateStart != '')
	{
		year = dateStart.substr(0,4);
		month = dateStart.substr(4,2);
		day = dateStart.substr(6,2);
		oForm.elements["start_year"].value = year;
		oForm.elements["start_month"].value = month;
		oForm.elements["start_day"].value = day;
	}
	if (dateEnd != '')
	{
		year = dateEnd.substr(0,4);
		month = dateEnd.substr(4,2);
		day = dateEnd.substr(6,2);
		oForm.elements["end_year"].value = year;
		oForm.elements["end_month"].value = month;
		oForm.elements["end_day"].value = day;
	}
	
}

function On_WorkStatisitcsListForm_StatisticsClick(oForm)
{
	var startYear = getValueByName(oForm, "start_year");
	var startMonth = getValueByName(oForm, "start_month");
	var startDay = getValueByName(oForm, "start_day");
	var endYear = getValueByName(oForm, "end_year");
	var endMonth = getValueByName(oForm, "end_month");
	var endDay = getValueByName(oForm, "end_day");

	if (startYear.length==0 || startMonth.length==0 || startDay.length==0 ||
		endYear.length==0 || endMonth.length==0 || endDay.length==0)
	{
		alert("请输入完整日期!");
		return false;
	}
	if (startMonth.length == 1)
	{
		startMonth = "0" + startMonth;
	}
	if (endMonth.length == 1)
	{
		endMonth = "0" + endMonth;
	}
	if (startDay.length == 1)
	{
		startDay ="0" + startDay;
	}
	if (endDay.length == 1)
	{
		endDay = "0" + endDay;
	}
	if (!isDate(startYear+"-"+startMonth+"-"+startDay))
	{
		alert("起始日期错误");
		return false;
	}
	if (!isDate(endYear+"-"+endMonth+"-"+endDay))
	{
		alert("截止日期错误");
		return false;
	}
	var startDate = startYear + startMonth + startDay;
	var endDate = endYear + endMonth + endDay;
	oForm.elements["date_start"].value = startDate;
	oForm.elements["date_end"].value = endDate;
	oForm.submit();	
}

function On_SysStatisticsListForm_IfTIDClick(oForm, oSender)
{
	if (oForm.elements["if_p_id"].checked && oForm.elements["if_sum"].checked)
	{
		oForm.all("tid").style.display = "inline";
	}
	else
	{
		oForm.all("tid").style.display = "none";
		oForm.elements["if_t_id"].checked = false;
	}
}


function On_SysStatisticsListForm_ChooseDateType(oForm)
{
	if (oForm.datetype[0].checked)
	{
		oForm.all("date1").style.display = "inline";	
		oForm.all("date2").style.display = "none";
	}
	else if (oForm.datetype[1].checked)
	{
		oForm.all("date1").style.display = "inline";
		oForm.all("date2").style.display = "inline";	
	}
	else
	{
		oForm.all("date1").style.display = "none";
		oForm.all("date2").style.display = "none";
	}
}
//-----------------------
//统计用户列表管理相关函数
//-----------------------

function On_StatUserListForm_UserListClick(oForm)
{
	if (oForm.elements["selected_user_id"].value != "")
	{
		oForm.elements["current_user_id"].value = oForm.elements["selected_user_id"].value;
		oForm.submit();
	}
}

function On_StatUserListForm_AddUserClick(oForm)
{
	if (oForm.elements["current_user_id"].value == 0)
	{
		alert("admin用户不用添加!");
		return;
	}

	if (oForm.elements["user_name"].value=="admin" &&
	 oForm.elements["selected_user_id"][0].selected)
	{
		alert("请选择用户!");
		return;
	}

	var adduser = oForm.elements["add_user_id"];
	if (adduser[0].selected)
	{
		alert("请选择 可添加用户");
		return;
	}
	oForm.elements["action"].value = "insert";
	oForm.submit();
}

function On_StatUserListForm_DelUserClick(oForm)
{
	var oCheckboxColl = oForm.elements["su_id"];
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
	oForm.elements["action"].value = "delete";
	oForm.submit();
}


function On_StatUserListForm_FormLoad(oForm, oSender)
{
	On_StatForm_SelectLoad(oForm,'selected_user_id',oForm.elements["current_user_id"].value);
}

function On_StatForm_SelectLoad(oForm, oCurObject, oTarget)
{
	//var curObj = oForm.elements[oCurObject].value;
	var oSelectObj = oForm.elements[oCurObject];
	for(var i=0;i<oSelectObj.length; i++)
	{
		if(oSelectObj.options[i].value == oTarget)
		{
			oSelectObj.options[i].selected = true;
			break;
		}
	}
}
