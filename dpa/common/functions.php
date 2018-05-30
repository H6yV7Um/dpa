<?php
// 兼容php4 begin
if (!function_exists("str_ireplace")) {
  function str_ireplace($a,$b,$arr){
    return str_replace($a,$b,$arr);
  }
}
if (!function_exists("iconv")) {
  function iconv($a,$b,$arr){
    return $arr;
  }
}
if (!function_exists("http_build_query")) {
  function http_build_query($arr){
    $str = "";
    if (is_array($arr)) {
      $i=0;
      foreach ($arr as $k=>$v){
        if ($i>0) {
          $str .= "&";
        }
        $str .= $k."=".$v;

        $i++;
      }
    }
    return $str;
  }
}
// 兼容php4 end

include_once("convCharacter.php");
require_once("common/lib/cArray.cls.php");
require_once("common/lib/cString.cls.php");
/**
 * 获取config目录下指定配置文件的配置信息
 *
 * @param string $config_dir
 * @param string $file_name
 *
 * @since 2009-09-16
 * @return array
 */
function __fetch_config($config_dir,$file_name)
{
  $configs = array();
  $d = dir($config_dir);
  if ($d) {
  while (false !== ($entry = $d->read())) {
    if ($file_name==$entry)
    {
      $tail = substr($entry, -4);
      if($tail == '.ini')
      {
        $configs = parse_ini_file($config_dir."/".$entry, true);
      }
    }
  }
  $d->close();
  }
  return $configs;
}

function &DBO($name = null, $dsn = null, $option = false)
{
  if (!isset($GLOBALS['mdb2_alias'])) $GLOBALS['mdb2_alias'] = array();

  if(is_null($dsn)) {
    if(isset($GLOBALS['mdb2_alias'][$name]) && is_object($GLOBALS['mdb2_conns'][$GLOBALS['mdb2_alias'][$name]])) {
      return $GLOBALS['mdb2_conns'][$GLOBALS['mdb2_alias'][$name]];
    }else if (false===strpos($name,"://")) {
      // 此处几乎用不到
      __gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'], $GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],"",$name);
      $db = &DBO($name, $_SERVER["SRV_DB_DSN_R"], $option);
      return $db;
    } else {
      return PEAR::raiseError('mysql config ' . $name . ' not found');
    }
  } else {
    // 数组必须转为字符串，后面的方法要求dsn为字符串
    if (is_array($dsn)) {
      $l_dsn = DbHelper::getDSNstrByProArrOrIniArr($dsn);
    }else {
      $l_dsn = $dsn;
    }
    $l_name = cString::GetAliasByDsnString($l_dsn);
    if (empty($l_name)) exit('db connect info is wrong!');  // 只要$l_dsn不为空就不会为空

    if (""==trim($name)) {
      //$name = $l_name;  // 形如 localhost:3307:root...这样的字符串
    }else {
      // 如果有 $name，此处的 $name 可能是随意指定的，也可能是存在于mysql_config.ini文件中的
      if ( !isset($GLOBALS['mdb2_alias'][$name]) ) {
        $GLOBALS['mdb2_alias'][$name] = $l_name;  // 对应一个
      }
    }

    // 存在则直接返回以前的
    if (isset($GLOBALS['mdb2_conns'][$l_name]) && !$option['new_link']) {
      return $GLOBALS['mdb2_conns'][$l_name];
    }
    $db = & MDB2::factory($dsn, $option);
    $GLOBALS['mdb2_conns'][$l_name] = &$db;
    return $db;
  }
}

function getTimezone(){
  return cArray::getTimezone();
}

// format array to add "value", "hidden" atribuite
function FmtDataAddAtr($a_arr, $a_atri=array()){
  if (empty($a_arr)) {
    return $a_arr;
  }

  foreach ($a_arr as $l_f=>$l_v){
    $a_arr[$l_f] = array("value" => $l_v);  // 覆盖原来的数组并增加一个维度
    if (array_key_exists($l_f, $a_atri)) {
      $a_arr[$l_f] = $a_atri[$l_f];  // 覆盖或附加一些属性给原数组
    }
  }

  return $a_arr;
}


//
function buildOptions($arr, $select_id=0, $display_key=true, $orderby="", $name="name"){
  $str = "";
  if (""!=$orderby) {
    $n_arr = array();
    foreach ($arr as $key => $vals){
      $n_arr[strtolower($vals[$orderby])][$key] = $vals;
    }
    ksort($n_arr);
    foreach ($n_arr as $keys){
      foreach ($keys as $vals){
        $select_con = ($select_id==$vals["id"])?'selected="selected"':"";
        $str .= '<option value="'.$vals["id"].'" '.$select_con.'>'.$vals[$orderby]." ".convCharacter($vals[$name]).'</option>';
      }
    }
  }else {
    foreach ($arr as $key => $val){
      $select_con = ($select_id==$key)?'selected="selected"':"";
      if ($display_key) {
        $str .= '<option value="'.$key.'" '.$select_con.'>'.$val.'('.$key.')</option>';
      }else {
        $str .= '<option value="'.$key.'" '.$select_con.'>'.$val.'</option>';
      }
    }
  }
  return $str;
}

