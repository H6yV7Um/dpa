document.write('<style>');
document.write('TD.Tree_FOLDER_1{background-color:#B5C7DE;border:1pt solid #B5C7DE;height:0pt;padding:0pt 5pt 0pt 2pt;}');
document.write('TD.Tree_FOLDER_2{background-color:#B5C7DE;color:#FFFFFF;border:0pt;height:0pt;padding:0pt 5pt 0pt 2pt;}');
document.write('TD.Tree_FOLDER_3{background-color:#C7C1C0;border:1pt solid #000000;height:0pt;padding:0pt 5pt 0pt 2pt;}');
document.write('TD.Tree_FILE_1{height:0pt;padding:1pt 5pt 1pt 2pt;}');
document.write('TD.Tree_FILE_2{text-decoration:underline;color:#C96C45;height:0pt;padding:1pt 5pt 1pt 2pt;}');
document.write('TD.Tree_FILE_3{text-decoration:underline;color:#0000FF;height:0pt;padding:1pt 5pt 1pt 2pt;}');
document.write('</style>');

var tree=new Tree_treeView();
tree.useImage=true;
tree.useTitleAsHint=true;
tree.useTitleAsStatus=true;
tree.useHint=true;
tree.useStatus=true;
tree.showSelect=true;

tree.showLine=true;//显示结点连线

tree.Indent=13;//缩进量

//tree.useHint = true; //是否显示提示文字

//tree.useStatus = true; //是否显示状态栏文字

//tree.useImage=false;//不使用图标

tree.showSelect = false; // 布尔型。是否高亮度显示选择的结点。默认值 true

tree.folderImg1="/dpa_html/images/TreeMaker/clsfld.gif";//默认文件夹折叠图标

tree.folderImg2="/dpa_html/images/TreeMaker/openfld.gif";//默认文件夹展开图标

tree.lineFolder="/dpa_html/images/TreeMaker/";

tree.fileImg="/dpa_html/images/TreeMaker/link.gif";//文件图标

//tree.target="parent.frames[1]";//目标框架
tree.target="parent.frmCenter";//目标框架

//若要兼容Netscape 6.X，要设为parent.frames[1]。
//tree.target="parent.frames(0).frames(1)";//目标框架

tree.folderClass1="Tree_FOLDER_1";//文件夹样式(正常状态)
tree.folderClass2="Tree_FOLDER_2";//文件夹样式(鼠标位于文件夹上时)
tree.folderClass3="Tree_FOLDER_3";//文件夹样式(选择状态)
tree.fileClass1="Tree_FILE_1";//文件样式(正常状态)
tree.fileClass2="Tree_FILE_2";//文件样式(鼠标位于文件上时)
tree.fileClass3="Tree_FILE_3";//文件样式(选择状态)
