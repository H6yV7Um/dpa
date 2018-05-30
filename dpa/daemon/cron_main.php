<?php
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  require_once("D:/www/dpa/configs/system.conf.php");
}else if ('CYG' === strtoupper(substr(PHP_OS, 0, 3))) {
  require_once("/cygdrive/d/www/dpa/configs/system.conf.php");
}else {
  require_once("/data0/deve/runtime/configs/system.conf.php");
}
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("mod/DBR.cls.php");

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) define("CRON_MASTER_URL","http://localhost/dpa/main.php",true);  // 依据部署的主库那台机器提供的url
else define("CRON_MASTER_URL","https://admin.".$GLOBALS['cfg']['WEB_DOMAIN']."/dpa/main.php",true);  // 依据部署的主库那台机器提供的url
define("DEBUG2",true,true);
if (!defined("NEW_LINE_CHAR")) define("NEW_LINE_CHAR","\r\n",true);
$Exec_Program_User = get_current_user(); //"finance";  // 默认的程序执行用户

if ( 'WIN' !== strtoupper(substr(PHP_OS, 0, 3)) ) {
  $USER_ARR = posix_getpwuid(posix_getuid());  // 进程的账号，而不是文件所属账号
  if (!empty($USER_ARR)) {
    $Exec_Program_User = $USER_ARR["name"];
  }
}

// 是否循环执行?
// 依据具体的情况，当作为daemon运行的时候需要循环
// 也可以放在crontab中执行,每个账户的cron放一个，则无需循环执行
// 由于crontab程序的特点是最快每一分钟执行一次，因此可以通过时间控制是否去数据库中获取最新任务列表,一分钟获取一次就足够了
if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
  while(1){
    main($Exec_Program_User);
    sleep(60);
  }
}else {
  main($Exec_Program_User);
}

//
function main($Exec_Program_User){
  if (DEBUG2) {
    $old_t = microtime();
    list($usec, $sec) = explode(" ", $old_t);
    echo "begin time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".$old_t.NEW_LINE_CHAR;
  }

  // 运行时候的时间，精确到分钟。由于循环一圈的时间小于60s(实际只有几秒)，保证每一分钟至少执行一次
  $curr_datetime = time();  // 本地php的时间设置 -- 其时区即为 sever_timezone
  $curr_parttime = date("Hi",$curr_datetime);  //
  $old_datetime  = -1;

  // 如果运行时间大于old时间(用不等于判断也行)，则执行，否则不用执行
  if ($old_datetime == $curr_parttime) {
    //return null;
  }else {
    $old_datetime = $curr_parttime;  // 更新 $old_datetime,$old_datetime可以不用在循环体外提前声明
    proc_one($curr_datetime,$Exec_Program_User);
  }

  if (DEBUG2) {
    $end_t = microtime();
    list($usec, $sec) = explode(" ", $end_t);
    echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".$end_t.NEW_LINE_CHAR.NEW_LINE_CHAR;
  }
}

function proc_one($curr_datetime,$Exec_Program_User){
  // 这里的 $exec 命令可以来着 本地数据库，也可能来自指定的机器，通过指定的网络接口请求到数据
  $host = getServerIp();
  if (!$host) $host = '127.0.0.1'; // 本地

  // 优先从本地数据库获取
  $schedule_arr = getScheduleFromDB($host,$Exec_Program_User,1);

  if (empty($schedule_arr)) {
    // $schedule_arr = getScheduleFromWEB($host,$Exec_Program_User);  // FromWEB()
  }

  // for($i=0;$i<50;$i++) $b = exec($exec);
  if (!empty($schedule_arr))
  foreach ($schedule_arr as $val){
    exec_shell_command($val,$curr_datetime);
  }
}

function exec_shell_command($_sch,$curr_datetime){
  // 对每项进行分析，设定的执行时间是否正好需要执行
  // $_sch["host"]; 目前不能进行跨服务器的计划任务设定，必须是本机器。
  // $_sch["id"]; // 用于记录到日志中，并进行唯一标
  $if_exec = if_exec($_sch,$curr_datetime);

  if ($if_exec) {
    // mode 分为3种 0, 1, 2
    switch ($_sch["mode"]){
      case "0":
        // 定期rsync指定的文件, doc_list 可以是sql，也可以是id列表
        // parse_ini_file(""); 解析sql或者列表。
        // 首先判断是 sql 还是 id列表
        break;
      case "1":
        // 执行所写 shell command
        if(1==$_sch["mode"] && !empty($_sch["shell_command"])){

          $exec = rtrim($_sch["shell_command"]);
          if ("&"!=substr($exec,-1)) {
            $exec .= " & ";  // 需要判断是否有 后缀 & ，否则需要自动添加
          }
          echo $exec.NEW_LINE_CHAR;
          exec($exec);
        }
        break;
      case "2":
        // 文档添加任务

        break;
    }
  }
}

function getScheduleFromDB($host,$user="finance",$status=1){
  $dbR = new DBR();
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    return null;
  }
  $dbR->table_name = TABLENAME_PREF."schedule";
  // 必须是本机器ip的、并有权限的账号下的、状态为启动状态的，三个条件下的计划任务

  $sql_where = "where host='$host' and belong_user='$user' and status_='$status' order by id desc";
  $_sch = $dbR->getAlls($sql_where);
  if (!$_sch) {
    // 返回的 $_sch 为 false 或 array();
    return array();
  }
  return $_sch;
}

