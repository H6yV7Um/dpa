
var bodyID,htmlableID;

var WBTB_yToolbars = new Array();

var WBTB_YInitialized = false;

var WBTB_filterScript = false;

var WBTB_charset="UTF-8";

function OpenUpload(url,pid,setFrm)
{
	url = url + "?_p_id=" + pid + "&_setfrm=" + setFrm;
	newWindow = window.open(url,"upload","width=450,height=300,left=0,top=0,scrollbars=1,status=1,resizable=1");
	newWindow.focus();
}

function document_onreadystatechange()
{
	if (WBTB_YInitialized) return;
	WBTB_YInitialized = true;

	var i, curr;

	// NOTE: Modified by jingtao for support multi-HtmlEditor
	/*
	if(document.all("yToolbar1") != null)
	{
		curr = document.all("yToolbar1");
		WBTB_InitTB(curr);
		WBTB_yToolbars[WBTB_yToolbars.length] = curr;
	}
	if(document.all("yToolbar2") != null)
	{
		curr = document.all("yToolbar2");
		WBTB_InitTB(curr);
		WBTB_yToolbars[WBTB_yToolbars.length] = curr;
	}
	if(document.all("yToolbar3") != null)
	{
		curr = document.all("yToolbar3");
		WBTB_InitTB(curr);
		WBTB_yToolbars[WBTB_yToolbars.length] = curr;
	}
	*/

	var yTb = document.all("yToolbar");	
	if(yTb != null) {
		for (i = 0; i < yTb.length; i++) {
			curr = yTb[i];
			WBTB_InitTB(curr);
			WBTB_yToolbars[WBTB_yToolbars.length] = curr;
		}
	}	
}
if(document.attachEvent){
	document.onreadystatechange=function(){
		document_onreadystatechange();
	};
}else {
	document.addEventListener("DOMContentLoaded",document_onreadystatechange,false);
}
function WBTB_InitBtn(btn)
{
	btn.onmouseover = WBTB_BtnMouseOver;
	btn.onmouseout = WBTB_BtnMouseOut;
	btn.onmousedown = WBTB_BtnMouseDown;
	btn.onmouseup	= WBTB_BtnMouseUp;
	btn.ondragstart = WBTB_YCancelEvent;
	btn.onselectstart = WBTB_YCancelEvent;
	btn.onselect = WBTB_YCancelEvent;
	btn.YUSERONCLICK = btn.onclick;
	btn.onclick = WBTB_YCancelEvent;
	btn.YINITIALIZED = true;
	return true;
}

function WBTB_InitTB(y)
{
	y.TBWidth = 0;
	if (!WBTB_PopulateTB(y)) return false;
	y.style.posWidth = y.TBWidth;
	return true;
}


function WBTB_YCancelEvent()
{
	event.returnValue=false;
	event.cancelBubble=true;
	return false;
}

function WBTB_BtnMouseOver()
{
	if (event.srcElement.tagName != "IMG") return false;
	var image = event.srcElement;
	var element = image.parentElement;

	if (image.className == "WBTB_Ico") element.className = "WBTB_BtnMouseOverUp";
	else if (image.className == "WBTB_IcoDown") element.className = "WBTB_BtnMouseOverDown";

	event.cancelBubble = true;
}

function WBTB_BtnMouseOut()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		return false;
	}

	var image = event.srcElement;
	var element =	image.parentElement;
	yRaisedElement = null;

	element.className = "WBTB_Btn";
	image.className = "WBTB_Ico";

	event.cancelBubble = true;
}

function WBTB_BtnMouseDown()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		event.returnValue=false;
		return false;
	}

	var image = event.srcElement;
	var element = image.parentElement;

	element.className = "WBTB_BtnMouseOverDown";
	image.className = "WBTB_IcoDown";

	event.cancelBubble = true;
	event.returnValue=false;
	return false;
}

function WBTB_BtnMouseUp()
{
	if (event.srcElement.tagName != "IMG") {
		event.cancelBubble = true;
		return false;
	}

	var image = event.srcElement;
	var element = image.parentElement;

	if (element.YUSERONCLICK) eval(element.YUSERONCLICK + "anonymous()");

	element.className = "WBTB_BtnMouseOverUp";
	image.className = "WBTB_Ico";

	event.cancelBubble = true;
	return false;
}

function WBTB_PopulateTB(y)
{
	var i, elements, element;

	elements = y.children;
	for (i=0; i<elements.length; i++) {
	element = elements[i];
	if (element.tagName== "SCRIPT" || element.tagName == "!") continue;

	switch (element.className) {
		case "WBTB_Btn":
			if (element.YINITIALIZED == null) {
				if (! WBTB_InitBtn(element))
					return false;
			}

			element.style.posLeft = y.TBWidth;
			y.TBWidth	+= element.offsetWidth + 1;
			break;

		case "WBTB_TBGen":
			element.style.posLeft = y.TBWidth;
			y.TBWidth	+= element.offsetWidth + 1;
			break;

			//default:
			//  return false;
		}
	}

	y.TBWidth += 1;
	return true;
}

function WBTB_DebugObject(obj)
{
	var msg = "";
	for (var i in TB) {
		ans=prompt(i+"="+TB[i]+"\n");
		if (! ans) break;
	}
}

// NOTE: "////" modified by jingtao for support multi-HtmlEditor
////function WBTB_validateMode()
function WBTB_validateMode(bTextMode,frm)
{
	////if (!WBTB_bTextMode) return true;
	if (!bTextMode) return true;
	
	alert("请取消“查看HTML源代码”选项再使用系统编辑功能或者提交!");
	
	////WBTB_Composition.focus();
	var editor = eval(frm);
	editor.focus();
	
	return false;
}

////function WBTB_format1(what,opt)
function WBTB_format1(what,frm,opt)
{
	if (opt=="removeFormat")
	{
		what=opt;
		opt=null;
	}
	
	////WBTB_Composition.focus();
	var editor = eval(frm);
	editor.focus();
	
	if (opt==null)
	{
		////WBTB_Composition.document.execCommand(what);
		editor.document.execCommand(what);
	}else{
		////WBTB_Composition.document.execCommand(what,"",opt);
		editor.document.execCommand(what,"",opt);
	}
	
	WBTB_pureText = false;
	
	////WBTB_Composition.focus();
	editor.focus();
}

////function WBTB_format(what,opt)
function WBTB_format(what,bTextMode,frm,opt)
{
	  ////if (!WBTB_validateMode()) return;
	  if (!WBTB_validateMode(bTextMode,frm)) return;

	  ////WBTB_format1(what,opt);
	  WBTB_format1(what,frm,opt);
}

