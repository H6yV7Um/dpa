var leftStatus = "ON";
var rightStatus = "OFF";

//操作工具栏的关闭和打开
function switchLeftSysBar()
{
	if(document.getElementById("switchLeftPoint").innerText==3)
	{
		document.getElementById("switchLeftPoint").innerText=4;
		document.getElementById("frmLeft").style.display="none";
	}
	else
	{
		document.getElementById("switchLeftPoint").innerText=3;
		document.getElementById("frmLeft").style.display="";
	}
}

//操作工具栏的关闭和打开
function switchRightSysBar()
{
	if(document.getElementById("switchRightPoint").innerText==4)
	{
		document.getElementById("switchRightPoint").innerText=3;
		document.getElementById("frmRight").style.display="none";
	}
	else
	{
		document.getElementById("switchRightPoint").innerText=4;
		document.getElementById("frmRight").style.display="";
	}
}
