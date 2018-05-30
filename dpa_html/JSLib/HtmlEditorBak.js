//===========================================
// HTML_Editor Javascrip支持
//===========================================

var IFramesHTMLEditMode = new Array();


//===========================================
//	初始化所有的HTMLEditor为设计模式
//===========================================
function document.onreadystatechange()
{
	var f_name;
	var j = 0;
	for(var i=0; i<document.frames.length ;i++)
	{
		f_name = document.frames(i).name;
		if(f_name.substr(0,16) == "_HTML_Editor_IFM")
		{
			document.frames(i).document.designMode = "On";
			document.frames(i).document.bgcolor = "darkgray";
			var IFrameHTMLEditMode = new Array();
			IFrameHTMLEditMode[0] = document.frames(i);
			IFrameHTMLEditMode[1] = false;
			IFramesHTMLEditMode[j] = IFrameHTMLEditMode;
			j++;
		}
	}
}


//===========================================
//	判断指定的HTML_Editor的模式
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:
//		true-文档处于HTML编辑模式,不能执行脚本命令
//		false-文档未处于HTML编辑模式,能执行脚本命令
//===========================================

function isHTMLMode(oiframe)
{
	var i;
	for(i = 0; i < IFramesHTMLEditMode.length; i++)
	{
		if(IFramesHTMLEditMode[i][0] == oiframe
			&& IFramesHTMLEditMode[i][1])
		{
			return true;
		}
	}
	return false;
}



//===========================================
//	设置指定的HTML_Editor的模式
//	参数:
//	oiframe-HTMLEditor对象
//		true-文档处于HTML编辑模式,不能执行脚本命令
//		false-文档未处于HTML编辑模式,能执行脚本命令
//	返回值:无
//===========================================
function setHTMLMode(oiframe, bmode)
{
	var i;
	for(i = 0; i < IFramesHTMLEditMode.length; i++)
	{
		if(IFramesHTMLEditMode[i][0] == oiframe)
		{
			IFramesHTMLEditMode[i][1] = bmode;
		}
	}
}


//===========================================
//	切换HTML_Editor的编辑模式
//	参数:
//		oiframe-HTMLEditor对象
//		otoolbar-当前HTML_Editor绑定的工具条
//		bMode:
//			true-文档处于HTML编辑模式,不能执行脚本命令
//			false-文档未处于HTML编辑模式,能执行脚本命令
//	返回值:无
//===========================================

function setMode(oiframe, otoolbar, bMode)
{
	var sTmp;
	setHTMLMode(oiframe, bMode);
  	if (isHTMLMode(oiframe)) 
  	{
		sTmp = oiframe.document.body.innerHTML;
		oiframe.document.body.innerText=sTmp;
		otoolbar.style.display = 'none';
	} 
	else 
	{
		sTmp = oiframe.document.body.innerText;
		oiframe.document.body.innerHTML=sTmp;
		otoolbar.style.display = 'inline';
	}
  	oiframe.focus();
}



//-------------------------------------------------------------
// 切换HTML_Editor的编辑模式
// 参数:
//		oForm:当前的表单对象
//		idHTMLEditor:提供可视化编辑的内部帧对象ID
//		idIFrame:提供可视化编辑的内部帧对象ID
//		szTextArea:提供代码编辑的TextArea对象名称
//		oSender:事件发送者
//-------------------------------------------------------------
function On_HTMLEdit_Click(oForm, HTMLEditor, oIFrame, szTextArea, oSender)
{
	if(oSender.checked)
	{
		setHTMLMode(oIFrame, false);
		oForm.elements[szTextArea].style.display = "none";
		HTMLEditor.style.display = "block";
		oIFrame.document.body.innerHTML=oForm.elements[szTextArea].value;
	}
	else
	{
		setHTMLMode(oIFrame, true);
		oForm.elements[szTextArea].style.display = "inline";
		HTMLEditor.style.display = "none";
		oForm.elements[szTextArea].value = oIFrame.document.body.innerHTML;
	}
}


//===========================================
//	插入本模板相关图片
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function insertImageLocal(oiframe,p_id,t_id) 
{
	if (isHTMLMode(oiframe)) 
	{
		alert("Please uncheck 'Edit HTML'");
		return;
	}
	var sImgSrc = showModalDialog("selectTemplateImage.pl?_p_id=" + p_id + "&_t_id=" + t_id ,"dialogHeight: 500px; dialogWidth: 400px; dialogTop: px; dialogLeft: px; edge: Raised; center: Yes; help: No; resizable: Yes; status: No;");
	if(sImgSrc!=null) cmdExec("InsertImage",sImgSrc);
}

//===========================================
//	插入本模板相关图片
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function createLink(oiframe) 
{
	if (isHTMLMode(oiframe)) {
		alert("Please uncheck 'Edit HTML'");
		return;
	}
	cmdExec("CreateLink");
}