// NOTE: Modified by jingtao
/*
function WBTB_setMode(objField)
{
	WBTB_bTextMode=!WBTB_bTextMode;
	WBTB_setTab();
	var cont;
	if (WBTB_bTextMode) {
		document.all.WBTB_Container.style.display='none';
		WBTB_cleanHtml();
		cont=WBTB_Composition.document.body.innerHTML;
		//alert(cont);
		cont=WBTB_correctUrl(cont);
		if (WBTB_filterScript)
			cont=WBTB_FilterScript(cont);
		//WBTB_Composition.document.body.innerText=cont;
		objField.style.display = "inline";
		objField.value=cont;
	} else {
		document.all.WBTB_Container.style.display='';
		objField.style.display = "none";
		//.style.display = "block";		
		//cont=WBTB_Composition.document.body.innerText;
		cont=objField.value;
		cont=WBTB_correctUrl(cont);
		if (WBTB_filterScript)
			cont=WBTB_FilterScript(cont);
		//alert(cont);
		WBTB_Composition.document.body.innerHTML=cont;
		document.all.WBTB_Container.focus(); // Added by jingtao for fix a BUG
	}
	WBTB_setStyle();	
	WBTB_Composition.focus();
}
*/

//
function WBTB_updateContent(content)
{
	// 替换转意字符
	var r1 = new RegExp("<([^>]*)&lt;([^<>]*)>", "g");
	content = content.replace(r1, "<$1<$2>");
		
	var r2 = new RegExp("<([^>]*)&gt;([^<>]*)>", "g");
	content = content.replace(r2, "<$1>$2>");
		
	var r3 = new RegExp("<([^>]*)&amp;([^<>]*)>", "g");
	content = content.replace(r3, "<$1&$2>");
		
	var r4 = new RegExp("<([^>]*)&quot;([^<>]*)>", "g");
	content = content.replace(r4, "<$1\"$2>");
	
	// 将所有标记转换成小写
	var re = new RegExp("<\/?[A-Z]+", "g");
	var arr, str;
	while ((arr = re.exec(content)) != null) {
		str = arr.toString();
		content = content.replace(str, str.toLowerCase());
	}		
	
	return content;
}

function WBTB_setMode(objField,container,tabHtml,tabDesign,bTextMode,frm)
{
	var editor = eval(frm);
	editor.focus();
	
	WBTB_setTab(tabHtml,tabDesign,bTextMode);
	
	var cont;
	
	if (bTextMode) {
		document.all(container).style.display='none';		
		WBTB_cleanHtml(frm);
		cont = editor.document.body.innerHTML;		
		cont = WBTB_correctUrl(cont);
		if (WBTB_filterScript) {
			cont = WBTB_FilterScript(cont);
		}		
		
		objField.style.display = "inline";
		objField.value = cont;
	} else {
		document.all(container).style.display='';		
		objField.style.display = "none";		
		cont = objField.value;
		cont = WBTB_correctUrl(cont);
		if (WBTB_filterScript) {
			cont = WBTB_FilterScript(cont);
		}		
		editor.document.body.innerHTML = cont;
		document.all(container).focus();
	}
	
	WBTB_setStyle(bTextMode,frm);
	
	editor.focus();
}

////function WBTB_setStyle()
function WBTB_setStyle(bTextMode,frm)
{
	////bs = WBTB_Composition.document.body.runtimeStyle;
	var editor = eval(frm);
	bs = editor.document.body.runtimeStyle;
	
	//根据mode设置iframe样式表
	////if (WBTB_bTextMode) {
	if (bTextMode) {
		bs.fontFamily="宋体,Arial";
		bs.fontSize="10pt";
	}else{
		bs.fontFamily="宋体,Arial";
		bs.fontSize="10.5pt";
	}
	bs.scrollbar3dLightColor= '#D4D0C8';
	bs.scrollbarArrowColor= '#000000';
	bs.scrollbarBaseColor= '#D4D0C8';
	bs.scrollbarDarkShadowColor= '#D4D0C8';
	bs.scrollbarFaceColor= '#D4D0C8';
	bs.scrollbarHighlightColor= '#808080';
	bs.scrollbarShadowColor= '#808080';
	bs.scrollbarTrackColor= '#D4D0C8';
	bs.border='0';
}

////function WBTB_setTab()
function WBTB_setTab(tabHtml,tabDesign,bTextMode)
{
	//html和design按钮的样式更改
	////var mhtml=document.all.WBTB_TabHtml;
	var mhtml=document.all(tabHtml);
	
	////var mdesign=document.all.WBTB_TabDesign;
	var mdesign=document.all(tabDesign);
	
	////if (WBTB_bTextMode)		
	if (bTextMode)
	{
		mhtml.className="WBTB_TabOn";
		mdesign.className="WBTB_TabOff";
	}else{
		mhtml.className="WBTB_TabOff";
		mdesign.className="WBTB_TabOn";
	}
}

function WBTB_getEl(sTag,start)
{
	while ((start!=null) && (start.tagName!=sTag)) start = start.parentElement;
	return start;
}

////function WBTB_UserDialog(what)
function WBTB_UserDialog(what,bTextMode,frm)
{
	////if (!WBTB_validateMode()) return;
	if (!WBTB_validateMode(bTextMode,frm)) return;
	
	////WBTB_Composition.focus();
	var editor = eval(frm);
	editor.focus();
	
	////WBTB_Composition.document.execCommand(what, true);
	editor.document.execCommand(what, true);

	//去掉添加图片时的src="file://
	/*if(what=="InsertImage")
	{
		////WBTB_Composition.document.body.innerHTML=(WBTB_Composition.document.body.innerHTML).replace(:"src=\"file://","src=\"");
		editor.document.body.innerHTML=(editor.document.body.innerHTML).replace("src=\"file://","src=\"");
	}*/
	
	if(what=="InsertImage")
	{
		alert("此功能并不上传图片！\n要上传图片，请使用“上传文件”功能。");
	}	

	WBTB_pureText = false;
	
	////WBTB_Composition.focus();
	editor.focus();
}

////function WBTB_foreColor()
function WBTB_foreColor(bTextMode,frm)
{
	////if (!WBTB_validateMode()) return;
	if (!WBTB_validateMode(bTextMode,frm)) return;
	
	var arr = showModalDialog("/gsps/htmleditor/selcolor.htm", "", "dialogWidth:18.5em;dialogHeight:17em;status:no;scroll:no;help:no");
	
	////if (arr != null) WBTB_format('forecolor', arr);
	////else WBTB_Composition.focus();
	if (arr != null)
	{
		WBTB_format('forecolor', bTextMode, frm, arr);
	}
	else
	{
		var editor = eval(frm);
		editor.focus();
	}
}

////function WBTB_backColor()
function WBTB_backColor(bTextMode,frm)
{
	////if (!WBTB_validateMode()) return;
	if (!WBTB_validateMode(bTextMode, frm)) return;
	
	var arr = showModalDialog("/gsps/htmleditor/selcolor.htm", "", "dialogWidth:18.5em;dialogHeight:17em;status:no;scroll:no;help:no");
	
	////if (arr != null) WBTB_format('backcolor', arr);
	////else WBTB_Composition.focus();
	if (arr != null)
	{
		WBTB_format('backcolor', bTextMode, frm, arr);
	}
	else
	{
		var editor = eval(frm);
		editor.focus();
	}
}

