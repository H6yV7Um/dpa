<?php
/**
 * 执行抓取库中某个id的抓取任务，执行如下：
 * -i: id号，可以是范围 1-20,3,5,6 这样的
 * -t: 表名. 默认就是 grab_list
 * -d: 暂未用，保留
 * -z: 是否主程序，表示能控制自身繁衍其他id进程的
 *
php D:/www/dpa/daemon/grab/spider.cls.php -i 1
php D:/www/dpa/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 1-4 > bj.txt
php D:/www/dpa/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 5-8 > tj.txt
php D:/www/dpa/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 197-200 > sh.txt
php D:/www/dpa/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 489-492 > gz.txt
php D:/www/dpa/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 497-500 > sz.txt
 *
 *
php /data0/deve/projects/daemon/grab/spider.cls.php -i 1
php /data0/deve/projects/daemon/grab/spider.cls.php -t dpps_grab_request_201204 -i 601-724

 */
// 抓取相关的状态配置 <!-- grab begin
define("GRAB_REQUEST_STATUS_IN","in");
define("GRAB_REQUEST_STATUS_IN_STR","用户提交入库");
define("GRAB_REQUEST_STATUS_DOING","doing");
define("GRAB_REQUEST_STATUS_DOING_STR","正在抓取中");
define("GRAB_REQUEST_STATUS_COMPLETE","complete");
define("GRAB_REQUEST_STATUS_COMPLETE_STR","该抓取任务处理完成");
define("GRAB_REQUEST_STATUS_EMPTY","empty");
define("GRAB_REQUEST_STATUS_EMPTY_STR","未抓取到内容（内容空或超时）");
define("GRAB_REQUEST_STATUS_SCRAP","scrap");
define("GRAB_REQUEST_STATUS_SCRAP_STR","重复的任务，废弃此条");
define("GRAB_REQUEST_STATUS_DEL","del");
define("GRAB_REQUEST_STATUS_DEL_STR","域名错误，或被遗弃的");
define("GRAB_REQUEST_STATUS_ARTI_INTER_FAIL","arti_inter_fail");
define("GRAB_REQUEST_STATUS_ARTI_INTER_FAIL_STR","调用博文接口失败");
define("GRAB_REQUEST_STATUS_PHOTO_INTER_FAIL","photo_inter_fail");
define("GRAB_REQUEST_STATUS_PHOTO_INTER_FAIL_STR","调用相册接口失败");

// 接口
define("INTERFACE_INSERT_ARTICLE", "https://admin.ni9ni.com/dpa/main.php");

// 常数配置 PRO_REQUEST_NUM
define("GRAB_PRO_REQUEST_NUM",1);
define("GRAB_PRO_REQUEST_NUM_STR","每次处理多少个博客搬家请求");

// 允许的最大进程数, 当前默认只能有一个进程运行
define("GRAB_PROC_MAX_PROC_NUM", 3);

if (!defined('DEBUG2')) define("DEBUG2",true,true);

ini_set('memory_limit', '200M');

if ( 'WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) {
  // image save path
  define("IMAGE_SAVE_PATH_HOST",     "D:/www/ni9ni/htdocs/img3/ss");
  define("WATERMARK_FILE",         "D:/www/ifeng/auto/admin/watermark.png");
  define("IMAGE_SAVE_PATH_URL",     "http://my.ni9ni.com/ni9ni/htdocs/img3/ss");
  define("IMAGE_SAVE_PATH_RELATE",   "hmove");
}else {
  define("IMAGE_SAVE_PATH_HOST",     "/data0/htdocs/img3/ss");
  define("WATERMARK_FILE",         "/data0/htdocs/img3/watermark.png");
  define("IMAGE_SAVE_PATH_URL",     "http://img3.ni9ni.com/ss");
  define("IMAGE_SAVE_PATH_RELATE",   "hmove");
}
// grab end -->

if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) )require_once("D:/www/dpa/configs/system.conf.php");
else require_once("/data0/deve/runtime/configs/system.conf.php");
require_once("lang/".$GLOBALS['cfg']['LANG_DEFINE_FILE']);
require_once("common/functions.php");
require_once("common/grab_func.php");
require_once("common/lib/cArray.cls.php");
require_once("common/lib/Parse_Arithmetic.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");
require_once("HTTP/Request.php");
require_once("simple_html_dom.php");

if ('cli'==php_sapi_name()) {
  $_o = cArray::get_opt($argv, 'i:d:t:z:o:');
}else {
  $_o = $_GET;
}

// 控制进程少于设定的数值, 由于自身运行的时候也算在内，所以需要等号
if (getExecProcNum()<=GRAB_PROC_MAX_PROC_NUM) {
  if (DEBUG2) {
    $old_t = microtime();
    list($ousec, $osec) = explode(" ", $old_t);
    echo "begin time: ".date("Y-m-d H:i:s",$osec) ." | microtime: ".($osec+$ousec).NEW_LINE_CHAR;
  }
  main();
  if (DEBUG2) {
    $end_t = microtime();
    list($usec, $sec) = explode(" ", $end_t);
    echo "end__ time: ".date("Y-m-d H:i:s",$sec) ." | microtime: ".($sec+$usec) .' spend: '.($sec+$usec-$osec-$ousec).'s'.NEW_LINE_CHAR.NEW_LINE_CHAR;
  }
  if (isset($_o["o"])) {
    require_once("common/lib/Shutdown_computer.cls.php");
    if ("off"==$_o["o"]) {
      Shutdown_computer::shutdown();
    }else if ("restart"==$_o["o"]) {
      Shutdown_computer::restart_computer();
    }
  }
}else {
  echo "process num is full, can not start this!". NEW_LINE_CHAR;
}


