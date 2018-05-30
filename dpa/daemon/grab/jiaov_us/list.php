<?php
/**
 * zhantai.com 文章抓取
 */
class jiaov_us
{
  var $grab_page_num = 2;    // 最多抓取多少翻页

function content_conv($a_str){
  if (false===$a_str || ''==$a_str) {
    // 网络不通的时候, 返回的将是 bool(false)
    return $a_str;
  }
  // 能识别的就不用转码了
  if (false!==strpos($a_str,"首页")) {
    return $a_str;
  }else {
    return iconv("GBK","UTF-8//IGNORE",$a_str);
    //     iconv("UTF-8","GBK//IGNORE",$a_str);
  }
}
// 抓取文章列表
function get_article_list(&$dbR, &$dbW, $uni_a, $a_info, $a_domain, $db_old_list, $timeout=60){
  $l_alist = array(); // 所有的文章列表
  $l_arti_url = array(); // 文章链接汇总数组
  $l_total = array(); // 文章总数等信息
  $l_hidden = 0;     // 隐藏的文章总数
  $l_url = trim($a_info["url"]);
  $l_url = str_replace("http://www.jiaov.us/","http://jiaov.us/",$l_url);
  //$GLOBALS['cfg']['DEBUG'] = true;

  // 抓取指定网址
  echo date("Y-m-d H:i:s"). " " .$l_url." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;  // 便于查看
  if (false===strpos($l_url,"://")) {  // bool(false)也能成立
    msg_http_request($dbR, $dbW, $a_info,$l_url);
    return $l_alist;
  }
  $l_h_u = array();
  $html_content = request_cont($l_h_u, $l_url, '', $timeout);
  if ($GLOBALS['cfg']['DEBUG']) $html_content = file_get_contents('D:/www/dpa/daemon/grab/jiaov_us/bj_55.html');
  $html_content = $this->content_conv($html_content);
  //echo $html_content;exit;
  if (""==$html_content) {  // bool(false)也能成立
    msg_http_request($dbR, $dbW, $a_info,$l_url);
    return $l_alist;
  }

  $l_sep = "<table width='90%' border='0'>";
  if (false!==strpos($html_content,$l_sep)) {
    $page_id = 1;
    // 需要循环页码获取所有的列表
    for ($i=1;$i<=$page_id;$i++){
      $l_base_url_a = parse_url($l_url);
      $M_DOMAIN = $l_base_url_a["host"];
      $l_base_scheme = $l_base_url_a["scheme"];

      // 获取列表 http://www.jiaov.us/bj/55/index1.html
      if ($i<=1) {
        $html_content2 = $html_content;  // 第一页直接使用抓取的默认页面即是
      } else {
        $l_url2 = rtrim($l_url," /")."/index".($i-1).".html";
        str_replace("http://www.jiaov.us/","http://jiaov.us/",$l_url2);
        if (false===strpos($l_url2,"://")) {  // bool(false)也能成立
          msg_http_request($dbR, $dbW, $a_info,$l_url2);
          $html_content2 = "";
        }else{
          echo date("Y-m-d H:i:s"). " " .$l_url2 ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;  // 便于查看
          $l_h_u2 = array();
          $html_content2 = request_cont($l_h_u2,$l_url2, '', $timeout);
          if ($GLOBALS['cfg']['DEBUG']) $html_content2 = file_get_contents('D:/www/dpa/daemon/grab/jiaov_us/bj_55_index1.html');
          $html_content2 = $this->content_conv($html_content2);
        }

        // 根库需要进行不停地访问，防止超时被掐断
        $dbR->dbo = &DBO('grab');
        mysql_ping($dbR->dbo->getConnection());
        $dbR->dbo = &DBO($dbR->l_name0_r);
        mysql_ping($dbR->dbo->getConnection());

        sleep(1);  // 防止频繁抓取被发现
      }
      // 可能超时
      if (""==$html_content2) {
        // 可能是部分空
        msg_http_request($dbR, $dbW, $a_info,$l_url2);
        return $l_alist;
      }

      // 分析出列表
      $l_list_a = $this->proc_list($html_content2,$l_base_scheme."://".$M_DOMAIN, $a_domain);
      // 可以将每次抓取的数据先入库再说，无需一起入库，防止数组太大或者断电、意外退出程序导致数据丢失. 以后完善????
      $l_tmp_a = cArray::Index2KeyArr($l_list_a[0],array('value'=>"url"));
      // 抓取到数据后, 需要依据抓取的数据同上一次数据的对照，
      // 如果发现跟上一次的数据雷同，或者存在其中，则停止抓取 $page_id 设置为1，否则进行加1
      if (!empty($l_tmp_a)){
        // 对于动态翻页的，不能只检查一项，也许被删除了
        if (!in_array($l_tmp_a[0],$l_arti_url)) {
          // 还有一种情况，如果抓取页面中的数据是以前抓取过了的，则同样需要停止翻页抓取了
          $arr_intersect = array_intersect($db_old_list,$l_tmp_a);
          if (!empty($arr_intersect)) {
            $page_id--;  // 能结束循环
          }else {
            $page_id++;
          }
          $l_hidden += $l_list_a[1];
          $l_alist = array_merge($l_alist, $l_list_a[0]);
          $l_arti_url = array_merge($l_arti_url,$l_tmp_a);
        }else {
          $page_id--;  // 能结束循环
        }
      }
      if ($page_id>$this->grab_page_num) {
        $page_id = 1;  // 最多抓取10翻页
      }
      unset($l_tmp_a);
      unset($l_list_a);
      unset($l_h_u2);
    }
  }else {
    // 该页面可能有问题，没有找到相关信息
    echo date("Y-m-d H:i:s"). "  " ."no var _edi" ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() .NEW_LINE_CHAR;
  }
  $l_total["arti_hidden"] = $l_hidden;
  if(!array_key_exists("totalCount",$l_total)) $l_total["totalCount"] = count($l_alist);

  return array($l_alist,$l_total);
}

function proc_list($a_str,$htt_domain="",$a_domain){
  $l_arr = array();
  $l_hidden = 0;

  $l_sep = "<table";

  $l_0tmp = array();
  if (false!==stripos($a_str,$l_sep)) {
    $a_str = str_ireplace($l_sep,$l_sep,$a_str);  // 大小写统一起来
    $l_0tmp = explode($l_sep,$a_str);
    if (count($l_0tmp)>=7) {
      $l_tbl_str = $l_sep.$l_0tmp[6];
    }
  }
  // 分析出列表
  if (isset($l_tbl_str)) {
    $l_html = str_get_html($l_tbl_str);

    // 检查td0里面是否有内容
    if (false===stripos($l_tbl_str,"<td") || false===stripos($l_html->find("td",0),"<p")) {
      $l_html->clear();unset($l_html);  // 释放内存
      return array($l_arr,$l_hidden);
    }
    // 左侧列表，右侧为滚动区，当前只抓取左侧右侧滚动区域以后再抓
    foreach ($l_html->find("td",0)->find("p") as $i=>$l_ptag){

      if (false!==strpos($l_ptag->plaintext,"对不起") || false===strpos($l_ptag->innertext,"<a")) {
        $l_ptag->clear();unset($l_ptag);  // 释放内存
        $l_html->clear();unset($l_html);  // 释放内存
        return array($l_arr,$l_hidden);// 直接退出
      }
      //echo $l_ptag->innertext;

      // 文章链接
      $link = $l_ptag->find("a",0)->href;
      $link = get_abs_url($htt_domain,$link);

      // 文章标题
      $title= $l_ptag->find("a",0)->plaintext;

      // 获取日期, 详细日期可以去详细日期里面, 此处可以不用获取日期
      $l_arr[$i]["title"] = trim($title);
      $l_arr[$i]["url"] = trim($link);

      $l_ptag->clear();unset($l_ptag);  // 释放内存
    }

    $l_html->clear();unset($l_html);  // 释放内存
  }else {
    // 页面空了
    echo " article_list empty! content:".$a_str ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;
  }

  return array($l_arr,$l_hidden);
}

// 分离出时间、联系email、电话/手机
function getArticleDati($a_html){
  $l_mat = array();

  $l_sep = "<br>";
  $a_html = str_ireplace(array("<br />","<br/>"),$l_sep,$a_html);
  $l_2tmp = explode($l_sep,$a_html);

  if (count($l_2tmp)>2 && false!==strpos($l_2tmp[1],"手机/电话:") && false!==strpos($l_2tmp[2],"发布时间:")) {
    // 获取手机/电话
    $l_phone = substr($l_2tmp[1], (strpos($l_2tmp[1],":") + 1) );
    $l_phone = str_replace("&nbsp;"," ", $l_phone);
    $l_phone = trim( html_entity_decode($l_phone) );
    // 如果有qq号码，则分离一下以后完善????

    // 发布时间
    $l_dati = str_replace("&nbsp;"," ", $l_2tmp[2]);
    $l_dati = html_entity_decode($l_dati);
    if (preg_match("/(\d{4}-\d+-\d+)[-,\s]+(\d+:\d+(:\d+)?)/", $l_dati, $l_madt)) {
      $l_mat["createdate"] = $l_madt[1];
      $l_mat["createtime"] = $l_madt[2];
      $l_mat["phone"] = $l_phone;
    }else {
      return $l_mat;
    }

  }else {
    return $l_mat;  // 可能改版什么的，直接返回空数组
  }

  return $l_mat;
}

function getArticleTitle($htmlutf8){
  $l_str = "";

  // 先试着内部，以后从title中
  /*$l_sep = '<h1 class=fs3>';
  if (false!==strpos($htmlutf8, $l_sep)) {
    //
    $l_3tmp = explode($l_sep, $htmlutf8);
    if (preg_match("|([^<]+)</?\w+ |", $l_3tmp[1], $l_madt)) {
      $l_str = $l_madt[1];
      print_r($l_madt);exit;
      return $l_str;
    }
  }*/

  $l_sep = '</title>';
  $htmlutf8 = str_ireplace($l_sep,$l_sep,$htmlutf8);
  if (false!==strpos($htmlutf8, $l_sep)) {
    $l_4tmp = explode($l_sep, $htmlutf8);
    $l_5tmp = explode('<title>',$l_4tmp[0]);
    $l_6tmp = explode(' ',trim($l_5tmp[1]));
    // 去掉第一个和最后一个空格的东西就是中级的部分
    $l_t_n = count($l_6tmp);
    unset($l_6tmp[0]);
    unset($l_6tmp[ $l_t_n-1 ]);
    $l_str = implode(" ",$l_6tmp);
  }

  return $l_str;
}

// 分离出栏目
function getArticleLanMu($a_html){
  $l_str = "";

  $l_sep = "<table";

  if ( false!==strpos($a_html, $l_sep) ) {
    $l_html = str_get_html($a_html);
    $l_str = trim($l_html->find("td",0)->find("a",0)->plaintext);
    $l_html->clear();unset($l_html);  // 释放内存
  } else {
    return $l_str;  // 可能改版什么的，直接返回空数组
  }

  return $l_str;
}

// $l_common_static 用于存放一些共用的静态数据，便于下次请求的时候不必重新进行重复计算
function get_arti_detail(&$dbR, &$dbW, $a_arr, $timeout=60, &$l_common_static){
  $l_arr = array();

  // 根库需要进行不停地访问，防止超时被掐断
  $dbR->dbo = &DBO('grab');
  mysql_ping($dbR->dbo->getConnection());
  $dbR->dbo = &DBO($dbR->l_name0_r);
  mysql_ping($dbR->dbo->getConnection());

  sleep(1);
  // 抓取指定网址, 是gb2312编码的
  $a_arr["url"] = str_replace("http://www.jiaov.us/","http://jiaov.us/",$a_arr["url"]);
  echo date("Y-m-d H:i:s"). " " .$a_arr["url"] ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;  // 便于查看
  if (false===strpos($a_arr["url"],"://")) {  // bool(false)也能成立
    msg_http_request($dbR, $dbW, $a_arr,$a_arr["url"]);
    return $l_arr;
  }
  $l_h_u = array();
  $html_content_utf8 = request_cont($l_h_u,$a_arr["url"], '',$timeout);
  if ($GLOBALS['cfg']['DEBUG']) $html_content_utf8 = file_get_contents('D:/www/dpa/daemon/grab/jiaov_us/bj_55_arti.html');
  $html_content_utf8 = $this->content_conv($html_content_utf8);
  //echo $html_content_utf8;exit;
  $l_sep = '<table';

  $l_0tmp = array();
  if (false!==stripos($html_content_utf8,$l_sep)) {
    $html_content_utf8 = str_ireplace($l_sep,$l_sep,$html_content_utf8);  // 大小写统一起来
    $l_0tmp = explode($l_sep,$html_content_utf8);
    if (count($l_0tmp)>=5) {
      $l_tbl_str = $l_sep.$l_0tmp[4];
    }
  }
  //
  if (isset($l_tbl_str)) {
    if (empty($l_common_static)) {
      $l_common_static = array();

      // 获取到全部城市的拼音数据, 回到根库, 最好重新将$dbR赋值一下, 不然后面说zhuaqu.dpps_project表不存在。原因未找到，以后再找原因????
      //$dbR = new DBR();
      $dbR->dbo = &DBO($dbR->l_name0_r);
      $l_srv_db_dsn = $dbR->getDSN("array");
      if (!empty($l_srv_db_dsn["database"])) $dbR->SetCurrentSchema($l_srv_db_dsn["database"]);

      //$dbR->dbo->getDatabase();  //echo "FILE:".__FILE__." LINE:". __LINE__ .NEW_LINE_CHAR ; print_r($dbR);
      $l_err = $dbR->errorInfo();
      if ($l_err[1]>0){
        // 数据库连接失败后
        echo date("Y-m-d H:i:s") . " FILE:".__FILE__. " error_msg: " . var_export($l_err,true) ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . " ".NEW_LINE_CHAR;
        return $l_arr;
      }
      $dbR->table_name = TABLENAME_PREF."project";
      $p_arr_gongyong = $dbR->getOne("where name_cn='共用数据'");
      if (PEAR::isError($p_arr_gongyong)) {
        echo "LINE:". __LINE__ ." p_arr_gongyong_error_message： " .$p_arr_gongyong->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
        print_r(array_keys($GLOBALS['mdb2_conns']));exit;
        return $l_arr;
      }

      // 从共用数据库中获取到数据
      $dsn = DbHelper::getDSNstrByProArrOrIniArr($p_arr_gongyong);
      $dbR->dbo = &DBO("gongyong",$dsn);
      $dbR->SetCurrentSchema($p_arr_gongyong['db_name']);
      //$dbR->dbo->getDatabase();
      $dbR->table_name = "region_sheng";
      // 获取到 name_eng_zhantai 对应的 name_cn 和 name_eng
      $l_city = $dbR->getAlls("where status_='use' and name_eng_zhantai != '' ", "id,name_eng,name_cn,name_eng_zhantai");
      if (PEAR::isError($l_city)) {
        echo "LINE:". __LINE__ ." l_city_error_message： " .$l_city->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
        print_r(array_keys($GLOBALS['mdb2_conns']));exit;
        return $l_arr;
      }else {
        echo "GET region_sheng data succ! LINE:". __LINE__ ." " .  NEW_LINE_CHAR;
      }
      // 拼装为一个二维数组，并且是以zhangtai拼音为基准的
      $l_common_static = cArray::Index2KeyArr($l_city,array("key"=>"name_eng_zhantai","value"=>array()));
    }
    $l_tbl_str = spider::content_replace_special($l_tbl_str);
    $l_html = str_get_html($l_tbl_str);

    // 检查td0里面是否有内容
    if (false===stripos($l_tbl_str,"<td") || false===stripos($l_html->find("td",0),"<p") || false===stripos($l_html->find("td",0),"<a")) {
      $l_html->clear();unset($l_html);  // 释放内存
      return $l_arr;
    }

    if (false===stripos($l_tbl_str,"联系EMail") || false===strpos($l_tbl_str,"发布时间") || false===strpos($l_tbl_str,"手机/电话")) {
      $l_html->clear();unset($l_html);  // 释放内存
      return $l_arr;
    }

    // 获取标题
    $l_title = $this->getArticleTitle($html_content_utf8);

    // 日期，时间, email, 手机/电话, QQ
    $a_str = $l_html->find("td",0)->innertext;
    $a_str = str_ireplace("<p>","<p>",$a_str);
    $l_1tmp = explode("<p>",$a_str);
    // email 直接通过, 可能为空，但是一定会有一个a标签的
    $l_email = $l_html->find("td",0)->find("a",0)->plaintext;

    $l_dati_phone = $this->getArticleDati($l_1tmp[0]);

    if (!empty($l_dati_phone)) {
      //
      $l_arr["createdate"] = $l_dati_phone["createdate"];
      $l_arr["createtime"] = $l_dati_phone["createtime"];
      $l_arr["short_text"] = $l_dati_phone["phone"];
      $l_arr["title"]    = $l_title;
      $l_arr["author"]    = $l_email;  // 作者

      // 所属栏目是必须项
      $l_lanmu = $this->getArticleLanMu($l_sep . $l_0tmp[1]);
      if (""==$l_lanmu) {
        $l_arr["suoshulanmu"] = '新闻';
        //$l_arr["suoshuzilanmu"] = '';
        $l_arr["grab_url"] = $a_arr["url"];
        $l_arr["s_shu_chengshi"] = "";
      } else {
        $l_zhantai_city = trim( $this->get_city_by_Url($a_arr["url"]) );
        $l_arr["suoshulanmu"] = "交友聚会";
        $l_arr["suoshuzilanmu"] = $l_lanmu;
        $l_arr["grab_url"] = $a_arr["url"];
        $l_arr["s_shu_chengshi"] = $l_common_static[$l_zhantai_city]['name_eng'];
      }

      // 获取文章内容
      $l_content = $l_1tmp[1];
      $l_arr["content"] = $l_content;

      /*$l_img_cont = spider::downimg_and_chgimg($a_arr["url"],$l_content, $a_arr["id"]+0 ,false,IMAGE_SAVE_PATH_HOST."/".IMAGE_SAVE_PATH_RELATE."/blogs",IMAGE_SAVE_PATH_URL."/".IMAGE_SAVE_PATH_RELATE."/blogs");  // 下载图片和替换图片链接
      if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) $l_img_cont["content"] = cn_substr($l_img_cont["content"],700,'utf8',''); // windows下cmd长度有限制
      $l_arr["content"] = $l_img_cont["content"];
      $l_arr["img"] = $l_img_cont["img"];*/
    }

    $l_html->clear();unset($l_html);  // 释放内存
  }else {
    //
    echo date("Y-m-d H:i:s"). " url: ".$a_arr["url"]." content can not find ".$l_sep ."" ." memory usage:".  memory_get_usage() .'; '. " memory usage peak(high):".  memory_get_peak_usage() . NEW_LINE_CHAR;
    var_dump($html_content_utf8);
    //exit;
  }

  return $l_arr;
}

function get_city_by_Url($a_url){
  $l_rlt = "bj";

  $l_tmp = parse_url($a_url);
  if (!empty($l_tmp['path'])) {
    $l_path = ltrim($l_tmp['path']," /");
    $l_2tmp = explode("/",$l_path);
    $l_rlt  = $l_2tmp[0];
  }
  return $l_rlt;
}

}