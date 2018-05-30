<?php
/**
 * 99qh.com 文章抓取
 */
function content_conv_99qh_com($a_str){
  // 能识别的就不用转码了
  if (false!==strpos($a_str,"99期货")) {
    return $a_str;
  }else {
    //$html_content = iconv("UTF-8","GBK//IGNORE",$a_str);
    return iconv("GBK","UTF-8//IGNORE",$a_str);
  }
}
// 抓取文章列表
function get_article_list_99qh_com(&$dbR, &$dbW, $uni_a, $a_info, $a_domain, $db_old_list=array(), $timeout=60){
  $l_alist = array(); // 所有的文章列表
  $l_total = array(); // 文章总数等信息
  $l_hidden = 0;     // 隐藏的文章总数
  $l_url = trim($a_info["url"]);

  // 抓取指定网址
  echo date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;  // 便于查看
  $l_h_u = array();
  $html_content = content_conv_99qh_com(request_cont($l_h_u, $l_url, '', $timeout));
  if (""==$html_content) {
    msg_http_request($dbR, $dbW, $a_info,$l_url);
    return array($l_alist,$l_total);
  }

  $l_sep = 's_99qh_line6';
  if (false!==strpos($html_content,$l_sep)) {

    $l_base_url_a = parse_url($l_url);
    $M_DOMAIN = $l_base_url_a["host"];
    $l_base_scheme = $l_base_url_a["scheme"];
    parse_str($l_base_url_a["query"],$l_tmp_arr);  // 分解相关的参数出来
    $l_breed = substr($l_tmp_arr["tag"],0,2);
    $l_type = substr($l_tmp_arr["tag"],2,4);

    // 没有翻页的文章列表，以后有空再完善有文章列表的
    $l_alist = proc_list_99qh_com($html_content,$l_tmp_arr["date"],$l_url);

  }else {
    // 该页面可能有问题，没有找到相关信息
    //echo date("Y-m-d H:i:s"). "  " ."no var _edi".NEW_LINE_CHAR;
    echo date("Y-m-d H:i:s")." 未抓取到 $l_url 的数据;".NEW_LINE_CHAR;
  }
  $l_total["arti_hidden"] = $l_hidden;
  if(!array_key_exists("totalCount",$l_total)) $l_total["totalCount"] = count($l_alist);

  return array($l_alist,$l_total);
}

function proc_list_99qh_com($html,$_Date,$l_url){
  $s_info = array();
  // if ("UTF-8"!=$in_charact) $html = iconv($in_charact,"UTF-8//IGNORE",$title);
  $l_w = str_get_html($html);
  $l_use = $l_w->find("td.s_99qh_line6",0)->innertext;  // 需要用到的部分
  $l_w->clear();unset($l_w);

  //
  $l_html = str_get_html($l_use);;

  $i=0;
  foreach($l_html->find("tr") as $trs)
  {
    //  保证是文章标题和链接
    if (false!==strpos($trs->innertext,"s_99qh_12px_important_special")) {
      // 首先保证是当天的文章，即时间一定要核对上
      $dtime = trim($trs->find("td",1)->plaintext);
      $l_tt = explode(" ",$dtime);
      //print_r($l_tt);
      //var_dump($_Date);
      // 抓取的日期同页面日期不一致则直接跳过,通常碰到第一个就会退出
      if (trim($l_tt[0])!=$_Date) {
        echo date("Y-m-d H:i:s")." $l_url  datetime do not match! ".NEW_LINE_CHAR;
        if (0==$i) {  // 第一条不匹配就退出
          $trs->clear();unset($trs);
          break;
        }else {
          continue;
        }
      }else {
        $s_info[$i]["createdate"] = $l_tt[0];  // 日期
        $s_info[$i]["createtime"] = $l_tt[1];  // 时间
        $title = trim($trs->find("td",0)->plaintext);        // 标题
        $s_info[$i]["title"] = ("UTF-8"==$out_charact)?$title:iconv("UTF-8",$out_charact."//IGNORE",$title);
        $s_info[$i]["url"]  = trim($trs->find("a" ,0)->href);    // 链接

        $i++;
      }
    }
    $trs->clear();unset($trs);
  }

  // clean up memory,一定要清理内存，不然就memory leak了
  $l_html->clear();unset($l_html);
  return $s_info;
}

function get_arti_detail_99qh_com(&$dbR, &$dbW, $l_info, $timeout=60){
  $l_arr = array();
  if (empty($l_info)) {
    return "";  // 直接返回
  }

  echo date("Y-m-d H:i:s"). " " .$l_info["url"].NEW_LINE_CHAR;  // 便于查看
  $l_h_u = array();
  $_cont = request_cont($l_h_u, $l_info["url"], '',$timeout);  // 强制输出为utf8的

  if (false!==strpos($_cont,"div_content")) {
    $l_html = str_get_html($_cont);
    // 获取本页时间 ts_Time
    $ts_Time = trim($l_html->find("span[id=ts_Time]",0)->innertext);
    $date = str_replace(array("年","月","日"),"-",$ts_Time);  //
    $l_tmp= explode("-",$date);

    if (false!==strpos($_cont,"xixi_070926_title1")) {
      $l_html->find("span.xixi_070926_title1",0)->outertext = ""; // 清空
    }
    $cont = $l_html->find("div[id=div_content]",0)->innertext;    // 需要用到的部分

    // 去掉几部分
    $cont = str_replace(array("<本文结束>",$l_tmp[1]."/".$l_tmp[2],"(010)<br>"),"",$cont);
    $l_arr["content"] = $cont;

    // clean up memory,一定要清理内存，不然就memory leak了
    $l_html->clear();unset($l_html);
  }

  return $l_arr;
}