// 每个进程需要记录自己当前的任务，存储为全局变量，放到memcached或一个共用文件里面.
//    记录格式为哪个请求，列表处理到哪儿了? 并且依据处理进度实时更新。
// 首先从数据库中获取当前要抓取的URL
// 筛选的条件为优先选择没有处理完的那些处理中中断了的.
function main(){
  // 获取相应参数, 以后完善不同的参数。当前分别i:需要处理的请求id s:start t:stop
  $_o = & $GLOBALS["_o"];
  // 参数验证 begin
  if (!array_key_exists("i",$_o)) {
    echo "you should give id ".NEW_LINE_CHAR;
    return ;
  }
  // 没有指定哪张表则采用默认表
  $a_grab_tbl = (isset($GLOBALS["_o"]["t"]) && !empty($GLOBALS["_o"]["t"])) ? $GLOBALS["_o"]["t"] : TABLENAME_PREF."grab_request";

  // 分解出所有的id出来，支持 1-4,1,6,8 这样的多id类型
  $l_ids = cArray::getIdsByStr($_o["i"]);
  $l_ids = array_unique($l_ids);  // 去掉重复的

  // 先判断是否主程序，即能生成子进程（并非fork出来的），如果是主程序，为了提高效率，可以同时进行多个不同id的抓取工作。
  // 具体如何分配，为了防止内存使用过度，因此一个程序最多处理10条id
  if (isset($GLOBALS["_o"]["z"])) {
    $l_new_fenpei = array_chunk($l_ids, 10);
    // 主程序一直循环探测是否有程序执行完成
    print_r($l_ids);
    print_r($l_new_fenpei);// 以后完善之????
    exit;
  }

  $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
  $l_name0_w = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'];
  // 数据库连接，只需要一个dbr和dbw对象
  // 获取抓取数据库的连接信息
  $dbR = new DBR($l_name0_r);
  $l_err = $dbR->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " error request id: ". var_export($l_id,true).", table: $a_grab_tbl , error_msg: " . var_export($l_err,true). " FILE:".__FILE__. NEW_LINE_CHAR;
    return null;
  }
  $dbR->l_name0_r = $l_name0_r;

  $dbW = new DBW($l_name0_w);
  $l_err = $dbW->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") . " error request id: ". var_export($l_id,true).", table: $a_grab_tbl , error_msg: " . var_export($l_err,true). " FILE:".__FILE__. NEW_LINE_CHAR;
    return null;
  }
  $dbW->l_name0_w = $l_name0_w;  // 记录下别名
  // 防止读库过期, 写库暂时不需要
  //$l_sql = "set interactive_timeout=24*3600"; $dbR->Query($l_sql); 似乎不起作用

  $dbR->dbo = &DBO($l_name0_r);
  $dbR->table_name = TABLENAME_PREF."project";
  $p_arr = $dbR->GetOne("where name_cn='数据抓取'");
  if (PEAR::isError($p_arr)) {
    echo " error message： " .$p_arr->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
    return null;
  }

  begin_process($dbR, $dbW, $p_arr, $l_ids,$a_grab_tbl);
}

function begin_process(&$dbR, &$dbW, $p_arr, $l_ids,$a_grab_tbl){
  if (!empty($l_ids)) {
    foreach ($l_ids as $l_id_arr_or_int){
      if (is_array($l_id_arr_or_int)) {
        $l_id = $l_id_arr_or_int['id'];
      }else {
        $l_id = $l_id_arr_or_int;
      }
      echo date("Y-m-d H:i:s") . " to_process_grab_id:".$l_id." and table is:".$a_grab_tbl . NEW_LINE_CHAR;
      $mem_use = memory_get_peak_usage() * 100 /(200*1024*1024);
      if ($mem_use > 90) {
        echo date("Y-m-d H:i:s"). " memory_use too large, will_exit! id: ".$l_id." has not process! memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;
        break;
      }else {
        one_id($dbR, $dbW, $p_arr, $l_id_arr_or_int, $a_grab_tbl);
      }
    }
  }
}

// 处理一个id，或一个id请求的数组 array('id'=>1,...) 。就是省去重新请求一次数组
function one_id(&$dbR, &$dbW, $p_arr, $l_id, $a_grab_tbl){
  if (!is_array($l_id)) {
    $l_id = $l_id+0;  // 强制为整型数据
    if ( $l_id <= 0 ) {
      echo "id must big than zero!".NEW_LINE_CHAR;
      return ;
    }
    // 参数验证 end

    // 依据数据库连接信息，重新连接新库
    $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
    $dbR->dbo = &DBO('grab', $dsn);
    $dbR->SetCurrentSchema($p_arr['db_name']);
    $dbR->table_name = $a_grab_tbl;

    // 同样优先处理doing状态的, 一次只处理一条请求，因为可能包含了很多的子请求
    $l_rlt = $dbR->GetOne("where id = ".$l_id);
    if (PEAR::isError($l_rlt)) {
      echo " error message： " .$l_rlt->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
      return null;
    }
  }else {
    $l_rlt = $l_id;
  }
  // 获取到数据以后进行处理
  if (!empty($l_rlt)) {
    spider::proceed($dbR, $dbW, $p_arr, $l_rlt,$a_grab_tbl);
  }
}