//===========================================
//	执行指定脚本命令
//	参数:
//		oiframe-HTMLEditor对象
//		cmd:执行命令
//		opt:命令参数
//	返回值:无
//===========================================
function cmdExec(oiframe,cmd,opt) 
{
  	if (isHTMLMode(oiframe)) 
  	{
		alert("请进入编辑模式！");
		return;
	}
  	oiframe.document.execCommand(cmd,"",opt);
	oiframe.focus();
}

//===========================================
//	用途:鼠标停留在用途按钮上事件句柄
//	参数:
//		eButton-按钮对象对象
//	返回值:无
//===========================================
function button_over(eButton)	
{
	eButton.style.backgroundColor = "#B5BDD6";
	eButton.style.borderColor = "darkblue darkblue darkblue darkblue";
	eButton.style.borderWidth = '1px';
	eButton.style.borderStyle = 'solid'; 
}

//===========================================
//	用途:鼠标离开用途按钮上事件句柄
//	参数:
//		eButton-按钮对象对象
//	返回值:无
//===========================================
function button_out(eButton) 
{
	eButton.style.backgroundColor = "#6699CC";
	eButton.style.borderColor = "#6699CC";
}

//===========================================
//	用途:鼠标按下用途按钮上事件句柄
//	参数:
//		eButton-按钮对象对象
//	返回值:无
//===========================================
function button_down(eButton) 
{
	eButton.style.backgroundColor = "#8494B5";
	eButton.style.borderColor = "darkblue darkblue darkblue darkblue";
}

//===========================================
//	用途:鼠标离开用途按钮上事件句柄
//	参数:
//		eButton-按钮对象对象
//	返回值:无
//===========================================
function button_up(eButton) 
{
	eButton.style.backgroundColor = "#B5BDD6";
	eButton.style.borderColor = "darkblue darkblue darkblue darkblue";
	eButton = null; 
}

//===========================================
//	用途:插入外部图片链接
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function insertImageLink(oiframe) 
{
	if (isHTMLMode(oiframe)) 
	{
		alert("Please uncheck 'Edit HTML'!");
		return;
	}
	var sImgSrc=prompt("插入外部图片链接(如:http://image.domain.com/xxx.jpg", "");
	if(sImgSrc!=null) cmdExec(oiframe,"InsertImage",sImgSrc);
}


//===========================================
//	用途:颜色挑选器
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function foreColor(oiframe)	
{
	var arr = showModalDialog("selcolor.pl","","font-family:Verdana; font-size:12; dialogWidth:45em; dialogHeight:24em" );
	if (arr != null) cmdExec(oiframe,"ForeColor",arr);	
}


//===========================================
//	用途:弹出HTML Table制作对话框
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function tableDialog(oiframe)
{
   //----- Creates A Table Dialog And Passes Values To createTable() -----
   var rtNumRows = null;
   var rtNumCols = null;
   var rtTblAlign = null;
   var rtTblWidth = null;
   showModalDialog("/publish/table.htm",window,"status:false;dialogWidth:16em;dialogHeight:13em");
}

//===========================================
//	用途:制作HTML Table
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function createTable(oiframe)
{
   //----- Creates User Defined Tables -----
   var cursor = oiframe.document.selection.createRange();
   if (rtNumRows == "" || rtNumRows == "0")
   {
      rtNumRows = "1";
   }
   if (rtNumCols == "" || rtNumCols == "0")
   {
      rtNumCols = "1";
   }
   var rttrnum=1
   var rttdnum=1
   var rtNewTable = "<table border='1' align='" + rtTblAlign + "' cellpadding='0' cellspacing='0' width='" + rtTblWidth + "'>"
   while (rttrnum <= rtNumRows)
   {
      rttrnum=rttrnum+1
      rtNewTable = rtNewTable + "<tr>"
      while (rttdnum <= rtNumCols)
      {
         rtNewTable = rtNewTable + "<td>&nbsp;</td>"
         rttdnum=rttdnum+1
      }
      rttdnum=1
      rtNewTable = rtNewTable + "</tr>"
   }
   rtNewTable = rtNewTable + "</table>"
   oiframe.focus();
   cursor.pasteHTML(rtNewTable);
}

//===========================================
//	用途:HTML预览
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function doPreview(oiframe){
     temp = oiframe.document.body.innerHTML;
     preWindow= open('', 'previewWindow', 'width=500,height=440,status=yes,scrollbars=yes,resizable=yes,toolbar=no,menubar=yes');
     preWindow.document.open();
     preWindow.document.write(temp);
     preWindow.document.close();
}

//===========================================
//	用途:HTML格式化操作
//	参数:
//		oiframe-HTMLEditor对象
//	返回值:无
//===========================================
function SetParagraph(oiframe,name,value) 
{
	oiframe.focus();
	if (value == '<body>')
	{
		oiframe.document.execCommand('formatBlock','','Normal');
		oiframe.document.execCommand('removeFormat');
		return;
	}
	oiframe.document.execCommand('formatblock','',value);
}