function getScheduleFromWEB($host,$user="finance",$status=1,$in_charact="GBK"){
  require_once "HTTP/Request.php";

  $req = new HTTP_Request(CRON_MASTER_URL);
  $req->setMethod("POST");
  $req->addPostData("do","login");
  $req->addPostData("username","admin");
  $req->addPostData("password","admin");
  $req->sendRequest();
  $cookie_arr = $req->getResponseCookies();

  $l_url = CRON_MASTER_URL."?do=getcron&host=$host&belong_user=$user&status_=$status";
  $req = new HTTP_Request($l_url);
  foreach ($cookie_arr as $cookie){
    $cookie_name  = $cookie["name"];
    $cookie_value = $cookie["value"];
    $req->addCookie($cookie_name,$cookie_value);
  }
  $req->_timeout = 5;
  $req->setMethod("GET");
  $req->addHeader("User-Agent","Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)");
  $rc = $req->sendRequest();

  if(PEAR::isError($rc))
  {
    $error = "HTTP_Request Error : ".$rc->getMessage();
    STDERR($error);
    exit;
  }
  $html_content = $req->getResponseBody();

  // XML_Unserializer
  require_once("XML/Unserializer.php");
  $un_xml = new XML_Unserializer();
  $un_xml->unserialize($html_content);
  $_sch = $un_xml->getUnserializedData();

  if (!$_sch) {
    // 返回的 $_sch 为 false 或 array();
    return array();
  }
  return $_sch;
}

/**
 * 某任务的时间设定是不是可以执行，即跟当前时间恰好吻合
 *
 * @param array $_sch
 * @param timestemp $curr_datetime
 * @return bool
 */
function if_exec($_sch,$curr_datetime){
  //$year  = date("Y",$curr_datetime);
  $month = date("n",$curr_datetime);  // 没有前导0
  $day   = date("j",$curr_datetime);  // 没有前导0
  $hour  = date("G",$curr_datetime);
  $minute= (int)date("i",$curr_datetime);
  $week= date("w",$curr_datetime);// w 0~6, 0:sunday  6:saturday

  $forbidd = trim($_sch["forbidden_date"]);
  if (!empty($forbidd)) {
    // 当前日期处于设置的禁止日期内（某个时区），则直接返回false，不允许执行
    if (if_forbidden($_sch,$curr_datetime)) {
      return false;
    }
  }
  // 时间从大到小进行排除 month
  if ("*"==$_sch["month"] || in_array($month,explode(",",$_sch["month"]))) {
    // week
    if ("*"==$_sch["week"] || in_array($week,explode(",",$_sch["week"]))) {
      // day
      if ("*"==$_sch["day"] || in_array($day,explode(",",$_sch["day"]))) {
        // hour
        if (proccessPer($_sch["hour"],$hour) || in_array($hour,explode(",",$_sch["hour"]))) {
          // minute
          if (proccessPer($_sch["minute"],$minute) || in_array($minute,explode(",",$_sch["minute"]))) {
            //echo date("Y-n-j G:i:s, w ",$curr_datetime).NEW_LINE_CHAR;
            return true;
          }
        }
      }
    }
  }
  return false;
}

function if_forbidden($a_arr,$curr_datetime){
  $forbidden_date=$a_arr["forbidden_date"];
  $forbidd_arr = explode(",",$forbidden_date);

  foreach ($forbidd_arr as $___forbidd){
    if (""!=$___forbidd) {
      // 先分离单个的时区，标记是@符号
      $l___forbidd = explode("@",$___forbidd);
      if (count($l___forbidd)>1) {
        $l_forbidd_tz = $l___forbidd[1]*1;
      }else {
        $l_forbidd_tz = $a_arr["forbidden_timezone"]*1;
      }
      // 将本地时间转化为目标时区的时间
      $tar = $curr_datetime - $a_arr["server_timezone"]*3600+$l_forbidd_tz*3600;
      $tar_YMD_arr = array(date("Y",$tar),date("n",$tar),date("j",$tar));

      $forbidd_ymd = explode("-",$l___forbidd[0]);
      $__arr = array();
      foreach( $forbidd_ymd as $v){
          if( ""!=trim($v) ){
            $__arr[]=$v*1;
          }
      }

      if (3==count($__arr)) {
        // 设置了年月日
        if($__arr===$tar_YMD_arr) return true;
      }elseif (2==count($__arr)){
        // 设置月日
        if($__arr[0]==$tar_YMD_arr[1] && $__arr[1]==$tar_YMD_arr[2]) return true;
      }elseif (1==count($__arr)){
        // 只设置了日
        if($__arr[0]==$tar_YMD_arr[2]) return true;
      }else{
        // 无法识别的、格式不对的，直接跳过，不做处理
      }
    }
  }
  return false;
}

// if str is * or */n
function proccessPer($str,$value){
  if ("*"==$str) {
    return true;
  }

  // */n
  if (false!==strpos($str,"*/")) {
    $arr = explode("/",$str);
    $per = intval($arr[1]);
    if ($per>=1) {
      if(0==$value%$per) return true; // 整除
    }
  }

  return false;
}