class spider
{
  // 抓取文章列表
  function proc_article(&$dbR, &$dbW, $p_arr, $a_vals,$uni_a, $a_info,$a_tablename, $a_domain, $timeout=60){
    // 获取字符编码信息, 字符编码信息应当自动获取更合理
    //$charactorset = get_charactor($a_domain);
    // 拼装域名目录名
    $l_domain_file = str_replace(".", "_", $a_domain);

    // 包含需要用到的方法, 依据不同域名获取相应方法
    require_once($l_domain_file."/list.php");

    // 抓取文章列表，不同域名采用不同的方法
    $l_func = new $l_domain_file();
    $db_old_list = array();
    $l_a_l = $l_func->get_article_list($dbR, $dbW, $uni_a, $a_info, $a_domain, $db_old_list, $timeout);

    // 将文章列表信息插入到文章列表数据表中。
    if (!empty($l_a_l[0])) {
      // 先将文章总数插入表 request 中去
      $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
      $dbW->dbo = &DBO('grab', $dsn);
      $dbW->SetCurrentSchema($p_arr['db_name']);
      $dbW->table_name = $a_tablename;
      set_status_by_id($dbR, $dbW, $a_tablename, $a_info["id"],
      array("arti_total" => $l_a_l[1]["totalCount"],"arti_hidden" => $l_a_l[1]["arti_hidden"]));

      foreach ($l_a_l[0] as $l_v){
        $data_arr = array(
          "parent_id"=>$a_info["id"],
          "tbl_name_eng"=>$a_tablename,
          "url"=>trim($l_v["url"]),
          "title"=>(!is_utf8_encode($l_v["title"]))? iconv("GBK","UTF-8//IGNORE",$l_v["title"]) :$l_v["title"],
          "status_"=>GRAB_REQUEST_STATUS_IN,
          "creator"=>(isset($_SESSION["user"]["username"])) ? $_SESSION["user"]["username"] : 'robot',
          "createdate"=>date("Y-m-d"),
          "createtime"=>date("H:i:s")
        );
        if (array_key_exists("short_text",$l_v)) $data_arr["short_text"] = trim($l_v["short_text"]);
        if (array_key_exists("creator",$l_v)) $data_arr["creator"] = $l_v["creator"];
        if (array_key_exists("createdate",$l_v)) $data_arr["createdate"] = $l_v["createdate"];
        if (array_key_exists("createtime",$l_v)) $data_arr["createtime"] = $l_v["createtime"];

        //下面两行可以省略，因为本身就是在操作这些库的写动作
        //$dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
        $dbW->dbo = &DBO('grab');
        $dbW->SetCurrentSchema($p_arr['db_name']);
        $dbW->table_name = TABLENAME_PREF."grab_article_list";
        $l_exist_c = cString_SQL::getUniExist($data_arr, $uni_a);
        $l_exi_one = $dbW->getExistorNot($l_exist_c);
        if (PEAR::isError($l_exi_one)) {
          echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
          return ;
        }
        if ( empty($l_exi_one) ) {
          $dbW->insertOne($data_arr);
          $l_err = $dbW->errorInfo();
               if ($l_err[1]>0){
                 // 增加失败后
            echo date("Y-m-d H:i:s"). " ".$dbW->getSQL() ." insert to article_list error!" . var_export($l_err, true) . NEW_LINE_CHAR;
            //exit;
            // print_r($data_arr);
          }else {
            // echo $dbW->LastID();
          }
        }
        //if (is_resource($dbR->dbo->connection)) mysql_ping($dbR->dbo->connection);
        sleep(1);
      }
    }

    // 然后再从文章列表数据表中逐一抓取文章内容并post到接口上。
    $dbR->dbo = &DBO('grab');
    $dbR->SetCurrentSchema($p_arr['db_name']);
    $dbR->table_name = TABLENAME_PREF."grab_article_list";
    $l_req = $dbR->getAlls(" where parent_id=".$a_info["id"]." and tbl_name_eng='$a_tablename' and status_ in ('".GRAB_REQUEST_STATUS_IN."','".GRAB_REQUEST_STATUS_EMPTY."' ) order by id " );
    $l_err = $dbR->errorInfo();
    if ($l_err[1]<=0){
      spider::post_to_localdb($dbR, $dbW, $p_arr, $a_vals, $uni_a, $l_req, $a_info, $a_domain,$dbR->table_name, $timeout);
    }else {
      //
      echo date("Y-m-d H:i:s"). " sql:".$dbR->getSQL() .". error_msg: " . var_export($l_err,true) . NEW_LINE_CHAR;
    }
  }

