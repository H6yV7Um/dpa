<?php
header('Content-Type: text/html;charset='.$GLOBALS['cfg']['out_character_contype']);
require_once("configs/css_js_img.conf.php");
//require_once("common/functions.php");
//require_once("lang/chinese.utf8.lang.php");

$header = "";
$content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$GLOBALS['cfg']['DEFAULT_LOGIN_ACTION'].".html");
$footer = '';


if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  $l_tmp_common = "/ni9ni/htdocs/www";
}else {
  $l_tmp_common = "";
}
$l_header_file = $_SERVER['DOCUMENT_ROOT'].$l_tmp_common."/ssi/header.ssi";
$l_footer_file = $_SERVER['DOCUMENT_ROOT'].$l_tmp_common."/ssi/footer.ssi";
if (file_exists($l_header_file)) $header = file_get_contents($l_header_file);  // 标准头
if (file_exists($l_footer_file)) $footer = file_get_contents($l_footer_file);  // 标准尾


$l_data_arr = array(
  "header"=>'',
  "footer"=>'',
  "system_name"=>$GLOBALS['language']['SYSTEM_NAME_STR'],
  "l_yanzhengma"=>"",
  "l_yanzhengma_js"=>"",
  "l_web_domain"=>$GLOBALS['cfg']['WEB_DOMAIN'],
  "back_url"=>isset($this->request['back_url']) ? urlencode($this->request['back_url']) : '',
  //"clientIP"=>getip()
);
if (isset($_SESSION["ERROR_LOGIN"]) && $_SESSION["ERROR_LOGIN"]["num"]>0) {
  $l_data_arr["l_yanzhengma"]='<tr>
                <td align="right" height="35">验证码:</td>
                <td>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td><input size="8" type="text" name="aicode" id="aicode" /><span style="display:none" id="msg_yanzhenma">验证码有误</span></td>
            <td width="72" align="center"><img id="check_img" src="/common/lib/aipic.php?create=yes" style="cursor:pointer" width="60" height="20" alt="看不清？点击更换一张" onclick="con_code()" /></td>
            <td><a href="javascript:con_code()">换一张</a></td>
          </tr></table>
        </td>
              </tr>';
  $l_data_arr["l_yanzhengma_js"]='if( "" == frm["aicode"].value ){
    errnum++;
    errsrt += "- “验证码”不能为空！\n";
  }';
}
$content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);
$content = replace_template_para($response['action_error'],$content,true);
$content = replace_template_para($l_data_arr,$content);

echo $content;