// filter()
function filter($para, $ext=false){
  $invalidChar = $ext?array("'",'"',"?","\\","*","and","or","union","select"):array("'",'"',"?","\\","*");
  if (is_array($para)) {
    foreach ($para as $key=>$val){
      $para[$key] = filter(trim($val));
    }
  }else{
    $para = str_replace($invalidChar,"",trim($para));
  }
  return $para;
}

function is_utf8_encode($input){
  //$encode = mb_detect_encoding($input, "ASCII,UTF-8,CP936,EUC_CN,BIG-5,EUC-TW");
  //return $encode == "UTF-8" ? true : false;
  $str1 = @iconv("UTF-8", "GBK",  $input);
  $str2 = @iconv("GBK" , "UTF-8", $str1);
  return $input == $str2 ? true : false;
}

function iconv_special($str, $a_fangxiang="gb2utf"){
  if ("gb2utf"==$a_fangxiang) {
    $str = iconv("GBK","UTF-8//IGNORE",$str);
    $str = iconv("UTF-8","GBK//IGNORE",$str);
  }else {
    $str = iconv("UTF-8","GBK//IGNORE",$str);
    $str = iconv("GBK","UTF-8//IGNORE",$str);
  }
  return $str;
}

// get ip , encode ip
function getip() {
  if (isset($_SERVER)) {
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
      $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
      $realip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif (isset($_SERVER["REMOTE_ADDR"])) {
      $realip = $_SERVER["REMOTE_ADDR"];
    }else {
      $realip = '';  // cli模式下运行的时候会走到此步骤
    }
  } else {
    if (getenv('HTTP_X_FORWARDED_FOR')) {
      $realip = getenv( 'HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_CLIENT_IP')) {
      $realip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('REMOTE_ADDR')) {
      $realip = getenv('REMOTE_ADDR');
    }else {
      $realip = '';
    }
  }
  return $realip;
}
// 是否可以替代上面方法
function getLocalIP()
{
  if ('WIN' !== strtoupper(substr(PHP_OS, 0, 3))){
    $exec = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
    return exec($exec);
  }else {
    return getip();
  }
}

function encode_ip($dotquad_ip)
{
  $ip_sep = explode('.', $dotquad_ip);
  return sprintf('%02x%02x%02x%02x', $ip_sep[0], $ip_sep[1], $ip_sep[2], $ip_sep[3]);
}

function decode_ip($int_ip)
{
  $hexipbang = explode('.', chunk_split($int_ip, 2, '.'));
  return hexdec($hexipbang[0]). '.' . hexdec($hexipbang[1]) . '.' . hexdec($hexipbang[2]) . '.' . hexdec($hexipbang[3]);
}