  //
  function post_to_localdb(&$dbR, &$dbW, $p_arr, $a_vals, $uni_a, $arti_data, $a_info, $a_domain, $tbl_article_list, $timeout=60){
    if (!empty($arti_data)) {
      $l_domain_cls = str_replace(".", "_", $a_domain);
      $l_func = new $l_domain_cls();
      $l_common_static = array();  // yuliu
      foreach ($arti_data as $l_val){
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
        $dbW->dbo = &DBO('grab', $dsn);
        $dbW->SetCurrentSchema($p_arr['db_name']);
        $dbW->table_name = $tbl_article_list;
        set_status_by_id($dbR, $dbW, $tbl_article_list, $l_val["id"], array("status_" => GRAB_REQUEST_STATUS_DOING) );
        // 一篇篇文章抓取，然后post到接口上去,返回成功的，则将状态设置为完成，否则设置为出错等
        if (is_array($l_val) && array_key_exists("url",$l_val)) {
        $l_a_l = $l_func->get_arti_detail($dbR, $dbW, $l_val, $timeout, $l_common_static);  // 依据数据库的实际字段的中文字段名组织数据
        }else {
          $l_a_l = array();
          echo date("Y-m-d H:i:s"). " arti_data:". var_export($arti_data, true)." l_val is not array, l_val:".var_export($l_val,true) . NEW_LINE_CHAR;
        }

        // 切换一下数据库连接信息
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
        $dbW->dbo = &DBO('grab', $dsn);
        $dbW->SetCurrentSchema($p_arr['db_name']);
        $dbW->table_name = $tbl_article_list;
        // $l_a_l 返回两部分，一部分是
        if (!empty($l_a_l)) {
          // 开始调用发文接口 , 要拼装好post数组
          // tags 数组转字符串
          if ( is_array($l_a_l['tags']) ) $l_a_l['tags'] = implode(',',$l_a_l['tags']);
          // 同时更新数据表中的相应文章创建时间字段, 不应该是全字段，包括正文不应该都update进去, 顶多更新创建时间和日期
          $l_up_tmp_arr = array();
          if (array_key_exists("createdate",$l_a_l)) {
            $l_up_tmp_arr['createdate'] = $l_a_l['createdate'];
          }
          if (array_key_exists("createtime",$l_a_l)) {
            $l_up_tmp_arr['createtime'] = $l_a_l['createtime'];
          }
          if (!empty($l_up_tmp_arr)) set_status_by_id($dbR, $dbW, $tbl_article_list, $l_val["id"], $l_up_tmp_arr );

          // 很多情况下标题和摘要提前已经抓取过
          $l_data_arr = array(
            "title"    =>$l_val["title"],
          );
          if (array_key_exists("short_text",$l_val)) {
            $l_data_arr["short_text"] = $l_val["short_text"];
          }
          $l_data_arr = array_merge($l_data_arr,$l_a_l);  // 增加或覆盖一些新字段

          // 文档id通常也不会有此字段，但为了保险起见还是进行此步骤。
          if (array_key_exists('id', $l_data_arr)) unset($l_data_arr['id']);

          // 相应的英文字段对应到CMS项目中的中文名称，在CMS项目中这些中文字段意义基本固定
          $l_r_key2cn = array(
            'author'=>'作者',
            'title'=>'文档标题',
            'content'=>'正文',
            'short_text'=>'摘要',
            'atype'=>'文章类型',    // 大多CMS正文页中没有此字段
            'tags'=>'tag标签',    // 大多CMS正文页中没有此字段
            'suoshulanmu'=>"所属栏目",  // 默认一个文章类型
            'suoshuzilanmu'=>"所属子栏目",  //
            'grab_url'=>"其他来源",  //
            's_shu_chengshi'=>"所属城市",  //
          );
          spider::replac2cnkey($l_data_arr, $l_r_key2cn);  // 替换为中文键名

          // 网络和cli两种方式并存，网络需要流量需要web服务器参与而cli则简单快速得多
          if (isset($l_data_arr["creator"]) && '0'==$l_data_arr["creator"]) $l_data_arr["creator"] = "robot"; // 抓取机器人

          // 生活频道, 交友的抓取采用本地进行
          if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) && 19==$a_vals["p_id_to"] && 2==$a_vals["t_id_to"]) {
            // windows下主要是向服务器进行同步数据的, 由于服务的ip被封，所以需要用本地的机器
            // 身份认证部分的, 因为请求的url是https协议的，443端口，设置了http auth认证的
            $cookie_arr = getCookieArr();
            $a_BasicAuth = getBaseAuth();

            // 项目id和表id应该自动获取, 此三项必须保留
            $l_data_arr["do"] = "document_add";
            $l_data_arr["p_id"] = 8;// $a_vals["p_id_to"]; 本地和服务器上不一致, 需要服务器提供一个接口，通过项目中文名进行对应
            $l_data_arr["t_id"] = 2;// $a_vals["t_id_to"];
            // 让返回的结果是json串, 需要设定返回类型
            $l_data_arr["cont_type"] = "js_novar";  // 无js变量名的 json 数据

            $l_h_u = array();
            $l_ex_out = request_cont($l_h_u, INTERFACE_INSERT_ARTICLE, $l_data_arr ,60,$cookie_arr,"",$a_BasicAuth);
            $l_command   = INTERFACE_INSERT_ARTICLE . " " . var_export($l_data_arr, true);
            if (false!==strpos($l_ex_out,'"ret":0') || false!==strpos($l_ex_out,'成功发布')) {
              $return_var = 0;  // 表示成功
            }else {
              $return_var = 1;
            }

            // 同时还需要向抓取数据表中post数据，保持了线上的一致性，以后完善之，????
          }else {

          // 进行编码成url串, 包括了urlencode动作等
          $l_query_str = http_build_query($l_data_arr);

          $l_command = 'php '. $GLOBALS['cfg']['PATH_ROOT'] . '/main.php -g "do=document_add&p_id='.$a_vals["p_id_to"].'&t_id='.$a_vals["t_id_to"].'" -d "'.$l_query_str.'" >> '.$GLOBALS['cfg']['LOG_PATH'].'/cli_document_add_'.date("Ym").'.txt';
          //echo $l_command . NEW_LINE_CHAR ;
          exec( $l_command,$l_ex_out,$return_var );  // 执行该命令 http_build_query以后不能再进行escapeshellcmd
          //print_r($l_ex_out);print_r($return_var);
          }
          //$l_log = date("Y-m-d H:i:s"). " FILE:".__FILE__ ."\r\n"."CMD:". $l_command . "\r\n"."memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . "\r\n"."l_ex_out:".var_export($l_ex_out,true).", "."return_var:".var_export($return_var,true)."\r\n";
          //file_put_contents($GLOBALS['cfg']['LOG_PATH']."/".date("Y-m")."spider_document_add_cmd_.txt", $l_log . NEW_LINE_CHAR. NEW_LINE_CHAR, FILE_APPEND);
          usleep(300);  // 稍微停顿

          // exec执行成功会返回$return_var=0，失败则为1
          if (!$return_var) {
            // 执行成功, 处理完成以后，最后还要将状态设置为完成
            set_status_by_id($dbR, $dbW, $tbl_article_list, $l_val["id"], array("status_" => GRAB_REQUEST_STATUS_COMPLETE));
          }else {
            // 输出出错信息或者记录到日志中
            set_status_by_id($dbR, $dbW, $tbl_article_list, $l_val["id"], array("status_" => GRAB_REQUEST_STATUS_SCRAP));
            echo $l_command . NEW_LINE_CHAR ;
          }
        }else {
          set_status_by_id($dbR, $dbW, $tbl_article_list, $l_val["id"], array("status_" => GRAB_REQUEST_STATUS_EMPTY) );
        }
        usleep(200);
      }
    }
  }

  // 处理一条抓取请求
  function proc_one_request(&$dbR,&$dbW,$p_arr,$a_vals,$a_req,$a_tablename,$timeout = 120){
    // 将某条文章列表页面解析，分解出所有的文章列表，并入库。暂时不处理翻页列表，以后用到再完善之
    $l_url = trim($a_req["url"]);  // 必须是绝对地址
    if ( false!==strpos($l_url,"://")) {
      $l_tmp = parse_url($a_req["url"]);
      $l_domain = getSimpleDomain($l_tmp["host"]);

      // 文章抓取
      if ($a_req['if_article']) {
        // 包括抓取文章以及post到接口上(或者exec进行发布)
        spider::proc_article($dbR, $dbW, $p_arr, $a_vals, array("parent_id","tbl_name_eng","url"), $a_req, $a_tablename, $l_domain, $timeout);
      }

      // 相册处理如果有的话. 暂未实现, 以后完善
      if ($a_req['if_album']) {}
    }
  }

  //
  function content_replace_special($a_str){
    // 主要是替换正文中的<o:p> <?XML 特殊标签
    $l_str = "";
    if (false!==strpos($a_str,'<?xml')) {
      $l_str = preg_replace('/<\?xml[^>]+>/i',"",$a_str);
    }else {
      $l_str = $a_str;
    }
    $l_str = str_replace(array("<o:p>","</o:p>"),"",$l_str);

    return $l_str;
  }

  function allowimgtype(){
    return array(".gif",".jpg",".jpeg",".png",".bmp");  // 允许的图片后缀
  }
  //
  function getExtt($a_str){
    $l_a = parse_url($a_str);
    if (!key_exists("path",$l_a)) {
      $l_a["path"] = $a_str;
    }
    $l_extt = substr(basename($l_a["path"]), strrpos(basename($l_a["path"]),"."));// 最后一个.
    return $l_extt;
  }

  // <img 标签的 real_src 优先使用!
  function get_img_real_src(&$obj){
    $l_rlt = array();
    $l_str = $obj->outertext;

    if (false!==strpos($l_str, "real_src")) {
      $l_rlt["src"] = $obj->real_src;
      $l_rlt["if_real"] = 1;
    }else if (false!==strpos($l_str, "src")) {
      $l_rlt["src"] = $obj->src;
      $l_rlt["if_real"] = 0;
    }else {
      $l_rlt["src"] = "";
      //$l_rlt["if_real"] = 0;
    }

    return $l_rlt;
  }

  function genImgFileName($a_str, $callback="mc_file"){
    $allow_arr = spider::allowimgtype();
    // 获取文件后缀
    $l_extt = strtolower(spider::getExtt($a_str));
    if (!in_array($l_extt,$allow_arr)) {
      $l_extt = ".jpg";  // 默认为jpg
    }

    // 文件名
    $l_a = parse_url($a_str);
    $l_file_name = basename($l_a["path"]);
    if(!empty($callback)){
      $l_file_name = $callback($l_file_name);
      $l_file_name = substr($l_file_name,0,16);
    }

    return $l_file_name.$l_extt;
  }

  // 正文中如果有图片，则需要抓取图片，同时更改图片地址
  function downimg_and_chgimg($a_url,$a_str,$uid,$parent_link=false, $img_tar_path="",$img_url_path="",$timeout=60,$zd_domain=""){
    // 文件存放路径是UID的后两位
    $l_u_path = $uid%100;

    // 需要判断正文中的图片，
    $l_img = array();
    if (false!==strpos($a_str,"<img")) {
      $l_html = str_get_html($a_str);
      foreach ($l_html->find("img") as $l_ele){
        $l_if_real_src = spider::get_img_real_src($l_ele);
        $l_img_url = get_abs_url( $a_url, html_entity_decode($l_if_real_src["src"]) );
        $l_img[] = $l_img_url;

        // 如果父链接的图片地址跟中图地址不一致，则还需要抓取大图
        $l_if_parent_img = 0;
        if( "a" == $l_ele->parent()->tag){
          $l_a_href = html_entity_decode($l_ele->parent()->href);
          // 如果外层链接是图片，则需要下载该图片
          $allow_arr = spider::allowimgtype();
          // 获取文件后缀
          $l_extt = strtolower(spider::getExtt($l_a_href));
          if (in_array($l_extt,$allow_arr)) {
            $l_parent_img = get_abs_url( $a_url, $l_a_href);
            if (!empty($l_parent_img) ) {
              $l_if_parent_img = 1;
              // 先检测该图片是否存在，存在则抓取
              $p_file_name = $uid."_".spider::genImgFileName($l_parent_img);
              if(file_exists($img_tar_path."/".$l_u_path."/".$p_file_name)){
                echo $img_tar_path."/".$l_u_path."/".$p_file_name." img_exist!".NEW_LINE_CHAR;
              }else {
              }
              // 实施抓取,全部覆盖
                $Files = new Files();
                echo date("Y-m-d H:i:s"). " " .$l_img_url.NEW_LINE_CHAR;  // 便于查看
                $l_h_u = array();
                $l_cot = request_cont($l_h_u,$l_img_url,"",$timeout);
                // 抓取内容为空的话，先不做处理，也保存到文件中去
                if (""!=$l_cot) $Files->overwriteContent($l_cot,$img_tar_path."/".$l_u_path."/".$p_file_name);
                if (file_exists(WATERMARK_FILE)) spider::watermark_pic($img_tar_path."/".$l_u_path."/".$p_file_name,WATERMARK_FILE);

                usleep(1000);  // 下载停顿下

            }
          }
        }

        // blshe 有非常奇怪的情况, 上上级有链接的情况
        if ("blshe.com"==getSimpleDomain($a_url)) {
          if( $l_ele->parent() ){
            if ("a"!= $l_ele->parent()->tag && $l_ele->parent()->parent()) {
              if ("a" == $l_ele->parent()->parent()->tag) {
                $l_p_p_href = html_entity_decode($l_ele->parent()->parent()->href);
                if("h"!=substr($l_p_p_href,0,1)&&"/"!=substr($l_p_p_href,0,1)) $l_p_p_href = "/".$l_p_p_href;
                // 替换为绝对链接
                $l_ele->parent()->parent()->href = get_abs_url( $a_url, $l_p_p_href);
              }
            }
          }
        }

        // 抓取图片, 无论是否本网站的图片
        // 先检测该图片是否存在，存在则抓取
        $l_file_name = $uid."_".spider::genImgFileName($l_img_url);
        if(file_exists($img_tar_path."/".$l_u_path."/".$l_file_name)){
          echo $img_tar_path."/".$l_u_path."/".$l_file_name." img_exist!".NEW_LINE_CHAR;
        }else {
        }
          // 实施抓取,覆盖
          $Files = new Files();
          echo date("Y-m-d H:i:s"). " " .$l_img_url.NEW_LINE_CHAR;  // 便于查看
          $l_h_u = array();
          $l_cot = request_cont($l_h_u,$l_img_url,"",$timeout);
          if (""!=$l_cot) $Files->overwriteContent($l_cot,$img_tar_path."/".$l_u_path."/".$l_file_name);
          if (file_exists(WATERMARK_FILE)) spider::watermark_pic($img_tar_path."/".$l_u_path."/".$l_file_name,WATERMARK_FILE);

          usleep(1000);  // 下载停顿下


        // 拼装新地址, 规则同上，文件名也同上用新文件名
        $l_n_img_url = $img_url_path."/".$l_u_path."/".$l_file_name;
        if($l_if_real_src["if_real"]) {
          $l_ele->real_src = $l_n_img_url;
          $l_ele->src = $l_n_img_url;
        }else {
          $l_ele->src = $l_n_img_url;
        }

        // 同时图片的链接地址也相应换成一样的地址（原来就是一样的地址）
        if(1==$l_if_parent_img) {
          $l_ele->parent()->href = $img_url_path."/".$l_u_path."/".$p_file_name;
        }
      }
      $a_str = $l_html->innertext;  // 图片地址被替换过了的

      if (isset($l_ele)) $l_ele->clear();unset($l_ele);  // 释放内存
      $l_html->clear();unset($l_html);  // 清理内存
    }

    // 如果有视频、音频文件
    if (false!==strpos($a_str,"<embed")) {
      $l_base_info = parse_url($a_url);

      $l_embed_name_url = 0;
      $l_html = str_get_html($a_str);
      foreach ($l_html->find("embed[src]") as $l_ele){
        $l_src = trim($l_ele->src);
        if ("/"==substr($l_src,0,1)) {
          // 需要替换链接
          $l_src = $l_base_info["scheme"]."://".$l_base_info["host"].$l_src;
          $l_ele->src = $l_src;  // 修改html的内容
        }
        $l_embed_name_url = 1;
      }
      $a_str = $l_html->innertext;  // 视频音频地址被替换过了的

      if($l_embed_name_url) $l_ele->clear();unset($l_ele);// 释放内存
      $l_html->clear();unset($l_html);  // 清理内存
    }
    if (false!==strpos($a_str,"<param")) {
      $l_base_info = parse_url($a_url);
      $l_param_name_url = 0;
      $l_html = str_get_html($a_str);
      foreach ($l_html->find("param[name=URL]") as $l_ele){
        $l_src = trim($l_ele->value);
        if ("/"==substr($l_src,0,1)) {
          // 需要替换链接
          $l_src = $l_base_info["scheme"]."://".$l_base_info["host"].$l_src;
          $l_ele->value = $l_src;  // 修改html的内容
        }
        $l_param_name_url = 1;
      }
      $a_str = $l_html->innertext;  // 视频音频地址被替换过了的

      if($l_param_name_url) $l_ele->clear();unset($l_ele);  // 释放内存
      $l_html->clear();unset($l_html);  // 清理内存
    }

    return array("img"=>$l_img,"content"=>$a_str);
  }


  // 统一的图片加水印代码，由其v1提供
  function watermark_pic($srcfile,$watermark_file){
    if(function_exists('imageCreateFromJPEG') && function_exists('imageCreateFromPNG') && function_exists('imageCopyMerge')) {
      //$srcfile = A_DIR.'/spring.jpg';//需要加水印的图片
      //$watermark_file = A_DIR.'/watermark.png';//水印图片位置
      $watermarkstatus = 9;//水印放置位置
      $fileext = strtolower(trim(substr(strrchr($watermark_file, '.'), 1)));
      $ispng = $fileext == 'png' ? true : false;
      $attachinfo = @getimagesize($srcfile);
      if(!empty($attachinfo) && is_array($attachinfo) && $attachinfo[2] != 1 && $attachinfo['mime'] != 'image/gif') {
      } else {
        return '';
      }
      $watermark_logo = $ispng ? @imageCreateFromPNG($watermark_file) : @imageCreateFromGIF($watermark_file);
      if(!$watermark_logo) {
        return '';
      }
      $logo_w = imageSX($watermark_logo);
      $logo_h = imageSY($watermark_logo);
      $img_w = $attachinfo[0];
      $img_h = $attachinfo[1];
      $wmwidth = $img_w - $logo_w;
      $wmheight = $img_h - $logo_h;
      if(is_readable($watermark_file) && $wmwidth > 100 && $wmheight > 100) {
        switch ($attachinfo['mime']) {
          case 'image/jpeg':
            $dst_photo = imageCreateFromJPEG($srcfile);
            break;
          case 'image/gif':
            $dst_photo = imageCreateFromGIF($srcfile);
            break;
          case 'image/png':
            $dst_photo = imageCreateFromPNG($srcfile);
            break;
          default:
            break;
        }
        switch($watermarkstatus) {
          case 1:
            $x = +5;
            $y = +5;
            break;
          case 2:
            $x = ($img_w - $logo_w) / 2;
            $y = +5;
            break;
          case 3:
            $x = $img_w - $logo_w - 5;
            $y = +5;
            break;
          case 4:
            $x = +5;
            $y = ($img_h - $logo_h) / 2;
            break;
          case 5:
            $x = ($img_w - $logo_w) / 2;
            $y = ($img_h - $logo_h) / 2;
            break;
          case 6:
            $x = $img_w - $logo_w - 5;
            $y = ($img_h - $logo_h) / 2;
            break;
          case 7:
            $x = +5;
            $y = $img_h - $logo_h - 5;
            break;
          case 8:
            $x = ($img_w - $logo_w) / 2;
            $y = $img_h - $logo_h - 5;
            break;
          case 9:
            $x = $img_w - $logo_w - 5;
            $y = $img_h - $logo_h - 5;
            break;
        }
        if($ispng) {
          $watermark_photo = imagecreatetruecolor($img_w, $img_h);
          imageCopy($watermark_photo, $dst_photo, 0, 0, 0, 0, $img_w, $img_h);
          imageCopy($watermark_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h);
          $dst_photo = $watermark_photo;
        } else {
          imageAlphaBlending($watermark_logo, true);
          imageCopyMerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h, 30);
        }
        switch($attachinfo['mime']) {
          case 'image/jpeg':
            imageJPEG($dst_photo, $srcfile, 85);
            break;
          case 'image/gif':
            imageGIF($dst_photo, $srcfile);
            break;
          case 'image/png':
            imagePNG($dst_photo, $srcfile);
            break;
        }
      }
      //echo "success";
    }
  }


  //
  function proceed(&$dbR, &$dbW, &$p_arr, &$a_vals, $a_grab_tbl){
    // 将url算法执行以后获取到相应的新url，如果返回的url是一个字符串并且跟原来的一样则无需处理，直接抓取
    // 如果url不等，则需要新增到数据表中去；如果返回的是一个非空数组，同样需要一一insert
    $l_url = spider::getUrls($dbR, $dbW, $p_arr, $a_vals);
    //print_r($l_url);print_r($p_arr);print_r($a_vals); exit;
    $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr);
    $dbR->dbo = &DBO('',$dsn);$dbR->SetCurrentSchema($p_arr['db_name']);
    $dbW->dbo = &DBO('',$dsn);$dbW->SetCurrentSchema($p_arr['db_name']);

    $l_tablename = $a_grab_tbl;

    if (is_string($l_url) && $l_url==$a_vals["url"]) {
      // 单条就直接处理，多条则进行递归处理
      spider::proc_one_request($dbR,$dbW,$p_arr,$a_vals,$a_vals,$l_tablename);
    }else {
      // 如果有算法，一般是放到新数据表中去，通常有多条url，则此时需要具备递归处理能力
      if (!empty($a_vals['son_request_tbl'])) {
        // 表示已经存在此表或者指定了需要创建的表名
        // 替换掉表名为按照日期的表名, 首先判断是否指定了表名，如果指定了就按照指定的表名
        $l_tablename_new = $a_vals['son_request_tbl'];
      }else {
        $l_tablename_new = $l_tablename.'_'.date("Ym");
        // 需要将原来表中的 son_request_tbl 字段更新一下
        $dbW->table_name = $a_grab_tbl;
        set_status_by_id($dbR, $dbW, $a_grab_tbl, $a_vals['id'], array("son_request_tbl"=>$l_tablename_new));
      }
      if (is_string($l_url)) $l_urls[] = $l_url;  // 统一为数组进行处理
      else $l_urls = $l_url;  // 数组
      // 如果是很多url(即数组的话，需要插入另外一张表结构同request的表中，并且levelnum设置为2 parent_id不=0了) 然后对这些新的url逐一进行处理。

      // 按照日期创建数据一张新表类似于request
      // 获取request的表结构
      $l_tmp = $dbR->SHOW_CREATE_TABLE($l_tablename);
      if (!array_key_exists("Create Table", $l_tmp[0])){
        return ;
      }else {
        $l_sql = $l_tmp[0]["Create Table"];
      }

      $l_sql = preg_replace('/^CREATE TABLE( IF NOT EXISTS)? `('.$l_tablename.')`/i', 'CREATE TABLE IF NOT EXISTS `'.$l_tablename_new.'`', $l_sql, 1);
      //echo $l_sql . NEW_LINE_CHAR . NEW_LINE_CHAR;
      //exit;
      $dbW->Query($l_sql);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " l_err:" . var_export($l_err, TRUE);
        return ;
      }
      // 创建新表end

      // 将抓取请求写入新的数据表表中去 begin
      $dbW->table_name = $l_tablename_new;
      $l_tm_arr = array();
      foreach ($l_urls as $l_k=>$l_url) {
        // 需要对url做一些判断，可以参考PEAR包中的validate，此处仅仅判断是否为http(s)://开头
        $l_url = trim($l_url);
        if ( false!==strpos($l_url,"://")) {
          $data_arr = array(
            "name_cn"=>$a_vals["name_cn"],
            "url"=>$l_url,
            "parent_id"=>$a_vals["id"],
            "p_id_to"=>$a_vals["p_id_to"],
            "t_id_to"=>$a_vals["t_id_to"],
            "levelnum"=>($a_vals["levelnum"]+1),  //
            "creator"=>(isset($_SESSION["user"]["username"])) ? $_SESSION["user"]["username"] : 'robot',
            "createdate"=>date("Y-m-d"),
            "createtime"=>date("H:i:s"),

            "if_article"=>$a_vals['if_article'],
            "if_album"  =>$a_vals['if_album'],
          );
          //print_r($data_arr);

          $l_exist_c = cString_SQL::getUniExist($data_arr, array('url'));
          $l_exi_one = $dbW->getExistorNot($l_exist_c);
          if (PEAR::isError($l_exi_one)) {
            echo " error message： " .$l_exi_one->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
            //exit;
            return ;
          }
          if (!empty($l_exi_one) && is_array($l_exi_one)) {
            // 存在则不用入库
            $data_arr = array_merge($l_exi_one,$data_arr);  // 模拟从数据库中获取到
          }else {
            $dbW->insertOne($data_arr);
            // 错误处理
            $l_err = $dbW->errorInfo();
            if ($l_err[1]>0){
              // 数据库连接失败后
              echo date("Y-m-d H:i:s") . " 出错了， -错误信息： " . $l_err[2]. "." . NEW_LINE_CHAR;
              //return null;
            }else {
              $data_arr['id'] = $dbW->LastID();
            }
          }

          // 重新组织一下数组
          //$l__ids[] = $data_arr['id'];
          $l_tm_arr[] = $data_arr;
        }
      }
      // url入新库完成

      // $l__ids 的话涉及到重新请求可能存在主从库同步的问题,
      // 再次调用开头的过程. 形成递归, 主要是针对算法部分可能会进入此处
      begin_process($dbR,$dbW,$p_arr,$l_tm_arr,$l_tablename_new);
    }

    // 可能涉及到主从库同步的问题，因此直接使用刚才的数据
    /*if (isset($l_tablename_new)) {
      $l_tablename = $dbR->table_name = $l_tablename_new;
      //$l_reqs = $dbR->getAlls("where parent_id = ".$a_vals["id"] . " order by id ");
      if(is_array($l_tm_arr[0]))$l_reqs = $l_tm_arr;
      else $l_reqs = array();  // 如果为空能结束程序运行
    }else {
      $l_tablename = $dbR->table_name = $a_grab_tbl;
      $l_reqs = array($a_vals);
    }
    // 似乎要进行错误判断, 暂时先省略吧，如果没有找到则返回NULL
    // 对url逐一进行抓取，并将文章列表入库
    if (!empty($l_reqs)) {
      foreach ($l_reqs as $l_req){
        // 分解出文章列表，将文章列表信息入库到另一张表中. 然后处理文章列表页面，将文章类别页面的连接逐一抓取并入库发布
        //spider::proc_one_request($dbR,$dbW,$p_arr,$a_vals,$l_req,$l_tablename);
      }
    }*/
    //$dbR->dbo->free();
    //$dbW->dbo->free();  // 释放内存
  }


  function getUrls(&$dbR, &$dbW, &$p_arr, &$a_vals){
    // 依据结果，如果有算法，则需要执行一下算法
    $l_ath = trim($a_vals["arithmetic"]);
    //echo $l_ath . "-----------".NEW_LINE_CHAR;
    if (""!=$l_ath) {
      $l_err = array();
      // 首先将算法解析为一维数组
      $l_arr = Parse_Arithmetic::parse_like_ini_file($l_ath);

      // 先获取项目信息，如果设置了项目的话
      // $l_arr["project"] = "name=测试CMS-用于线上";  // 人工指定一个
      if (array_key_exists("project",$l_arr)) {
        // 获取数据库连接信息
        $l_p_info = Parse_SQL::getPinfoByProjCNname($l_arr["project"]);
        $a_vals["pa_p_info"] = $l_p_info;

        $dsn = DbHelper::getDSNstrByProArrOrIniArr($l_p_info);
        $dbR->dbo = &DBO('', $dsn);$dbR->SetCurrentSchema($l_p_info['db_name']);
      }

      // 将code部分放到一个function中去,function名称采用文件名加
      $l_func = preg_replace('/\W/',"_",basename(__FILE__)."_".__LINE__) ."_". $a_vals['id'] . "_" . time();
      // echo $l_func;
      $l_func_str = pinzhuangFunctionStr($l_arr, $l_func, '');

      eval($l_func_str);  // 执行加载一下

      $l_urls = $l_func($dbR);

    }else {
      $l_urls = trim($a_vals["url"]);
    }

    return $l_urls;
  }

  function replac2cnkey(&$a_data_arr,$a_k_arr=array('author'=>'作者')){
    if (!empty($a_k_arr)) {
      foreach ($a_k_arr as $l_k=>$l_cn){
        if (array_key_exists($l_k, $a_data_arr)) {
          $a_data_arr[$l_cn] = $a_data_arr[$l_k];
          unset($a_data_arr[$l_k]);
        }
      }
    }
  }
}

