var gobjCommuWindow;
//var isUnexpectedUnload = true;
function logout(){
	if(MWalert(2, "您要退出办公信息平台吗?") != "VbYes")
		return;
	if ("object" == typeof(gobjCommuWindow)){
		if (typeof(gobjCommuWindow.opener) == "object"){
			gobjCommuWindow.window.close();
		}
	}
	//isUnexpectedUnload = false;
	parent.navigate("../logoutHandle.asp");
}

function openCommu(){
	var rndName = "winChat" + parseInt(Math.random() * 1000);
	var strStrings = "../PsnOnlineState/Communicator.asp";
	gobjCommuWindow = parent.top.window.open(strStrings,null,"height=" + (screen.availHeight-50).toString() + ",width=260,status=yes,toolbar=no,menubar=no,location=no,left=" + (screen.availWidth - 270).toString() + ",top=0px");
	return;
}

function visitMindsware(){
	parent.top.window.open('http://www.mindsware.com',null);
}
/*
function checkUnload() {
	if (isUnexpectedUnload)
		event.returnValue = "办公信息平台菜单页";
}

function refresh() {
	isUnexpectedUnload = false;
	window.navigate("../Menu_new.asp");
}
*/