function char_preg($a_charset="utf8")
{
  $l_c = array();
  $l_c['utf8']   = "/[\x01-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/e";
  $l_c['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
  $l_c['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
  $l_c['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

  if (key_exists($a_charset,$l_c)) {
    return $l_c[$a_charset];
  }else {
    return $l_c;
  }
}
// 从0开始截取一定长度的字符串, 折算为英文长度, $a_len长度不能为负值，实际应用中不会有这样的
function cn_substr($a_str, $a_len, $a_charset="utf8", $a_suffix="...")
{
  // suffix是英文字符串 ... 这样的或其他英文字符串，不要是中文的
  $l_s_len = ($a_len-strlen($a_suffix));
  $l_s_len = ($l_s_len<0)?0:$l_s_len;  // 后缀长度不能大于 a_len, 实际业务中也不会有这样的情况

  // 匹配所有的单个完整的字符和汉字
  preg_match_all(char_preg($a_charset),$a_str,$l_arr);

  $l_flag  = 0;  // 是否需要回退的标志
  $l_s_num = 0;  // 加后缀后的实际宽度
  $l_total = 0;  // 转换为字符的折算宽度，汉字算2个宽度
  $l_count = 0;  // 多少个字符，汉字算1个字符长度
  foreach($l_arr[0] as $k=> $l_v)
  {
    if(strlen($l_v)==1){
      if ($l_s_num<$l_s_len) {
        $l_s_num += 1;
        $l_count++;
      }
      $l_total += 1;
    }else {
      if ($l_s_num<$l_s_len) {
        if ($l_s_num==$l_s_len-1) $l_flag = 1;
        $l_s_num += 2;
        $l_count++;
      }
      $l_total += 2;
    }
  }
  if ($l_flag) $l_count--;  // 回退1
  // 如果总长度小于等于截取的字符串长度，则不必加后缀
  if ($l_total <= $l_s_len) $a_suffix = "";

  return join("",array_slice($l_arr[0],0,$l_count)).$a_suffix;
}

// utf8
function utf8_substr($utf8str, $from=0, $len, $chinese_len=false)
{
  if ($chinese_len) {
    $bei = 1;
  }else {
    $bei = 2;
  }
  // 匹配utf-8字符
  preg_match_all("/[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\x01-\x7f]/e",$utf8str,$ar);
  $counter=0;
  $flag=0;
  while($counter<$len&&$flag<count($ar[0]))
  {
    if(strlen($ar[0][$flag])==1)
      $counter+=0.5*$bei;
    else
      $counter+=1*$bei;
    $flag++;
  }
  return join("",array_slice($ar[0],0,$flag))."";
}

function decint2utf8str($int) { // returns UCS-16 or UCS-32 to UTF-8 from an integer
  $i=(int)$int; // integer?
  if ($i<0) return false; // positive?
  if ($i<=0x7f) return chr($i); // range 0
  if (($i & 0x7fffffff) <> $i) return '?'; // 31 bit?
  if ($i<=0x7ff) return chr(0xc0 | ($i >> 6)) . chr(0x80 | ($i & 0x3f));
  if ($i<=0xffff) return chr(0xe0 | ($i >> 12)) . chr(0x80 | ($i >> 6) & 0x3f) . chr(0x80  | $i & 0x3f);
  if ($i<=0x1fffff) return chr(0xf0 | ($i >> 18)) . chr(0x80 | ($i >> 12) & 0x3f) . chr(0x80 | ($i >> 6) & 0x3f) . chr(0x80  | $i & 0x3f);
  if ($i<=0x3ffffff) return chr(0xf8 | ($i >> 24)) . chr(0x80 | ($i >> 18) & 0x3f) . chr(0x80 | ($i >> 12) & 0x3f) . chr(0x80 | ($i >> 6) & 0x3f) . chr(0x80  | $i & 0x3f);
  return chr(0xfc | ($i >> 30)) . chr(0x80 | ($i >> 24) & 0x3f) . chr(0x80 | ($i >> 18) & 0x3f) . chr(0x80 | ($i >> 12) & 0x3f) . chr(0x80 | ($i >> 6) & 0x3f) . chr(0x80  | $i & 0x3f);
}

function escape($str,$a_div="%u"){
  // 匹配utf-8字符
  preg_match_all("/[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}|[\\x00-\\x7f]/e",$str,$r);
  $str = $r[0];

  $l = count($str);
  for($i=0; $i <$l; $i++){
    $value = ord($str[$i][0]);
    if($value < 223){
      //先将utf8编码转换为ISO-8859-1编码的单字节字符，urlencode单字节字符. utf8_decode()的作用相当于iconv("UTF-8","CP1252",$v)。
      $str[$i] = rawurlencode(utf8_decode($str[$i]));
    }else{
      $str[$i] = $a_div.strtoupper(bin2hex(iconv("UTF-8","UCS-2",$str[$i])));
    }
  }
  return join("",$str);
}

function unescape($str,$a_div="\\"){
  $ret = '';
  $len = strlen($str);
  // 增加自动判断 %u还是\u
  if ("\\" == $a_div) $a_2div = "%";
  else $a_2div = "\\";
  if (false===strpos($str, $a_div.'u') && false!==strpos($str, $a_2div.'u')) {
    $a_div = $a_2div;
  }

  for ($i = 0; $i < $len; $i++){
    if ($a_div == $str[$i]){
      if ($str[$i+1] == 'u') {
        $val = hexdec(substr($str, $i+2, 4));
        $ret .= decint2utf8str($val);
        $i += 5;
      }else {
        $ret .= urldecode(substr($str, $i, 3));
        $i += 2;
      }
    }else $ret .= $str[$i];
  }
  return $ret;
}
/**
 * 替换html模板中的css、js、图片地址路径
 *
 * @param string $content
 * @param string $new_css_path
 * @param string $new_js_path
 * @return string html_content
 */
function replace_cssAndjsAndimg($content,$new_css_path,$new_js_path,$new_img_path){
  if ($content) {
    // css替换
    $content = str_replace('href="css/','href="'.$new_css_path.'/',$content);
    $content = str_replace('href=\'css/','href=\''.$new_css_path.'/',$content);
    // js替换
    $content = str_replace('src="js/','src="'.$new_js_path.'/',$content);
    $content = str_replace('src=\'js/','src=\''.$new_js_path.'/',$content);

    // 最后替换img，css和js里面的img替换也能在这一步完成
    // 以后再完善此步骤
    $content = str_replace('"images/','"'.$new_img_path.'/',$content);
    $content = str_replace('\'images/','\''.$new_img_path.'/',$content);
  }
  return $content;
}
// 替换模板注释中的内容
function replace_template_para($rep_arr,$conten,$red=false){
  if (is_array($rep_arr)) {
    foreach ($rep_arr as $key => $val){
      if ($red) {
        $conten = str_replace('<!--{$'.$key.'}-->',"<span style='color:#FF0000'>".$val."</span>",$conten);
      }else {
        $conten = str_replace('<!--{$'.$key.'}-->',$val,$conten);
      }
    }
  }
  return $conten;
}
// js src导入其内容,位置不能变
function jssrc2content($content){
  preg_match_all("|(<script\s+(\S+\s+)(\S+\s+)?src=[\"'](\S+)[\"']></script>)|U",$content,$match);

  foreach ($match[4] as $key => $val){
    if ("/"==substr($val,0,1)) {
      $val = "http://".$_SERVER["SERVER_NAME"].$val;
    }
    $tem = file_get_contents($val);
    $content = str_replace($match[1][$key],'<script '.$match[2][$key].$match[3][$key].'><!--//--><![CDATA[//><!--'."\n".$tem."\n".'//--><!]]></script>',$content);
  }
  $content = preg_replace('|//--><!\]\]></script>\s+<script type="text/javascript" language="javascript"><!--//--><!\[CDATA\[//><!--|',"",$content);
  return $content;
}

/**
 * $ding 很关键，表示退订还是预订某项；str就是表示某一项，可以用string，也可以用相应的整型数字；$old，表示原来的订阅情况，int型
 *
 * @param int $old
 * @param int|str 某项 $str
 * @param bool  是否订阅 $ding
 * @return int,新的预订数字
 */
function generYuDing($old,$str=0,$ding=true){
  if ("zxg"==$str) {
    $new = 4;
  }else if ("zxjj"==$str) {
    $new = 2;
  }else if ("xtgb"==$str) {
    $new = 1;
  }else {
    $new = (int)$str;
  }

  if (!$ding) {  // 退订某项，或多项
    return ~(~(int)$old|$new);
  } else {    // 预订某项
    return (int)$old|$new;
  }
}

function imagetype(){
    return array('.gif', '.jpg', '.jpeg', '.png', '.bmp', '.ico');  // 图片文件后缀
}

/**
 * 图片上传，同时执行插入数据库操作，(TODO 同步到前端以后完成)
 *
 * @param unknown_type 数据库连接, 用于执行插入操作和update操作
 * @param unknown_type $PATH_UPLOAD_IMG, 图片保存于服务器路径
 * @param unknown_type $_remote_pre, 图片rsync路径
 * @param unknown_type $upload_filename, 上传post数据中的 字段名
 * @return unknown
 */
function upload_imgs($upload_filenames, $PATH_UPLOAD_IMG, $IMG_URL_PRE){
  $result = array('ret'=>1, 'url'=>array(), 'msg'=>'');
  if (empty($upload_filenames)) return $result;

  $Max_IMG_SIZE = $GLOBALS['cfg']['MAX_UPLOAD_IMG_SIZE'];

  // 先检查图片的传输状态，有多少成功
  $up_succ = 0;
  $up_succ_file = array();
  foreach ($upload_filenames as $_t_up_f => $_nouse){
    if(UPLOAD_ERR_OK == $_nouse["error"]) {
      $up_succ++;
      $up_succ_file[] = $_t_up_f;
    }
  }

  if (0==$up_succ) {      // 没有上传成功的
    return $result;
  } else if ($up_succ >= 1) {   // 至少有一张成功上传的图片
    $allowedimagetype = imagetype();

    // 上传图片
    foreach ($up_succ_file as $_t_up_f) {
      // 只取扩展名
      $extt = substr(basename($upload_filenames[$_t_up_f]['name']), strpos(basename($upload_filenames[$_t_up_f]['name']), "."));

      // 通过文件内容生成文件名，md5加密的
      $file_md5 = md5(file_get_contents($upload_filenames[$_t_up_f]['tmp_name'])).$extt;

      // TODO 关于文件名，上传的图片使用md5文件名，但是其他的zip等文件则需要显示原文件, [name] => a测试  -a' 文档.docx,空格等依然保留，需进行urlencode处理
      if (in_array($extt, $allowedimagetype)) {
          $filename = $file_md5;
      } else {
          $filename = $upload_filenames[$_t_up_f]['name'];
      }
      //echo $filename . "\r\n";echo $extt . "\r\n";print_r($upload_filenames);exit;

      // 上传路径采用截取文件名前2位的方式
      $_remote_pre = substr($file_md5, 0, 2)."/".substr($file_md5, 2, 2); // 车模id,可能会有很多目录，先分成1000个
      $uploaddir = $PATH_UPLOAD_IMG."/".$_remote_pre;

      $uploadfile = $uploaddir . "/" . $filename; // 文件在本地的完整路径
      createdir(dirname($uploadfile));

      $file_size = @filesize($upload_filenames[$_t_up_f]['tmp_name']);
      // 先判断文件大小，超过500k, 则提示错误信息
      if (false === $file_size) {
        $result['msg'][$_t_up_f] = ' 图片大小未知！ ';
      }else if($file_size <= $Max_IMG_SIZE){
        if (move_uploaded_file($upload_filenames[$_t_up_f]['tmp_name'], $uploadfile)) {
          $img_url = $IMG_URL_PRE . "/$_remote_pre/" . str_replace(' ', '%20', $filename);
          //$img_url = $IMG_URL_PRE . "/$_remote_pre/" . str_replace('+', '%20', urlencode($filename)); //
          $result['ret'] = 0;
          $result['url'][$_t_up_f] = $img_url;
        } else {
          $result['msg'][$_t_up_f] = " move_uploaded_file error! ";
        }
      } else {
        // 图片太大
        $result['msg'][$_t_up_f] = " 请控制单张图片的大小在 " . $Max_IMG_SIZE/1024 ." k以内 ";
      }
    }
  } else {
    // 应该没有小于0的,不做处理
    $result['msg'] = ' 上传图片过程中 发生未知错误！ ';
    return $result;
  }

  return $result;
}

//建立目地文件夹
function createdir($dir='')
{
  if (!is_dir($dir)){
    // 该参数本身不是目录 或者目录不存在的时候
    $temp = explode('/',$dir);
    $cur_dir = '';
    for($i=0;$i<count($temp);$i++)
    {
      $cur_dir .= $temp[$i].'/';
      if (!is_dir($cur_dir))
      {
        @mkdir($cur_dir,0775);
      }
    }
  }
}

function format_for_json($a_arr,$parent_id="p_id"){
  $new_arr = array();

  foreach ($a_arr as $vals){
    $new_arr[$vals[$parent_id]][] = $vals;
  }

  return $new_arr;
}

function getJson($a_arr,$name="brand",$vname="name",$pref_f="eng_character", $a_son_id='id'){
  $js = "var json_$name = {";

  foreach ($a_arr as $par_id=>$vals){
    $js .= $par_id.':{';

    foreach ($vals as $sons){
      $sell_status = "";
      if (isset($sons["sell_status"]) && 0==$sons["sell_status"]) {
        $sell_status = "(停销)";
      }
      $l_son_i = is_numeric($sons[$a_son_id])? $sons[$a_son_id] : '"'.$sons[$a_son_id].'"';
      $js .= $l_son_i.':"'.$sons[$pref_f]." ".$sons[$vname].$sell_status.'",';
    }
    // 去掉多余的逗号
    if (","==substr($js,-1)) {
      $js = substr($js,0,-1);
    }

    $js .= '},';
  }
  // 去掉多余的逗号 ,
  if (","==substr($js,-1)) {
    $js = substr($js,0,-1);
  }

  $js .= "};";

  return $js;
}


function getFieldDesc($a_value,$a_field_info_arr){
  if (is_array($a_field_info_arr) && key_exists($a_value,$a_field_info_arr)) {
    return $a_field_info_arr[$a_value];
  } else {
    return "error! ";
  }
}

/**
 *
 * 模拟action动作, 主要用于后台多任务的时候需要进行模拟处理
 *
 * @param string $l_doPath 动作名称，例如: user_add， logout等
 * @param unknown_type $if_is_open_page
 *
 */
function MoNiDO($l_doPath, $l_ptd_info_arr,$request,&$response,$form,$get,$cookie, $if_is_open_page=1){
  if ($if_is_open_page) {
    $GLOBALS['cfg']['if_is_open_page'] = $if_is_open_page;  // 省略增加文档所需要的身份认证
  }

  // 外部数据模拟出来的各个父级元素的ID数值
  $l_erwai = array("do"=>$l_doPath);  // 必需项
  if (!empty($l_ptd_info_arr)) {
    if (isset($l_ptd_info_arr['p_info']['id'])) {
      $l_erwai["p_id"] = $l_ptd_info_arr['p_info']['id'];
    }
    if (isset($l_ptd_info_arr['t_info']['id'])) {
      $l_erwai["t_id"] = $l_ptd_info_arr['t_info']['id'];
    }
    if (isset($l_ptd_info_arr['d_info']['id'])) {
      $l_erwai["id"] = $l_ptd_info_arr['d_info']['id'];
    }
  }

  // 按照 php.ini variables_order = "EGPCS" 的替换顺序，重新拼装请求数组
  $l_form    = array_merge($form,$l_erwai);
  $l_get     = array_merge($get, $l_erwai);  // form和get都加一下并没多大关系，不会影响程序
  $l_cookie  = $cookie;
  $l_request = array_merge($l_get,$l_form,$l_cookie);

  // 重新进行添加文档的动作处理, begin  摘自main.php文件
  if (!array_key_exists($l_doPath, $GLOBALS['ACTION_CONFIGS'])) $GLOBALS['ACTION_CONFIGS'][$l_doPath] = $GLOBALS['ACTION_CONFIGS']["default_map"];  // 用默认的替代大多数一样的
  $l_actionConfig = new ActionConfig($GLOBALS['ACTION_CONFIGS']);
  if (!$l_actionConfig->setCurrentPath($l_doPath)) exit('Path <b>'.$l_doPath.'</b> isn\'t defined');
  $l_actionServer = new ActionServer();
  $l_actionServer->init($l_actionConfig,$l_request,$l_get,$l_form,$l_cookie); // 外层的$_FILES不能携带进入里层的动作处理，因此去掉此参数
  // 防止页面跳转所做的设置, 程序中的运行，不需要之前web方式的跳转，只需要返回相应错误信息或数据
  $l_actionServer->actionMap->addParam2Forward("if_jump","page_no_jump","page_jump");
  $l_actionServer->process();
  // $l_actionServer->response['ret'];// 存放结果数据 $response['html_content']
  // $l_actionServer->forwardConfig;  // return返回值
  // 重新执行一次应用过程 end

  // 依据返回的结果进行判断成功如否, 以及相关结果数组
  return $l_actionServer->response;
}

/**
 * 获取项目信息、表信息甚至是该条记录, 即p_id,t_id,id
 * 依据项目中文名、表中文名甚至是文档id，参数以数组形式给出
 *
 * $a_p_t_d_arr = array(
    'p'=>array('name_cn'=>"用户中心库"),
    't'=>array('name_cn'=>"用户表"),
    //'d'=>array('name_cn'=""),
   )
 *
 */
function getProInfoTblInfoDocInfo(&$dbR, $a_p_t_d_arr=array()){
  $l_arr = array();
  if (empty($a_p_t_d_arr)) {
    return $l_arr;
  }

  if (array_key_exists('p',$a_p_t_d_arr)) {
    $l_sql = cString_SQL::FmtFieldValArr2Str($a_p_t_d_arr['p']);

    $dbR->dbo = &DBO($GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']);
    $dbR->table_name = TABLENAME_PREF."project";
    $p_arr = $dbR->GetOne("where ".$l_sql);
    if (PEAR::isError($p_arr)) {
      return $l_arr;
    }
    $l_arr['p_info'] = $p_arr;

    // 连接指定的数据库，从表定义表获取到相关数据，并从中获取到数据
    $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
    $dbR->dbo = &DBO($p_arr['name_eng']."_r",$dsn);

    if (array_key_exists('t',$a_p_t_d_arr)) {
      $l_sql = cString_SQL::FmtFieldValArr2Str($a_p_t_d_arr['t']);

      $dbR->table_name = TABLENAME_PREF."table_def";
      $t_info = $dbR->getOne(" where ".$l_sql);
      if (PEAR::isError($t_info)) {
        return $l_arr;
      }
      $l_arr['t_info'] = $t_info;
      // 继续在此表中获取条目信息

      if (array_key_exists('d',$a_p_t_d_arr)) {
        $l_sql = cString_SQL::FmtFieldValArr2Str($a_p_t_d_arr['d']);

        $dbR->table_name = $t_info['name_eng'];
        $d_info = $dbR->getAlls(" where ".$l_sql);
        if (PEAR::isError($d_info)) {
          return $d_info;
        }
        $l_arr['d_info'] = $d_info;
      }
    }
  }

  return $l_arr;
}

//
function getPagebar(&$dbR,$pageSize,$flag,$pagesize_flag, $request,$sql_where){
  // 分页部分 开始
  if (isset($request["pagesize_form"]) && $request["pagesize_form"] >= 1) {
    $pageSize = $request["pagesize_form"] + 0; // 替换掉request中旧的
    $request[$pagesize_flag] = $pageSize;
    unset($request["pagesize_form"]);
  } else {
    if (isset($request[$pagesize_flag]) && $request[$pagesize_flag] >= 1)
      $pageSize = $request[$pagesize_flag] + 0;  // how many  per page
  }
  $itemSum = $dbR->getCountNum($sql_where);
  $_p = isset($request[$flag])?$request[$flag]:1; // page number $currentPageNumber
  $_p = (int)$_p;                   // int number
  $_p = ($_p>ceil($itemSum/$pageSize))?ceil($itemSum/$pageSize):$_p;
  $_p = ($_p<1)?1:$_p;
  $pager = new Pager("?".http_build_query(get_url_gpc($request)),$itemSum,$pageSize,$_p,$flag);
  $pagebar = $pager->getBar();
  $page_bar_size = $pagebar." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  每页显示 <a href='".$pager->buildurl(array($pagesize_flag=>5))."'>5条</a> <a href='".$pager->buildurl(array($pagesize_flag=>50))."'>50条</a> <a href='".$pager->buildurl(array($pagesize_flag=>100))."'>100条</a>";
  //."(共找到：".$pager->itemSum." 条)";
  // 分页部分 结束

  return array("page_bar_size"=>$page_bar_size, "_p"=>$_p, "pageSize"=>$pageSize);
}

function getSqlWhere($request, $default_where="where type='PUB'"){
  // 有查询的时候，查询sql语句保留
  $sql_where = isset($request["sql_where"]) ? urldecode($request["sql_where"]) : $default_where;
  // 如果有查询条件 begin
  if (key_exists("search_field_1",$request)) {
    $sql_where = getWhere($sql_where,$request);
    // 有查询条件的时候，同时将sql语句注入到 request 数组中，便于作为链接的一部分
    $request["sql_where"] = $sql_where;
  }
  $sql_where = convCharacter($sql_where, true);

  return $sql_where;
}

function getWhere($old_where,$form){
  $new_w = "";
  $num = 0;     // 目前最多只有两个查询条件
  foreach ($form as $l_zd=>$l_v){
    if (false!==strpos($l_zd,"search_field_")) $num++;
  }

  if ('where+status_+%3D+%27use%27' == $old_where || "where status_ = 'use'" == $old_where)
    $old_where = '';
  $l_opt_arr = option_method_arr();
  // 多个检索条件
  for($i=1;$i<=$num;$i++){
    if (""!=trim($form["search_field_".$i])) { // 字段
      $search_field = cString_SQL::FormatField($form["search_field_".$i]);
      // 他们要求自动加上%
      if ('like' == trim($form["search_method_".$i]))
        $form["search_value_".$i] = '%' . $form["search_value_".$i] . '%';
      $search_method = array_key_exists($form["search_method_".$i], $l_opt_arr) ? $form["search_method_".$i] : '='; // 只允许白名单
      $search_value = cString_SQL::FormatValue($form["search_value_".$i], 'string');
      $_template = $search_field." ".$search_method." ".$search_value; // 数据模板

      // 排重 begin
      if (false!==strpos($old_where,$_template)) continue;
      // 排重 end

      if($i>1) {
        $__key = $i-1;
        if (isset($form["plus_query_".$__key])) {// 并查：可能是and也可能是 or

          $new_w .= " ". ( ('and'==$form["search_concat_".$i])?'and':'or' ) ." ";
        }else{
          echo " plus_query_  search_concat_  erorrrr!!!"; // 没有这样的可能性
        }
      }

      $new_w .= $_template;
    }
  }

  if (""!=$new_w) {
    if (""!=$old_where) {
      $new_w = $old_where. " and " . $new_w;
    }else {
      $new_w = " where " . $new_w;
    }
  }else {
    $new_w = $old_where;
  }

  return $new_w;
}

function serialize_xml($data, $options = array())
{
  //require_once 'D:/www/svnfinance/trunk/corpinfo/Web/api/XML/Serializer.php';
  require_once 'XML/Serializer.php';
  $Serializer = new XML_Serializer($options);
  $status = $Serializer->serialize($data);

  if (PEAR::isError($status)) {
    die($status->getMessage());
  }

  return $Serializer->getSerializedData();
}

function get_url_gpc($request){

  /*if (get_magic_quotes_gpc()) {
    return digui_deep($request,'stripslashes');
  }*/

  return $request;
}
// 递归地用回调函数作用于数组的每个单元上, 包括多维数组也能处理. 由于array_map的回调不能是class::func因此没放在class里
function digui_deep($a_arr, $a_func='stripslashes'){
  return is_array($a_arr) ? array_map('digui_deep',$a_arr) : $a_func($a_arr);
}

/**
 * Calculates current microtime
 * @public
 * @returns string
 */
function utime() {
  // microtime() = current UNIX timestamp with microseconds
  $time  = explode( ' ', microtime());
  $usec  = (double)$time[0];
  $sec  = (double)$time[1];
  return $sec + $usec;
}

function getFieldArr($field_arr, $a_val=array("key"=>"Field", "value"=>"Field")){
  return cArray::Index2KeyArr($field_arr, $a_val);
}

// template_
function AccordingTpl2Str($data_arr, $a_field=array("p_id"), $a_tpl='<input type="hidden" name="<!--{$ziduan}-->" value="<!--{$value}-->" />'){
  return Html::AccordingTpl2Str($data_arr, $a_field, $a_tpl);
}

function GetTopDomain($host) {
    // 如果是合法IP，直接返回
    if (filter_var($host, FILTER_VALIDATE_IP))
        return $host;
    // 如果只有一个点或没有点，这整个就是域名直接返回
    $host = trim($host);
    if (!$host || false === strpos($host, '.'))
        return $host;
    $dot_count = substr_count($host, '.');
    if ($dot_count <= 1)
        return $host;
    // 2个点以上，则只需截取最后两项作为顶级域
    $first_str = substr($host, 0, strrpos($host, '.')); // www.ffan
    $last_str = substr($host, strrpos($host, '.')); // .com
    $last_butone = substr($first_str, strrpos($first_str, '.')); // .ffan
    return $last_butone . $last_str; // .ffan.com
}

function getSimpleDomain($url){
  if (""==trim($url)) {
    echo " url is empty! ". NEW_LINE_CHAR;
    return "";
  }

  // http://10.1.169.16:8080/android/appdetail.jsp?appid=10385501&actiondetail=0&pageno=1&clickpos=1&transactionid=1393575781713588&lmid=1022&softname=万汇
  // 有可能是IP
  $pattern = "/[\w-]+\.(com|net|org|gov|cc|biz|info|tv|mobi|name|co|hk|cn|tw|us)(\.(cn|hk))*/i";
  preg_match($pattern, $url, $matches);
  if(count($matches) > 0) {
    return $matches[0];
  } else if (preg_match("/(\d+\.\d+\.\d+\.\d+)(:\d+)?/", $url, $matches)) {
    // 纯ip加端口的方式
    return $matches[0];
  } else {
    $pattern = "|^https?://localhost/?|";
    preg_match($pattern, $url, $matches);
    if(count($matches) > 0) {
      return "localhost";
    }

    echo "getSimpleDomain $url ".NEW_LINE_CHAR;
    $rs = parse_url($url);
    $main_url = $rs["host"];
    if(!strcmp((sprintf("%u",ip2long($main_url))),$main_url)) {
      return $main_url;
    }else{
      $arr = explode(".",$main_url);
      $count=count($arr);
      $endArr = array("com","net","org","3322");//com.cn  net.cn 等情况
      if (in_array($arr[$count-2],$endArr)){
        $domain = $arr[$count-3].".".$arr[$count-2].".".$arr[$count-1];
      }else{
        $domain =  $arr[$count-2].".".$arr[$count-1];
      }
      return $domain;
    }
  }
}

/**
 * 用于生成父级元素相关的后缀url, <input 标签等
 *
 * @param array $parent_ids_arr 各父级id组成的数组
 * @param array $data_arr  包含了各个父级id的实际数据，例如$_POST, $_GET等
 * @param array $a_options 预留， 留作以后做一些扩展用的方法
 * @return array
 */
function GetParentIds($data_arr, $parent_ids_arr, $a_options=array()){
  if(empty($a_options)) $a_options = array();
  // 结果数组
  $l_rlt = array();
  /*$l_rlt["parent_nav"] = "";          // 父级导航部分
  $l_rlt["parent_elements_str"] = "";      // 字符串
  $l_rlt["parent_ids_input_hidden"] = "";    // input隐藏标签
  $l_rlt["parent_ids_url_build_query"] = "";  // url后缀*/


  // 没有就直接返回
  if (empty($parent_ids_arr) || !is_array($data_arr)) {
    return $l_rlt;
  }

  ksort($parent_ids_arr); // 按照p1,p2,p3...这样的顺序显示
  $l_arr = array();
  $l_nav = $l_p_str = $href = "";  // nav的链接部分
  $l_ii = 1;
  foreach ($parent_ids_arr as $l_p => $l_f){
    $l_p_str .= $l_p.":".$l_f;  // 转成字符串1:p_id
    if (array_key_exists($l_f,$data_arr)) $l_arr[] = $l_f;

    if (array_key_exists("nav", $a_options)) {
      if (array_key_exists($l_f, $a_options["nav"])) {
        // 每个下一级都要带上上一级的链接地址
        $href .= "&".$l_f."=".$a_options["nav"][$l_f]["value"];
        $a_options["nav"][$l_f]["href"] = $href;  // 生成出来每级
        $l_ii++;
      }
    }
  }

  // 当设置了导航的时候
  if (array_key_exists("nav", $a_options))
     $l_nav = Html::AtagTpl2Str($a_options["nav"]);

  //
  $l_rlt["parent_nav"] = $l_nav;
  $l_rlt["parent_elements_str"] = $l_p_str;
  $l_rlt["parent_ids_input_hidden"] = AccordingTpl2Str($data_arr, $l_arr);
  $l_rlt["parent_ids_url_build_query"] = AccordingTpl2Str($data_arr, $l_arr,'&<!--{$ziduan}-->=<!--{$value}-->');


  return $l_rlt;
}

/**
 * 拼装一个function 字符串
 *
 * @param array $l_arr   主要是一些算法的代码
 * @param string $a_para 如：'&$dbR,&$dbW'
 * @return string
 */
function pinzhuangFunctionStr($l_arr, $l_func, $a_para=''){
  if (!defined("NEW_LINE_CHAR")) {
    define("NEW_LINE_CHAR","\r\n");
  }
  $l_func_str = NEW_LINE_CHAR . "function ".$l_func.'('.$a_para.'){'.NEW_LINE_CHAR;

  if (array_key_exists("code",$l_arr)){
    if (""!=trim($l_arr["code"])) {
      $l_func_str .= rtrim($l_arr["code"]," ;") . ";";
    }
  }

  if (array_key_exists("html",$l_arr)){
    if (""==trim($l_arr["html"])) {
      $l_func_str .= "return NULL;".NEW_LINE_CHAR;
    }else {
      $l_func_str .= "return ". rtrim($l_arr["html"]," ;").";".NEW_LINE_CHAR;
    }
  }

  $l_func_str .= NEW_LINE_CHAR . "}";  // 防止原来末尾有行注释导致缺少一个大括号

  return $l_func_str;
}

function record_xxtea() {
  include_once('common/lib/xxtea.cls.php');
  $encrypt_info = '';

  // 记录发布机器
  $date_info = "Page Update: " . date("Y-m-d H:i:s"). "\r\n";
  $ip_info = "Server IP: " . getLocalIP() . "\r\n";
  $whole_info = $date_info . $ip_info. __FILE__ ." ".__FUNCTION__."\r\n";
  $xxtea = new XXTEA();
  $xxtea->setOprStrKey($whole_info);
  $encrypt_info = base64_encode($xxtea->XXTEA_Encrypt());

  return $encrypt_info;
}

function GetCurrentUrlPre() {
  $url = '';

  // cli模式下没有 HTTP_HOST 等值
  if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['SERVER_PORT']))
    return $url;

  // 拼装url
  $url .= 'http';
  if (443 == $_SERVER['SERVER_PORT']) $url .= 's'; // 是否 https
  $url .= '://' . $_SERVER['SERVER_NAME'];

  // 默认的80端口可以不用显示, 443不显示端口号
  if (80 != $_SERVER['SERVER_PORT'] && 443 != $_SERVER['SERVER_PORT'])
    $url .= ':' . $_SERVER['SERVER_PORT'];

  return $url;
}

function GetCurrentUrl() {
  $url = GetCurrentUrlPre() . request_uri();
  return $url;
}

function request_uri() {
  if (isset($_SERVER['REQUEST_URI']))
    return $_SERVER['REQUEST_URI'];

  if (isset($_SERVER['argv'])) $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
  else $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];

  return $uri;
}

// 一个方法
function GetCurrentUrl2() {
    $url = '';

    // cli模式下没有 HTTP_HOST 等值
    if (!isset($_SERVER['HTTP_HOST']) || !isset($_SERVER['SERVER_PORT']))
      return $url;

    // 拼装url
    $url .= 'http';
    if (443 == $_SERVER['SERVER_PORT']) $url .= 's'; // 是否 https
    $url .= '://' . $_SERVER['SERVER_NAME'];

    // 默认的80端口可以不用显示, 443不显示端口号
    if (80 != $_SERVER['SERVER_PORT'] && 443 != $_SERVER['SERVER_PORT'])
      $url .= ':' . $_SERVER['SERVER_PORT'];

    if (isset($_SERVER['REQUEST_URI']))
      return $url .= $_SERVER['REQUEST_URI'];

    if (isset($_SERVER['argv'])) $uri .= $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
    else $uri .= $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];

    return $url;
  }