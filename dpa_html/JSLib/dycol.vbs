//-----------------------------------------
// 开始校验表单
//-----------------------------------------
function verify_all()
{        
	//-----------------------------------------
	// 校验新闻标题
	//-----------------------------------------
	if(!valid_biaoti())
	{
		return false;
	}

	//-----------------------------------------
	// 校验作者
	//-----------------------------------------
	if(!valid_author())
	{
		return false;
	}

	return true;
}


function valid_biaoti()
{
        var CharLength=document.myform._FORM_PF_sp_f33.value;
        var star=      document.myform._FORM_PF_sp_f61.value;  //文章级别
        var ifshou=    document.myform._FORM_PF_sp_f133.value; //是否新闻中心首页
        
        var len = CharLength.length;
        var count = 0;
        
        if(CharLength.length==0) 
        {
        alert(ifshou);
        alert("请填写新闻标题！");
        return false;
        }
        
        for(var i=0;i<len;i++)
        {
                var ascii = CharLength.charCodeAt(i);
                if(ascii > 127)
                {
                        count += 2;
                }
                else
                {
                        count++;
                }
        }
        
        if(ifshou == "yes")
        {
                if(count > 43)
                {
                        document.myform._FORM_PF_sp_f33.focus();
                        if(alert("此新闻发往新闻中心首页，标题超过21.5字,请点击确定,返回修改标题")) {return false;}
                        else{   return false;}
                }
        }
        
        if(star == "★★★")
        {
                if(count > 46)
                {
                        document.myform._FORM_PF_sp_f33.focus();
                        if(alert("新闻标题超过23个,请点击确定,返回修改标题")) {return false;}
                        else{   return false;}
                }
        }
        else
        {
                if(count > 49)
                {
                        document.myform._FORM_PF_sp_f33.focus();
                        if(confirm("\n新闻标题超过24.5个字,发布吗？")) {return false;}
                        else{   return false;}
                }
        }
        return true;
}




function valid_author()
{
	var author=document.myform._FORM_PF_sp_f231.value;
	author=author.replace(/ /g,'');
        if(author.length==0)
        {
                if(document.myform._FORM_PF_sp_f35.selectedIndex==0) 
                {
                        alert("请选择“媒体名称”或填写“来源/作者”！");
                        return false;
                }
        }
        else
        {
                if(document.myform._FORM_PF_sp_f35.selectedIndex!=0) 
                {
                        alert("如果选择了“媒体名称”就不要填写“来源/作者”。\n或者如果填写了“来源/作者”就不要选择“媒体名称”！");
                        return false;
                }
        }
	return true;
}


</script>