////function WBTB_fortable()
function WBTB_fortable(bTextMode,frm)
{
	////if (!WBTB_validateMode())	return;
	if (!WBTB_validateMode(bTextMode,frm))	return;
	
	var editor = eval(frm);
	
	var arr = showModalDialog("/gsps/htmleditor/table.htm", "", "dialogWidth:14.5em;dialogHeight:17.5em;status:no;scroll:no;help:no");

	if (arr != null)
	{
		var ss;
		ss=arr.split("*")
		row=ss[0];
		if (row=="") row=1;
		col=ss[1];
		if (col=="") col=1;
		tbwidth=ss[2];
		if (tbwidth=="") tbwidth=500;
		tbborder=ss[3];
		if (tbborder=="") tbborder=1;
		celpadding=ss[4];
		if (celpadding=="") celpadding=2;
		bdcolor=ss[5];
		if (bdcolor=="") bdcolor="#CCCCCC";
		bgcolor=ss[6];
		if (bgcolor=="") bgcolor="#FFFFFF";
		tbalign=ss[7];
		if (tbalign=="") tbalign="center";
		var string;
		string="<table border="+ tbborder +" cellspacing=0 width="+tbwidth+" cellpadding="+ celpadding +" align="+tbalign+" bgcolor='"+ bgcolor +"' bordercolor="+ bdcolor +" style='border-collapse:collapse'>";
		for(i=1;i<=row;i++){
			string=string+"<tr>";
			for(j=1;j<=col;j++){
				string=string+"<td>&nbsp;</td>";
			}
			string=string+"</tr>";
		}
		string=string+"</table>";
		
		////content=WBTB_Composition.document.body.innerHTML;
		content=editor.document.body.innerHTML;
		
		content=content+string;
		
		////WBTB_Composition.document.body.innerHTML=content;
		editor.document.body.innerHTML=content;
	}
	////else WBTB_Composition.focus();
	else editor.focus();
}

//// Added by jingtao for insert hyperlink
function WBTB_forhl(frm)
{
	var editor = eval(frm);
	editor.focus();
	
	var oSel = editor.document.selection;
	var oRange = oSel.createRange();
	var oElem = (oSel.type == "Control") ? oRange(0) : oRange.parentElement();
	var sTag = oElem.tagName.toUpperCase();
	
	if ((oSel.type == "Control" && sTag != "IMG")
			|| sTag == "CAPTION" || sTag == "COL" || sTag == "COLGROUP" || sTag == "FRAMESET" || sTag == "HTML" || sTag == "TEXTAREA"
			|| sTag == "TABLE" || sTag == "TBODY" || sTag == "TFOOT" || sTag == "TH" || sTag == "THEAD" || sTag == "TR") {
		alert("只能给文本或图片插入超链接");
		editor.focus();
		return;
	}
	
	var arg = new Array();
	arg[0] = oRange;
	arg[1] = oElem;

	var arr = showModalDialog("/gsps/htmleditor/hyperlink.htm", arg, "dialogWidth:21em;dialogHeight:19em;status:no;scroll:no;help:no");

	if (arr != null) {
		sPath = arr[0];
		sClass = arr[1];	
		sTarget = arr[2];
		sPrompt = arr[3];		

		var str = "<A";		
		if (sClass != ""){
			str += " class=\"" + sClass + "\"";
		}
		str += " href=\"" + sPath + "\"";			
		if (sPrompt != ""){
			str += " title=\"" + sPrompt + "\"";
		}
		if (sTarget != ""){
			str += " target=\"" + sTarget + "\"";
		}
		
		var txt;
		if (oSel.type == "Control") {
			if (sTag == "IMG") {
				txt = oElem.outerHTML;
				str += ">" + txt + "</A>";
				oElem.outerHTML = str;
			}
		} else {
			if (sTag == "A") {
				txt = oElem.innerText;
				oElem.outerHTML = "";
			} else {
				txt = oRange.htmlText;
				if (txt == "") {
					txt = sPath;
				}		
			}
			
			str += ">" + txt + "</A>";
			oRange.pasteHTML(str);
		}
	}	
	
	editor.focus();
}
//// End added

////function WBTB_forswf()
function WBTB_forswf(frm)
{
	var editor = eval(frm);
	
	var arr = showModalDialog("/gsps/htmleditor/swf.htm", "", "dialogWidth:14em;dialogHeight:13em;status:no;scroll:no;help:no");

	if (arr != null){
		var ss;
		ss=arr.split("*")
		path=ss[0];
		row=ss[1];
		col=ss[2];
		var string;
		string="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'  codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width="+row+" height="+col+"><param name=movie value="+path+"><param name=quality value=high><embed src="+path+" pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash' type='application/x-shockwave-flash' width="+row+" height="+col+"></embed></object>";
		
		////content=WBTB_Composition.document.body.innerHTML;
		content=editor.document.body.innerHTML;
		
		content=content+string;
		
		////WBTB_Composition.document.body.innerHTML=content;
		editor.document.body.innerHTML=content;
	}
	////else WBTB_Composition.focus();
	else editor.focus();
}

////function WBTB_forwmv()
function WBTB_forwmv(frm)
{
	var editor = eval(frm);
	
	var arr = showModalDialog("/gsps/htmleditor/wmv.htm", "", "dialogWidth:15em;dialogHeight:14.5em;status:no;scroll:no;help:no");

	if (arr != null){
		var ss;
		ss=arr.split("*")
		path=ss[0];
		width=ss[1];
		height=ss[2];
		var string;
		//string="<object align=center classid=CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95 hspace=5 vspace=5 width="+ width +" height="+ height +"><param name=Filename value="+ path +"><param name=ShowStatusBar value=1><embed type=application/x-oleobject codebase=http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701 flename=mp src="+ path +"  width="+ width +" height="+ height +"></embed></object>";
		string="<embed src='"+ path+"' width="+ width +" height="+ height +" autostart=true loop=false></embed>";
		
		////content=WBTB_Composition.document.body.innerHTML;
		content=editor.document.body.innerHTML;
		
		content=content+string;
		
		////WBTB_Composition.document.body.innerHTML=content;
		editor.document.body.innerHTML=content;
	}
	////else WBTB_Composition.focus();
	else editor.focus();
}

////function WBTB_forrm()
function WBTB_forrm(frm)
{
	var editor = eval(frm);
	
	var arr = showModalDialog("/gsps/htmleditor/rm.htm", "", "dialogWidth:15em;dialogHeight:14.5em;status:no;scroll:no;help:no");

	if (arr != null)
	{
		var ss;
		ss=arr.split("*")
		path=ss[0];
		row=ss[1];
		col=ss[2];
		var string;
		string="<object classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA' width="+row+" height="+col+"><param name='CONTROLS' value='ImageWindow'><param name='CONSOLE' value='Clip1'><param name='AUTOSTART' value='-1'><param name=src value="+path+"></object><br><object classid='clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA'  width="+row+" height=60><param name='CONTROLS' value='ControlPanel,StatusBar'><param name='CONSOLE' value='Clip1'></object>";
		
		////content=WBTB_Composition.document.body.innerHTML;
		content=editor.document.body.innerHTML;
		
		content=content+string;
		
		////WBTB_Composition.document.body.innerHTML=content;
		editor.document.body.innerHTML=content;
	}
	////else WBTB_Composition.focus();
	else editor.focus();
}

