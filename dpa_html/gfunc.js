//----------------------------------------------------------------------
//PART-I
//数据字典表单处理
//----------------------------------------------------------------------
var FIELD_RULE = "_PF_";
var VERIFY_RULE = "_VF_";
var FIELD_CNAME_RULE = "_FCR_";
var FIELD_TYPE_RULE = "_FTR_";


var field_rule = FIELD_RULE;
var verify_rule = VERIFY_RULE;
var field_cname_rule = FIELD_CNAME_RULE;
var field_type_rule = FIELD_TYPE_RULE;
var field_rule_len = field_rule.length;
var verify_rule_len = verify_rule.length;
	
	
function verifyNotNull(value)
{
	if(value == null)
	{
		return false;
	}
	else
	{
		if(value.length ==0 )
		{
			return false;
		}
	}
	return true;
}

function verifyNumeric(value)
{
	if(value == null || value == '')
	{
		return false;
	}
	return !isNaN(value);
}

function verifyCommon(object,title,value,type)
{
	if(type == 'Int' || type == 'Float')
	{
		if(!verifyNumeric(value))
		{
			alert(title+"必须为数值！");
			object.focus();
			return false;
		}
	}
	return true;
}


function verifyLength(value,maxLen)
{
	if(value == null || value == '')
	{
		return false;
	}
	var len = value.length;
	var count = 0;
	for(var i=0;i<len;i++)
	{
		var ascii = value.charCodeAt(i);
		if(ascii > 127)
		{
			count += 2;
		}
		else
		{
			count++;
		}
	}
	if(count > maxLen)
	{
		return false;
	}
	return true;
}


function getFormFieldValue(form,fieldName)
{
	var len;
	var index = 0;
	var object;
	var type;
	var value;
	len = form.elements.length;
	for(index=0;index<len;index++)
	{
		object = form.elements[index];
		if(object.name == fieldName)
		{
			type = object.type;
			if(type == "text" || type == "password" || type == "textarea" || type == "file" || type == "hidden")
			{
				return object.value;
			}
			else if(type == "select-one")
			{
				return object.options[object.selectedIndex].value;
			}			
			else
			{
				return null;
			}
		}
	}
	return null;
}



function actionclick(form)
{
	len = form.elements.length;
	var index;
	var fieldName;
	var very_notnull_fieldName;
	var very_numeric_fieldName;
	var very_length_fieldName;
	var verify_cname_fieldName;
	var verify_cname_fieldName_value;
	var verify_type_fieldName;
	var verify_type_fieldName_value;
	var value;
	for(index=0;index<len;index++)
	{
		var object = form.elements[index];
		fieldName = form.elements[index].name;
		if(fieldName.substr(0,field_rule_len) == field_rule)
		{
			if(object.disabled)
			{
				continue;
			}
			verify_cname_fieldName = field_cname_rule + fieldName.substr(field_rule.length);
			verify_cname_fieldName_value = getFormFieldValue(form,verify_cname_fieldName);
			value = getFormFieldValue(form,fieldName);
			verify_notnull_fieldName = verify_rule + "_NOTNULL_" + fieldName.substr(field_rule.length);
			var verify_notnull_value = getFormFieldValue(form,verify_notnull_fieldName);
			if(verify_notnull_value == "TRUE")
			{
				if(!verifyNotNull(value))
				{
					alert(verify_cname_fieldName_value+"不能为空！");
					form.elements[index].focus();
					return true;
				}
			}
			else
			{
				continue;
			}


			very_length_fieldName = verify_rule + "_LENGTH_" + fieldName.substr(field_rule.length);
			var verify_length_value = getFormFieldValue(form,very_length_fieldName);
			var i_verify_length_value = parseInt(verify_length_value);
			if(i_verify_length_value != 0)
			{
				if(!verifyLength(value,i_verify_length_value))
				{
					alert(verify_cname_fieldName_value+"的最大长度为"+i_verify_length_value);
					form.elements[index].focus();
					return true;
				}
			}

			
			very_numeric_fieldName = verify_rule + "_NUMERIC_" + fieldName.substr(field_rule.length);
			var verify_numeric_value = getFormFieldValue(form,very_numeric_fieldName);
			if(verify_numeric_value == "TRUE")
			{
				if(!verifyNumeric(value))
				{
					alert(verify_cname_fieldName_value+"必须为数值");
					form.elements[index].focus();
					return true;
				}
			}
			
			//通用验证：为Int,Float类型
			verify_type_fieldName = field_type_rule + fieldName.substr(field_rule.length);
			verify_type_fieldName_value = getFormFieldValue(form,verify_type_fieldName);
			
			if(!verifyCommon(object,verify_cname_fieldName_value,value,verify_type_fieldName_value))
			{
				return true;
			}
		}
	}
	form.submit();
}