<script LANGUAGE="VBScript">
<!--

	'----------------------------------------------------------------------
	' 解码函数:获取XMLHTTP返回的二进制结果并解码
	'----------------------------------------------------------------------
	Function bytes2BSTR(vIn)
		Dim strReturn, i,ThisCharCode,NextCharCode
	    strReturn = ""
	    For i = 1 To LenB(vIn)
	        ThisCharCode = AscB(MidB(vIn,i,1))
	        If ThisCharCode < &H80 Then
	            strReturn = strReturn & Chr(ThisCharCode)
	        Else
	            NextCharCode = AscB(MidB(vIn,i+1,1))
	            strReturn = strReturn & Chr(CLng(ThisCharCode) * &H100 + CInt(NextCharCode))
	            i = i + 1
	        End If
	    Next
	    bytes2BSTR = strReturn
	End Function


	'----------------------------------------------------------------------
	' 编码函数:对XMLHTTP的请求参数进行编码
	'----------------------------------------------------------------------
	Function URLEncoding(vstrIn)
		Dim strReturn, i,innerCode,Low8,Hight8,ThisChr
	    strReturn = ""
	    For i = 1 To Len(vstrIn)
	        ThisChr = Mid(vStrIn,i,1)
	        If Abs(Asc(ThisChr)) < &HFF Then
	            strReturn = strReturn & ThisChr
	        Else
	            innerCode = Asc(ThisChr)
	            If innerCode < 0 Then
	                innerCode = innerCode + &H10000
	            End If
	            Hight8 = (innerCode  And &HFF00)\ &HFF
	            Low8 = innerCode And &HFF
	            strReturn = strReturn & "%" & Hex(Hight8) &  "%" & Hex(Low8)
	        End If
	    Next
	    URLEncoding = strReturn
	End Function


	'----------------------------------------------------------------------
	' 全局变量:跟踪栏目的初始选择值
	'----------------------------------------------------------------------
	Dim RebuildCountOf_sp_f337,RebuildCountOf_sp_f118
	RebuildCountOf_sp_f337 = 0
	RebuildCountOf_sp_f118 = 0
	

	'----------------------------------------------------------------------
	' 奥运频道:所属奥运专题子栏目动态重建
	'----------------------------------------------------------------------
	Sub Rebuild_sp_f337()
		Dim strRequest,strResult,aLines,aCols,i,Item,j,sColVal
		Dim oReq,oColSelect,oOption
		Set oColSelect = document.myform.elements("_FORM_PF_sp_f337")
		
		'------------------------------------------------------------------------
		'CGI请求参数
		'	p_id:指定的项目ID
		'	tbl:专题子栏目表名称
		'	num_field:专题子栏目顺序字段
		'	col_field:专题子栏目名称字段
		'	sub_field:专题子栏目所属专题字段
		'	sub_value:希望查询的专题值
		'	order_field:返回结果排序字段(可忽略,默认按num_field排序)
		'------------------------------------------------------------------------
		strRequest = URLEncoding("p_id=249&tbl=sp_t17&num_field=sp_f216&col_field=sp_f217&sub_field=sp_f215&sub_value=" & Me.value)
		
		'创建请求对象
		Set oReq = CreateObject("MSXML2.XMLHTTP")
		
		'向指定的CGI发送请求
		oReq.open "POST","/cgi-bin/gsps/get_subcol_list.cgi",false
		oReq.setRequestHeader "Content-Length",Len(strRequest)
		oReq.setRequestHeader "CONTENT-TYPE","application/x-www-form-urlencoded"
		oReq.send strRequest
		
		'分析并获取返回结果
		strResult = bytes2BSTR(oReq.responseBody)

		'获取栏目以前的选择值
		sColVal = oColSelect.options(oColSelect.selectedIndex).value

		'根据CGI的返回结果重建专题子栏目列表		
		oColSelect.options.length = 0
		aLines = Split(strResult, vbLf, -1, 1)
		Set oOption = document.createElement("OPTION")
		oColSelect.options.add(oOption)
		oOption.innerText =  "0---"
		oOption.value = ""
		j=1
		For i=0 To UBound(aLines)
			If aLines(i) <> "" Then
				aCols = Split(aLines(i), ",", 2, 1)
				Set oOption = document.createElement("OPTION")
				oColSelect.options.add(oOption)
				oOption.innerText = j & "---" & aCols(1) & "(" & aCols(0) &  ")"
				oOption.value = aCols(0)
				j = j + 1
			End If
		Next

		'根据当前的栏目值调整栏目的选择值
		If RebuildCountOf_sp_f337 = 0 Then
			For i=0 To oColSelect.options.length - 1
				If oColSelect.options.item(i).value = sColVal Then
					oColSelect.selectedIndex = i
				End If
			Next
			RebuildCountOf_sp_f337 = RebuildCountOf_sp_f337 + 1
		End If
	End Sub
	
	

	'----------------------------------------------------------------------
	' 奥运频道:所属体育新专题1子栏目动态重建
	'----------------------------------------------------------------------
	Sub Rebuild_sp_f119()
		Dim strRequest,strResult,aLines,aCols,i,Item,j,sColVal
		Dim oReq,oColSelect,oOption
		Set oColSelect = document.myform.elements("_FORM_PF_sp_f119")
		
		'------------------------------------------------------------------------
		'CGI请求参数
		'	p_id:指定的项目ID
		'	tbl:专题子栏目表名称
		'	num_field:专题子栏目顺序字段
		'	col_field:专题子栏目名称字段
		'	sub_field:专题子栏目所属专题字段
		'	sub_value:希望查询的专题值
		'	order_field:返回结果排序字段(可忽略,默认按num_field排序)
		'------------------------------------------------------------------------
		strRequest = URLEncoding("p_id=6&tbl=sp_t77&num_field=sp_f1512&col_field=sp_f1513&sub_field=sp_f1511&sub_value=" & Me.value)
		
		'创建请求对象
		Set oReq = CreateObject("MSXML2.XMLHTTP")
		
		'向指定的CGI发送请求
		oReq.open "POST","/cgi-bin/gsps/get_subcol_list.cgi",false
		oReq.setRequestHeader "Content-Length",Len(strRequest)
		oReq.setRequestHeader "CONTENT-TYPE","application/x-www-form-urlencoded"
		oReq.send strRequest
		
		'分析并获取返回结果
		strResult = bytes2BSTR(oReq.responseBody)

		'获取栏目以前的选择值
		sColVal = oColSelect.options(oColSelect.selectedIndex).value

		'根据CGI的返回结果重建专题子栏目列表		
		oColSelect.options.length = 0
		aLines = Split(strResult, vbLf, -1, 1)
		Set oOption = document.createElement("OPTION")
		oColSelect.options.add(oOption)
		oOption.innerText =  "0---"
		oOption.value = ""
		j=1
		For i=0 To UBound(aLines)
			If aLines(i) <> "" Then
				aCols = Split(aLines(i), ",", 2, 1)
				Set oOption = document.createElement("OPTION")
				oColSelect.options.add(oOption)
				oOption.innerText = j & "---" & aCols(1) & "(" & aCols(0) &  ")"
				oOption.value = aCols(0)
				j = j + 1
			End If
		Next

		'根据当前的栏目值调整栏目的选择值
		If RebuildCountOf_sp_f118 = 0 Then
			For i=0 To oColSelect.options.length - 1
				If oColSelect.options.item(i).value = sColVal Then
					oColSelect.selectedIndex = i
				End If
			Next
			RebuildCountOf_sp_f118 = RebuildCountOf_sp_f118 + 1
		End If
	End Sub	
	
	
	Set document.myform.elements("_FORM_PF_sp_f336").onchange =  GetRef("Rebuild_sp_f337")
	Set document.myform.elements("_FORM_PF_sp_f118").onchange =  GetRef("Rebuild_sp_f119")
	
-->
</script>

<script language="javascript">