////function WBTB_InsertRow()
function WBTB_InsertRow(frm)
{
	////editor = WBTB_Composition;
	var editor = eval(frm);
	
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TABLE' :
		var newTable=objReference.cloneNode(true);
		var newRow= newTable.insertRow();

		for(x=0; x<newTable.rows[0].cells.length; x++)
		{
			var newCell = newRow.insertCell();
		}
		objReference.outerHTML=newTable.outerHTML;
		break;
		
	case 'TBODY' :
		var newTable=objReference.cloneNode(true);
		var newRow = newTable.insertRow();
		for(x=0; x<newTable.rows[0].cells.length; x++)
		{
			var newCell = newRow.insertCell();
		}
		objReference.outerHTML=newTable.outerHTML;
		break;
		
	case 'TR' :
		var rowIndex = objReference.rowIndex;
		var parentTable=objReference.parentElement.parentElement;
		var newTable=parentTable.cloneNode(true);
		var newRow = newTable.insertRow(rowIndex+1);
		for(x=0; x< newTable.rows[0].cells.length; x++)
		{
			var newCell = newRow.insertCell();
		}
		parentTable.outerHTML=newTable.outerHTML;
		break;
		
	case 'TD' :
		var parentRow=objReference.parentElement;
		var rowIndex = parentRow.rowIndex;
		var cellIndex=objReference.cellIndex;
		var parentTable=objReference.parentElement.parentElement.parentElement;
		var newTable=parentTable.cloneNode(true);
		var newRow = newTable.insertRow(rowIndex+1);
		for(x=0; x< newTable.rows[0].cells.length; x++)
		{
			var newCell = newRow.insertCell();
			if (x==cellIndex)newCell.id='ura';
		}
		parentTable.outerHTML=newTable.outerHTML;
		var r = editor.document.body.createTextRange();
		var item=editor.document.getElementById('ura');
		item.id='';
		r.moveToElementText(item);
		r.moveStart('character',r.text.length);
		r.select();
		break;
		
	default :
		return;
	}
}

////function WBTB_DeleteRow()
function WBTB_DeleteRow(frm)
{
	////editor=WBTB_Composition;
	var editor = eval(frm);
	
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TR' :
		var rowIndex = objReference.rowIndex;//Get rowIndex
		var parentTable=objReference.parentElement.parentElement;
		parentTable.deleteRow(rowIndex);
		break;
	
	case 'TD' :
		var cellIndex=objReference.cellIndex;
		var parentRow=objReference.parentElement;//Get Parent Row
		var rowIndex = parentRow.rowIndex;//Get rowIndex
		var parentTable=objReference.parentElement.parentElement.parentElement;
		parentTable.deleteRow(rowIndex);
		if (rowIndex>=parentTable.rows.length)
		{
			rowIndex=parentTable.rows.length-1;
		}
		if (rowIndex>=0)
		{
			var r = editor.document.body.createTextRange();
			r.moveToElementText(parentTable.rows[rowIndex].cells[cellIndex]);
			r.moveStart('character',r.text.length);
			r.select();
		}
		else
		{
			parentTable.removeNode(true);
		}
		break;
	
	default :return;
	}
}


////function WBTB_InsertColumn()
function WBTB_InsertColumn(frm)
{
	////editor = WBTB_Composition;
	var editor = eval(frm);
	
	objReference= WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TABLE' :// IF a table is selected, it adds a new column on the right hand side of the table.
		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell();
		}
		newCell.focus();
		objReference.outerHTML=newTable.outerHTML;
		break;
	
	case 'TBODY' :// IF a table is selected, it adds a new column on the right hand side of the table.
		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell();
		}
		objReference.outerHTML=newTable.outerHTML;
		break;
	
	case 'TR' :// IF a table is selected, it adds a new column on the right hand side of the table.
		objReference=objReference.parentElement.parentElement;
		var newTable=objReference.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell();
		}
		objReference.outerHTML=newTable.outerHTML;
		break;
	
	case 'TD' :// IF the cursor is in a cell, or a cell is selected, it adds a new column to the right of that cell.
		var cellIndex = objReference.cellIndex;//Get cellIndex
		var rowIndex=objReference.parentElement.rowIndex;
		var parentTable=objReference.parentElement.parentElement.parentElement;
		var newTable=parentTable.cloneNode(true);
		for(x=0; x<newTable.rows.length; x++)
		{
			var newCell = newTable.rows[x].insertCell(cellIndex+1);
			if (x==rowIndex)newCell.id='ura';
		}
		parentTable.outerHTML=newTable.outerHTML;
		var r = editor.document.body.createTextRange();
		var item=editor.document.getElementById('ura');
		item.id='';
		r.moveToElementText(item);
		r.moveStart('character',r.text.length);
		r.select();
		break;

	default :
		return;
	}
}


////function WBTB_DeleteColumn()
function WBTB_DeleteColumn(frm)
{
	////editor = WBTB_Composition;
	var editor = eval(frm);
	
	objReference=WBTB_GetRangeReference(editor);
	objReference=WBTB_CheckTag(objReference,'/^(TABLE)|^(TR)|^(TD)|^(TBODY)/');
	switch(objReference.tagName)
	{
	case 'TD' :
		var rowIndex=objReference.parentElement.rowIndex;
		var cellIndex = objReference.cellIndex;//Get cellIndex
		var parentTable=objReference.parentElement.parentElement.parentElement;
		var newTable=parentTable.cloneNode(true);
		if (newTable.rows[0].cells.length==1)
		{
			parentTable.removeNode(true);
			return;
		}
		for(x=0; x<newTable.rows.length; x++)
		{
			if (newTable.rows[x].cells[cellIndex]=='[object]')
			{
				newTable.rows[x].deleteCell(cellIndex);
			}
		}
		if (cellIndex>=newTable.rows[0].cells.length)
		{
			cellIndex=newTable.rows[0].cells.length-1;
		}
		if (cellIndex>=0)  newTable.rows[rowIndex].cells[cellIndex].id='ura';
		parentTable.outerHTML=newTable.outerHTML;
		if (cellIndex>=0){
			var r = editor.document.body.createTextRange();
			var item=editor.document.getElementById('ura');
			item.id='';
			r.moveToElementText(item);
			r.moveStart('character',r.text.length);
			r.select();
		}
		break;
		
	default :return;
	}
}


function WBTB_GetRangeReference(editor)
{
	editor.focus();
	var objReference = null;
	var RangeType = editor.document.selection.type;
	var selectedRange = editor.document.selection.createRange();

	switch(RangeType)
	{
	case 'Control' :
		if (selectedRange.length > 0 )
		{
			objReference = selectedRange.item(0);
		}
		break;

	case 'None' :
		objReference = selectedRange.parentElement();
		break;

	case 'Text' :
		objReference = selectedRange.parentElement();
		break;
	}
	return objReference;
}

function WBTB_CheckTag(item,tagName)
{
	if (item.tagName.search(tagName)!=-1)
	{
		return item;
	}
	if (item.tagName=='BODY')
	{
		return false;
	}
	item=item.parentElement;
	return WBTB_CheckTag(item,tagName);
}