function validclick(form)
{
	len = form.elements.length;
	var index;
	var fieldName;
	var very_notnull_fieldName;
	var very_numeric_fieldName;
	var very_length_fieldName;
	var verify_cname_fieldName;
	var verify_cname_fieldName_value;
	var verify_type_fieldName;
	var verify_type_fieldName_value;
	var value;
	for(index=0;index<len;index++)
	{
		var object = form.elements[index];
		fieldName = form.elements[index].name;
		if(fieldName.substr(0,field_rule_len) == field_rule)
		{
			if(object.disabled)
			{
				continue;
			}
			verify_cname_fieldName = field_cname_rule + fieldName.substr(field_rule.length);
			verify_cname_fieldName_value = getFormFieldValue(form,verify_cname_fieldName);
			value = getFormFieldValue(form,fieldName);
			verify_notnull_fieldName = verify_rule + "_NOTNULL_" + fieldName.substr(field_rule.length);
			var verify_notnull_value = getFormFieldValue(form,verify_notnull_fieldName);
			if(verify_notnull_value == "TRUE")
			{
				if(!verifyNotNull(value))
				{
					alert(verify_cname_fieldName_value+"不能为空！");
					form.elements[index].focus();
					return false;
				}
			}
			else
			{
				continue;
			}


			very_length_fieldName = verify_rule + "_LENGTH_" + fieldName.substr(field_rule.length);
			var verify_length_value = getFormFieldValue(form,very_length_fieldName);
			var i_verify_length_value = parseInt(verify_length_value);
			if(i_verify_length_value != 0)
			{
				if(!verifyLength(value,i_verify_length_value))
				{
					alert(verify_cname_fieldName_value+"的最大长度为"+i_verify_length_value);
					form.elements[index].focus();
					return false;
				}
			}

			
			very_numeric_fieldName = verify_rule + "_NUMERIC_" + fieldName.substr(field_rule.length);
			var verify_numeric_value = getFormFieldValue(form,very_numeric_fieldName);
			if(verify_numeric_value == "TRUE")
			{
				if(!verifyNumeric(value))
				{
					alert(verify_cname_fieldName_value+"必须为数值");
					form.elements[index].focus();
					return false;
				}
			}
			
			//通用验证：为Int,Float类型
			verify_type_fieldName = field_type_rule + fieldName.substr(field_rule.length);
			verify_type_fieldName_value = getFormFieldValue(form,verify_type_fieldName);
			
			if(!verifyCommon(object,verify_cname_fieldName_value,value,verify_type_fieldName_value))
			{
				return false;
			}
		}
	}
	return true;
}

//----------------------------------------------------------------------
//PART-II
//Cookie处理
//----------------------------------------------------------------------


// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
function setCookie(name, value, expires, path, domain, secure)
{
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
  document.cookie = curCookie;
}


// name - name of the desired cookie
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name)
{
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1)
  {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  } 
  else
    begin += 2;
  var end = document.cookie.indexOf(";", begin);
  if (end == -1)
    end = dc.length;
  return unescape(dc.substring(begin + prefix.length, end));
}


// name - name of the cookie
// [path] - path of the cookie (must be same as path used to create cookie)
// [domain] - domain of the cookie (must be same as domain used to create cookie)
// * path and domain default if assigned null or omitted if no explicit argument proceeds
function deleteCookie(name, path, domain) {
  if (getCookie(name)) {
    document.cookie = name + "=" + 
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
  }
}


// date - any instance of the Date object
// * hand all instances of the Date object to this function for "repairs"
function fixDate(date) {
  var base = new Date(0);
  var skew = base.getTime();
  if (skew > 0)
    date.setTime(date.getTime() - skew);
}