function getCookieArr(){
  // COOKIE begin
  if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))){
    require_once("D:/www/dpa/common/lib/cArray.cls.php");
    $l_file = "C:/Documents and Settings/Administrator/Cookies/admin@ni9ni.com.txt";
  } else {
    require_once("/data0/deve/runtime/common/lib/cArray.cls.php");
    $l_file = "/data0/deve/Cookies/admin@ni9ni.com.txt";  // 需要cookie进行认证，特别是cli模式下
  }
  if(file_exists($l_file)) $_COOKIE = cArray::parse_cookiefile($l_file);
  $cookie_arr = array();
  if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $l_k=>$l_v){
      $l_tmp = array();
      $l_tmp["name"] = $l_k;
      $l_tmp["value"] = $l_v;
      $cookie_arr[] = $l_tmp;
    }
  }
  // COOKIE end
  return $cookie_arr;
}

function getBaseAuth(){
  // basicAuth begin
  $a_BasicAuth = $l_tmp = array();
  if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))){
    $l_auth_file = "D:/www/config/gsps_post.ini";
  }else {
    $l_auth_file = "/data0/deve/config_ini_files/gsps_post.ini";
  }
  if (file_exists($l_auth_file)) {
    $l_tmp = parse_ini_file($l_auth_file, true);
    if (isset($l_tmp["baseAuth"]["BasicAuth"])) {
      $a_BasicAuth = $l_tmp["baseAuth"];
    }
  }
  // basicAuth end
  return $a_BasicAuth;
}