function WBTB_code(frm)
{
	WBTB_specialtype(frm,"<div class=quote style='cursor:hand'; title='Click to run the code' onclick=\"preWin=window.open('','','');preWin.document.open();preWin.document.write(this.innerText);preWin.document.close();\">","</div>");
}

/*
function WBTB_replace()
{
	var arr = showModalDialog("/gsps/htmleditor/replace.htm", "", "dialogWidth:16.5em; dialogHeight:13em; status:0; help:0");

	if (arr != null){
		var ss;
		ss=arr.split("*")
		a=ss[0];
		b=ss[1];
		i=ss[2];
		con=WBTB_Composition.document.body.innerHTML;
		if (i==1)
		{
			con=WBTB_rCode(con,a,b,true);
		}else{
			con=WBTB_rCode(con,a,b);
		}
		WBTB_Composition.document.body.innerHTML=con;
	}
	else WBTB_Composition.focus();
}*/

function WBTB_replace(frm)
{
	var editor = eval(frm);
	var oRange = editor.document.selection.createRange();
	var arr = showModalDialog("/gsps/htmleditor/replace.htm", oRange, "dialogWidth:23em;dialogHeight:15.5em;status:no;scroll:no;help:no");
	editor.focus();
}


// NOTE: Modified by jingtao
/*function WBTB_CleanCode() {
	editor=WBTB_Composition;
	editor.focus();
	// 0bject based cleaning
	var body = editor.document.body;
	for (var index = 0; index < body.all.length; index++) {
		tag = body.all[index];
		//if (tag.Attribute["className"].indexOf("mso") > -1)
		tag.removeAttribute("className","",0);
		tag.removeAttribute("style","",0);
	}

	// Regex based cleaning
	var html = editor.document.body.innerHTML;
	html = html.replace(/<o:p>&nbsp;<\/o:p>/gi, "");
	html = html.replace(/o:/gi, "");
	//html = html.replace(/<st1:[^>]*>/gi, "");

	// Final clean up of empty tags
	html = html.replace(/<font[^>]*>\s*<\/font>/gi, "");
	html = html.replace(/<span>\s*<\/span>/gi, "");

	editor.document.body.innerHTML = html;
}*/

function WBTB_RemoveElem(obj,tag)
{
	var aElem = obj.getElementsByTagName(tag);
	for (var i = aElem.length - 1; i >= 0; i--) {
		aElem[i].removeNode(true)
	}
}

function WBTB_RemoveComment(str)
{
	var start, end;
		
	while(true) {
		start = str.indexOf("<!--");
		if (start == -1) // not find
			break;
		
		end = str.indexOf("-->");	
		end = start > end ? (start + 3) : (end + 2);
		str = str.replace(str.substring(start, end), "");
	}
	
	return str;
}