function getExecProcNum($a_sep="spider.cls.php"){
  if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) return 1;
  if (""==$a_sep) $a_sep = basename(__FILE__);

  $l_cmd = "sudo /bin/ps axu|grep '$a_sep' | grep -v grep";
  $l_ot = array();
  exec($l_cmd,$l_ot);
  $l_num = count($l_ot);
  if ($l_num<=0) {
    $l_num = 0;
  }

  return $l_num;
}

function msg_http_request(&$dbR, &$dbW, $a_info, $a_url){
  echo date("Y-m-d H:i:s"). " " .$a_url. " content empty! or overtime ".NEW_LINE_CHAR;
}
// 一个非常便利的方法
function set_status_by_id(&$dbR, &$dbW, $tbl, $id, $data_arr){
  // 最后将状态设置为doing
  $dbW->table_name = $tbl;
  //$data_arr = array("status_" => $a_status);
  $condition= "id=".$id;
  $dbW->updateOne($data_arr, $condition);
  $l_err = $dbW->errorInfo();
  if ($l_err[1]>0){
    // 数据库连接失败后
    echo date("Y-m-d H:i:s") ." FILE:".__FILE__. " LINE:".__LINE__. " error updateOne id: ". var_export($id,true).", table: $tbl , error_msg: " . var_export($l_err,true). " SQL:". $dbW->getSQL() . NEW_LINE_CHAR;
    return null;
  }
}
function mc_file(){
  $a = microtime();
    list($usec, $sec) = explode(" ", $a);
    $b = (float)$usec + (float)$sec;
    return str_replace(".","_",$b);
}