function WBTB_CleanCode(frm)
{
	var editor = eval(frm);
	editor.focus();
	
	var arr = showModalDialog("/gsps/htmleditor/cleancode.htm", "", "dialogWidth:22.5em;dialogHeight:27em;status:no;scroll:no;help:no");

	if (arr != null) {
		var eBody = editor.document.body;
		var eHtml = eBody.innerHTML;
		var RegExp;
		
		// 清理注释
		eHtml = WBTB_RemoveComment(eHtml);
		eBody.innerHTML = eHtml;
		
		if (arr[0] == 0) { // 彻底清理
			// Regex based cleaning
			eHtml = eBody.innerHTML;
			eHtml = eHtml.replace(/<o:p>&nbsp;<\/o:p>/gi, "");
			eHtml = eHtml.replace(/o:/gi, "");
			eBody.innerHTML = eHtml;
	
			// 清理STYLE、SCRIPT、NOSCRIPT、EMBED、OBJECT、IFRAME、TEXTAREA、SELECT
			WBTB_RemoveElem(eBody, "STYLE");
			WBTB_RemoveElem(eBody, "SCRIPT");
			WBTB_RemoveElem(eBody, "NOSCRIPT");
			WBTB_RemoveElem(eBody, "EMBED");
			WBTB_RemoveElem(eBody, "OBJECT");
			WBTB_RemoveElem(eBody, "IFRAME");
			WBTB_RemoveElem(eBody, "TEXTAREA");
			WBTB_RemoveElem(eBody, "SELECT");
			
			// 为了彻底地清理（上面的方法可能有残留）
			eHtml = eBody.innerHTML;
			RegExp = /<(STYLE|SCRIPT|NOSCRIPT|EMBED|OBJECT|IFRAME|TEXTAREA|SELECT)[^<>]*>[^<>]*<\/\1\s*>/gi;
 			eHtml = eHtml.replace(RegExp, "");
 			eBody.innerHTML = eHtml;

			
			// 清理HTML标记 (保留<P></P>、<BR>、图片和自己的链接)
			// 先替换 <P></P>、<BR>、<A></A>、<IMG>，用"{3D718B82-C1DF-48f0-9181-5F5AA7081E49}"、"{B0EF3E72-55CD-4cd3-899C-36365592C752}"是为了防止和其它的字符串混淆
			eHtml = eBody.innerHTML;
			RegExp = /<(\/?)(P|A|BR|IMG)([^<>]*)>/gi;
			eHtml = eHtml.replace(RegExp, "{3D718B82-C1DF-48f0-9181-5F5AA7081E49}$1$2$3{B0EF3E72-55CD-4cd3-899C-36365592C752}");
			
			// 清除所有标记
			RegExp = /<[^<>]*>/gi;
			eHtml = eHtml.replace(RegExp, "");
			
			// 恢复
			RegExp = /\{3D718B82-C1DF-48f0-9181-5F5AA7081E49\}/gi;
			eHtml = eHtml.replace(RegExp, "<");
			RegExp = /\{B0EF3E72-55CD-4cd3-899C-36365592C752\}/gi;
			eHtml = eHtml.replace(RegExp, ">");			
			eBody.innerHTML = eHtml;
			
			// 清理非自己的链接	
			var aLink = eBody.getElementsByTagName("A");
			for (var i = aLink.length - 1; i >= 0; i--) {
				var sURL = aLink[i].getAttribute("href");
				if (sURL != null && sURL.search(/ifeng\./i) == -1) {
					aLink[i].outerHTML = aLink[i].innerText;
				}
			}
			
			// 只保留JPG/JPEG图片
			var aImg = eBody.getElementsByTagName("IMG");
			for (var i = aImg.length - 1; i >= 0; i--) {
				var sSrc = aImg[i].getAttribute("SRC");
				if (sSrc != null && sSrc.search(/\.(jpg|jpeg)$/i) == -1) {
					aImg[i].removeNode(true);
				}
			}				
		}
		else { // 选择清理
			// 清理SCRIPT
			if (arr[1]) {
				WBTB_RemoveElem(eBody, "STYLE");
				WBTB_RemoveElem(eBody, "SCRIPT");
				WBTB_RemoveElem(eBody, "NOSCRIPT");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(STYLE|SCRIPT|NOSCRIPT)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理IFRAME
			if (arr[2]) {
				WBTB_RemoveElem(eBody, "IFRAME");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(IFRAME)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理Flash等
			if (arr[3]) {
				WBTB_RemoveElem(eBody, "EMBED");
				WBTB_RemoveElem(eBody, "OBJECT");
				
				// 为了彻底地清理
				eHtml = eBody.innerHTML;
				RegExp = /<(EMBED|OBJECT)[^<>]*>(.|\n)*<\/\1\s*>/gi;
 				eHtml = eHtml.replace(RegExp, "");
 				eBody.innerHTML = eHtml;
			}
			
			// 清理GIF广告图片
			if (arr[4]) {
				// Image				
				var aGIF = eBody.getElementsByTagName("IMG");
				for (var i = aGIF.length - 1; i >= 0; i--) {
					var sSrc = aGIF[i].getAttribute("SRC");
					if (sSrc != null && sSrc.search(/\.gif$/i) != -1) { // Is gif
						var cond1 = (aGIF[i].getAttribute("width") <= arr[5]);
						var cond2 = (aGIF[i].getAttribute("height") <= arr[7]);
						var cond = ((arr[6] == "And") ? (cond1 && cond2) : (cond1 || cond2));
						if (cond) {
							aGIF[i].removeNode(true);
						}
					}
				}
				
				// Background		
				for (var i = eBody.all.length - 1; i >= 0; i--) {
					var aElem = eBody.all[i];
					var sBG = aElem.getAttribute("BACKGROUND");
					if (sBG != null && sBG.search(/\.gif$/i) != -1) { // Is gif
						var cond1 = (aElem.getAttribute("width") <= arr[5]);
						var cond2 = (aElem.getAttribute("height") <= arr[7]);
						var cond = ((arr[6] == "And") ? (cond1 && cond2) : (cond1 || cond2));
						if (cond) {
							aElem.removeAttribute("BACKGROUND","",0);
						}
					}
				}
			}
			
			// 清理非自己的链接
			if (arr[8]) {
				var aLink = eBody.getElementsByTagName("A");
				for (var i = aLink.length - 1; i >= 0; i--) {
					var sURL = aLink[i].getAttribute("href");
					if (sURL != null && sURL.search(/ifeng\./i) == -1) {
						aLink[i].outerHTML = aLink[i].innerText;
					}
				}
			}
			
			// 清理非自己的图片
			if (arr[9]) {
				// Image
				var aImg = eBody.getElementsByTagName("IMG");
				for (var i = aImg.length - 1; i >= 0; i--) {
					var sSrc = aImg[i].getAttribute("SRC");
					if (sSrc != null && sSrc.search(/ifeng\./i) == -1) {
						aImg[i].removeNode(true);
					}
				}
				
				// Background		
				for (i = eBody.all.length - 1; i >= 0; i--) {
					var aElem = eBody.all[i];
					var sBG = aElem.getAttribute("BACKGROUND");
					if (sBG != null && sBG.search(/ifeng\./i) == -1) {
						aElem.removeAttribute("BACKGROUND","",0);
					} // end if
				} // end for
			} // end if
		} // end if...else
		
		// 清理注释
		eHtml = eBody.innerHTML;
		eHtml = WBTB_RemoveComment(eHtml);
	
		// 清理多余空行	
		RegExp = /(<P>\s*<\/P>)+/gi;
 		eHtml = eHtml.replace(RegExp, "<P></P>");
 		RegExp = /(<BR>\s*)+/gi;
 		eHtml = eHtml.replace(RegExp, "<BR>");
 		eBody.innerHTML = eHtml;
	} // end if
 				
	editor.focus();
}
// End modified

// Added by jingtao
/*
function WBTB_InsertPage(frm)
{
	var editor = eval(frm);
	
	// 当前插入点之前已存在的分页标记个数
	var n = 0;
	
	// 当前位置
	var oRange = editor.document.selection.createRange();	
	if (oRange != null) {
		// 全部范围
		var oRange1 = oRange.duplicate();
		oRange1.expand("textedit");
  	oRange1.collapse();
  	
  	if (oRange1 != null) {
			// 从开始到当前位置的范围
			var oRange2 = oRange.duplicate();
			oRange2.setEndPoint("StartToStart", oRange1);
			
			// 计算当前插入点之前已存在的分页标记个数
			if (oRange2 != null) {
				var r = oRange2.htmlText.match(/\[page title=[^\]]*\]/g);
				if (r != null) {
					n = r.length;
				}
			}
		}
	}
	
	var oElem, oTitle;
	var collInput = document.all.tags("INPUT");
	if (collInput != null) {
		for (var i = 0; i < collInput.length; i++) {
			oElem = collInput[i];
			if (oElem.value == "Article.Title" && oElem.type == "hidden") {
				var sName = oElem.name;
				sName = sName.replace("_FORM_AP_", "_FORM_PF_");
				oTitle = document.all(sName);
				break;
			}
		}
	}
	
	var sOldTitle = "";
	if (oTitle != null && oTitle.type == "text") {		
		sOldTitle = oTitle.value + "(" + (n + 2) + ")";
	}
	
	var arr = showModalDialog("/gsps/htmleditor/page.htm", sOldTitle, "dialogWidth:22em;dialogHeight:15em;status:no;scroll:no;help:no");

	if (arr != null) {
		var sNewTitle = "";
		if (!arr[0] && arr[1] != null) sNewTitle = arr[1];		
		WBTB_InsertSymbol(frm, "[page title=" + sNewTitle + "]");
	}
	
	editor.focus();
}
*/

function WBTB_InsertPage(frm)
{
	var editor = eval(frm);
	var article = frm.replace(/^WBTB_Composition_/, "_FORM_AP_");
	if (document.all(article).value == "Article.Content") {
		WBTB_InsertSymbol(frm, "[page title=]");
	} else {
		alert("此模板域非文章正文属性，不能使用分页功能！");
	}	
	editor.focus();
}
// End added

function WBTB_FilterScript(content)
{
	content = WBTB_rCode(content, 'javascript:', 'javascript :');
	//var RegExp = /<script[^>]*>(.|\n)*<\/script>/ig;
	//content = content.replace(RegExp, "<!-- Script Filtered -->");
	var RegExp = /<script[^>]*>/ig;
	content = content.replace(RegExp, "<!-- Script Filtered/n");
	RegExp = /<\/script>/ig;
	content = content.replace(RegExp, "/n-->");
	return content;
}

////function WBTB_cleanHtml()
function WBTB_cleanHtml(frm)
{
	////var fonts = WBTB_Composition.document.body.all.tags("FONT");
	var editor = eval(frm);
	var fonts = editor.document.body.all.tags("FONT");
	
	var curr;
	for (var i = fonts.length - 1; i >= 0; i--) {
		curr = fonts[i];
		if (curr.style.backgroundColor == "#ffffff") curr.outerHTML = curr.innerHTML;
	}
}

////function WBTB_getPureHtml()
function WBTB_getPureHtml(frm)
{
	var str = "";
	//var paras = WBTB_Composition.document.body.all.tags("P");
	//if (paras.length > 0){
	//  for	(var i=paras.length-1; i >= 0; i--) str= paras[i].innerHTML + "\n" + str;
	//} else {
	
	////str = WBTB_Composition.document.body.innerHTML;
	var editor = eval(frm);
	str = editor.document.body.innerHTML;
	
	//}
	str=WBTB_correctUrl(str);
	return str;
}

function WBTB_updateContent(cont)
{
	// 将所有标记转换成小写
	var re = new RegExp("<\/?[A-Z]+", "g");
	var arr, str;
	while ((arr = re.exec(cont)) != null) {
		str = arr.toString();
		cont = cont.replace(str, str.toLowerCase());
	}
	
	// 替换转意字符
	while (cont.match(r1) != null || cont.match(r2) != null
			|| cont.match(r3) != null || cont.match(r4) != null)
	{
		var r1 = new RegExp("<([^>]*)&lt;([^<>]*)>", "g");
		cont = cont.replace(r1, "<$1<$2>");
		
		var r2 = new RegExp("<([^>]*)&gt;([^<>]*)>", "g");
		cont = cont.replace(r2, "<$1>$2>");
		
		var r3 = new RegExp("<([^>]*)&amp;([^<>]*)>", "g");
		cont = cont.replace(r3, "<$1&$2>");
		
		var r4 = new RegExp("<([^>]*)&quot;([^<>]*)>", "g");
		cont = cont.replace(r4, "<$1\"$2>");
	}
	
	return cont;
}

function WBTB_correctUrl(cont)
{
	// 先替换特殊字符
	cont = WBTB_updateContent(cont);
	
	var url=location.href.substring(0,location.href.lastIndexOf("/")+1);
	cont=WBTB_rCode(cont,location.href+"#","#");
	cont=WBTB_rCode(cont,url,"");

	//解决切换HTML/Design时误加内部绝对链接
	var url2 = 'href=\"' + location.href.substring(0,location.href.indexOf("/cgi-bin"));
	cont=WBTB_rCode(cont,url2,'href="');
	return cont;
}

var WBTB_bLoad=false
var WBTB_pureText=true
////var WBTB_bTextMode=true

WBTB_public_description=new WBTB_Editor

function WBTB_Editor()
{
	this.put_HtmlMode=WBTB_setMode;
	this.put_value=WBTB_putText;
	this.get_value=WBTB_getText;
}

////function WBTB_getText()
function WBTB_getText(bTextMode,frm)
{
	var editor = eval(frm);
	
	////if (WBTB_bTextMode)
	if (bTextMode)
		////return WBTB_Composition.document.body.innerText;
		return editor.document.body.innerText;
	else
	{
		////WBTB_cleanHtml();
		WBTB_cleanHtml(frm);
		
		////return WBTB_Composition.document.body.innerHTML;
		return editor.document.body.innerHTML;
	}
}

////function WBTB_putText(v)
function WBTB_putText(v,bTextMode,frm)
{
	var editor = eval(frm);
	
	////if (WBTB_bTextMode)
	if (bTextMode)
		////WBTB_Composition.document.body.innerText = v;
		editor.document.body.innerText = v;
	else
		////WBTB_Composition.document.body.innerHTML = v;
		editor.document.body.innerHTML = v;
}

////function WBTB_InitDocument(hiddenid, charset)
function WBTB_InitDocument(hiddenid,charset,bTextMode,frm)
{
	if (charset!=null)
		WBTB_charset=charset;
	var WBTB_bodyTag="<html><head><style type=text/css>.quote{margin:5px 20px;border:1px solid #CCCCCC;padding:5px; background:#F3F3F3 }\nbody{boder:0px}</style></head><BODY bgcolor=\"#FFFFFF\" >";
	
	////var editor=WBTB_Composition;
	var editor = eval(frm);
	
	var h=document.getElementById(hiddenid);
	editor.document.designMode="On"
	editor.document.open();
	editor.document.write(WBTB_bodyTag);
	if (h.value!="")
	{
		editor.document.write(h.value);
	}
	editor.document.write("</html>");
	editor.document.close();
	editor.document.body.contentEditable = "True";
	editor.document.charset=WBTB_charset;
	
	WBTB_bLoad=true;
	
	////WBTB_setStyle();
	WBTB_setStyle(bTextMode,frm);
	
	//eval("WBTB_Composition.document.body.innerHTML+=(self.opener."+ htmlableID +".checked)?(self.opener."+bodyID+".value):(WBTB_ubb2html(self.opener."+ bodyID +".value))");
}


////function WBTB_doSelectClick(str,el) {
function WBTB_doSelectClick(str,el,bTextMode,frm) {
	var Index = el.selectedIndex;
	if (Index != 0){
		el.selectedIndex = 0;
		////WBTB_format(str,el.options[Index].value);
		WBTB_format(str,bTextMode,frm,el.options[Index].value);
	}
}

var WBTB_bIsIE5 = (navigator.userAgent.indexOf("IE 5")  > -1) || (navigator.userAgent.indexOf("IE 6")  > -1) || (navigator.userAgent.indexOf("IE 7")  > -1);
var WBTB_edit;	//selectRang
var WBTB_RangeType;
var WBTB_selection;

//应用html
function WBTB_specialtype(frm, Mark1, Mark2){
	var strHTML;
	if (WBTB_bIsIE5) WBTB_selectRange(frm);
	if (WBTB_RangeType == "Text"){
		if (Mark2==null)
		{
			strHTML = "<" + Mark1 + ">" + WBTB_edit.htmlText + "</" + Mark1 + ">";
		}else{
			strHTML = Mark1 + WBTB_edit.htmlText +  Mark2;
		}
		WBTB_edit.pasteHTML(strHTML);
		var execs = frm + ".focus()";
		eval(execs);
		WBTB_edit.select();
	}
}

//选择内容,插入图片
function WBTB_InsertImageTag(frm,obj)
{
	//WBTB_Composition.focus();
	var execs = frm + ".focus()";
	eval(execs);
	if (WBTB_bIsIE5) WBTB_selectRange(frm);
	WBTB_edit.pasteHTML(obj.value);
}


//选择内容替换文本
function WBTB_InsertSymbol(frm,str1)
{
	//WBTB_Composition.focus();
	var execs = frm + ".focus()";
	eval(execs);
	if (WBTB_bIsIE5) WBTB_selectRange(frm);
	WBTB_edit.pasteHTML(str1);
}


function WBTB_selectRange(frm){
	var execs = "WBTB_selection = " + frm + ".document.selection";
	eval(execs);
	execs = "WBTB_edit = " + frm + ".document.selection.createRange()";
	eval(execs);
	execs = "WBTB_RangeType =  " + frm + ".document.selection.type";
	eval(execs);
}


function WBTB_rCode(s,a,b,i){
	//s原字串，a要换掉pattern，b换成字串，i是否区分大小写
	a = a.replace("?","\\?");
	if (i==null)
	{
		var r = new RegExp(a,"gi");
	}else if (i) {
		var r = new RegExp(a,"g");
	}
	else{
		var r = new RegExp(a,"gi");
	}
	return s.replace(r,b);
}


//提交数据到opener，已无用
/*
function WBTB_handin()
{
	if (!WBTB_validateMode()) return;
	var strHTMLbegin;
	var strHTMLend;
	strHTMLbegin = "";
	strHTMLend = "";
//	eval("self.opener."+bodyID+".value=strHTMLbegin + WBTB_getPureHtml(WBTB_Composition.document.body.innerHTML) + strHTMLend;self.opener."+htmlableID+".checked=true;");
	self.close();
}
*/

////function WBTB_View(objField)
function WBTB_View(frm)
{
	/*
	if (WBTB_bTextMode)
	{
		//切换成设计模式
		WBTB_setMode(objField);
	}
	*/
	
	////cont=WBTB_Composition.document.body.innerHTML;
	var editor = eval(frm);
	cont=editor.document.body.innerHTML;
	
	cont=WBTB_correctUrl(cont);
	
	document.form_view.content.value=cont;
	document.form_view.submit();
	return;
}


// 修改编辑栏高度,design状态有效
////function WBTB_Size(num)
function WBTB_Size(num,container,bTextMode)
{
	////if (!WBTB_bTextMode)	//if condition by minghui
	if (!bTextMode)
	{
		////var obj=document.all.WBTB_Container;
		var obj=document.all(container);
		
		if (parseInt(obj.height)+num>=300) {
			obj.height = parseInt(obj.height) + num;
		}
		if (num>0)
		{
			obj.width="100%";
		}
	}
}

/*
function WBTB_ubbcode(){
	if (!WBTB_validateMode()) return;
	cont=WBTB_getPureHtml(WBTB_Composition.document.body.innerHTML);
	var aryCode0 = new Array("<strong>","[b]","</strong>","[/b]","<p","[p","</p>","[/p]","<a href=","[url=","</a>","[/url]");
	var aryCode1 = new Array("<em>","[i]","</em>","[/i]","<u>","[u]","</u>","[/u]","<ul>","[list]","</ul>","[/list]","<ol>","[list=1]","</ol>","[/list]");
	var aryCode2 = new Array("<li>","[*]","</li>","","<font color=","[color=","<font face=","[font=","<font size=","[size=");
	var aryCode9 = new Array(">","]","<","[","</","[/");
	var aryCode = aryCode0.concat(aryCode1).concat(aryCode2).concat(aryCode9);

	for (var i=0;i<aryCode.length;i+=2){
		cont=WBTB_rCode(cont,aryCode[i],aryCode[i+1]);
	}
//	eval("self.opener."+bodyID+".value+=cont;");
	self.close();
}

function WBTB_ubb2html(str){
	if (str=="")
		return str;
	var aryCode0 = new Array("<br>","\n","<strong>","\\[b]","</strong>","\\[/b]","<p","\\[p","</p>","\\[/p]","<a href=","\\[url=","</a>","\\[/url]");
	var aryCode1 = new Array("<em>","\\[i]","</em>","\\[/i]","<u>","\\[u]","</u>","\\[/u]","<ul>","\\[list]","</ul>","\\[/list]","<ol>","\\[list=1]","</ol>","\\[/list]");
	var aryCode = aryCode0.concat(aryCode1);

	for (var i=0;i<aryCode.length;i+=2){
		str=WBTB_rCode(str,aryCode[i+1],aryCode[i]);
	}
	return str;
}
*/

// 拷贝frame数据到模板域Input对象,可保证在design状态正确提交表单
////function WBTB_CopyData(oFrm,objField)
function WBTB_CopyData(oFrm,objField,bTextMode)
{
	////if (WBTB_bTextMode)
	if (bTextMode) {
		return;	//支持自动排版、相关报道等
		var execs = "cont=" + oFrm + ".document.body.innerText";
		eval(execs);
	} else {
		var execs = "cont=" + oFrm + ".document.body.innerHTML";
		eval(execs);
	}
	cont=WBTB_correctUrl(cont);
	if (WBTB_filterScript) {
		cont=WBTB_FilterScript(cont);
	}
	
	if (cont == '<p>&nbsp;</p>') {
		cont = '';
	}
		
	objField.value = cont;
}


// NOTE: Modified by jingtao
/*
function WBTB_help()
{
	showModalDialog("/gsps/htmleditor/help.html", "", "dialogWidth:13.5em;dialogHeight:12.5em;status:no;scroll:no;help:no");
}
*/

function WBTB_help()
{
	window.open ("/gsps/doc/htmleditor.htm", "_blank");
}

////////////////////////////////////////
// NOTE: jingtao
/*
function WBTB_setMode_bak()
{
	WBTB_bTextMode=!WBTB_bTextMode;
	WBTB_setTab();
	var cont;
	if (WBTB_bTextMode) {
		document.all.WBTB_Toolbars.style.display='none';
		WBTB_cleanHtml();
		cont=WBTB_Composition.document.body.innerHTML;
		cont=WBTB_correctUrl(cont);
		if (WBTB_filterScript)
			cont=WBTB_FilterScript(cont);
		WBTB_Composition.document.body.innerText=cont;
	} else {
		document.all.WBTB_Toolbars.style.display='';
		cont=WBTB_Composition.document.body.innerText;
		cont=WBTB_correctUrl(cont);
		if (WBTB_filterScript)
			cont=WBTB_FilterScript(cont);
		WBTB_Composition.document.body.innerHTML=cont;
	}
	WBTB_setStyle();
	WBTB_Composition.focus();
}

// 拷贝数据到hidden
function WBTB_CopyData_bak(hiddenid)
{
	d = WBTB_Composition.document;
	if (WBTB_bTextMode)
	{
		cont=d.body.innerText;
	}else{
		cont=d.body.innerHTML;
	}
	cont=WBTB_correctUrl(cont);
	if (WBTB_filterScript)
		cont=WBTB_FilterScript(cont);
	document.getElementById(hiddenid).value = cont;
	if (document.getElementById(hiddenid).value == '<P>&nbsp;</P>')
	{
		document.getElementById(hiddenid).value = '';
	}
}

function WBTB_View_bak()
{
	if (WBTB_bTextMode) {
		cont=WBTB_Composition.document.body.innerText;
	} else {
		cont=WBTB_Composition.document.body.innerHTML;
	}
	cont=WBTB_correctUrl(cont);
	bodyTag="<html><head><style type=text/css>.quote{margin:5px 20px;border:1px solid #CCCCCC;padding:5px; background:#F3F3F3 }\nbody{boder:0px; font-family:Arial; font-size:10.5pt}</style></head><BODY bgcolor=\"#FFFFFF\" >";
	if (WBTB_filterScript)
		cont=WBTB_FilterScript(cont);
	cont=WBTB_rCode(cont,"\\[dvnews_ad]","<img src='wbTextBox/images/pic_ad.jpg' vspace=10 hspace=10 align=left border=1 title='Advertising'>");
	cont=WBTB_rCode(cont,"\\[dvnews_page]","<br><br><hr size=2 width=95% align=left>&nbsp; <font color=red face='Tahoma,Arail' size=2><b>Next Page ...</b></font><br><hr size=2 width=95% align=left>");
	preWin=window.open('preview','','left=0,top=0,width=550,height=400,resizable=1,scrollbars=1, status=1, toolbar=1, menubar=0');
	//preWin.document.open();
	preWin.document.write(bodyTag);
	preWin.document.write(cont);
	preWin.document.close();
	preWin.document.title="Preview";
	preWin.document.charset=WBTB_charset;
}
*/
