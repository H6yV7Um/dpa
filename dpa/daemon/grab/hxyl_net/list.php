<?php
/**
 * hxyl.net 文章抓取
 */
class hxyl_net
{
  var $grab_page_num = 3;    // 最多抓取多少翻页

function content_conv($a_str){
  if (false===$a_str || ''==$a_str) {
    // 网络不通的时候, 返回的将是 bool(false)
    return $a_str;
  }
  // 能识别的就不用转码了
  if (false!==strpos($a_str,"河蟹")) {
    return $a_str;
  }else {
    //$html_content = iconv("UTF-8","GBK//IGNORE",$a_str);
    return iconv("GBK","UTF-8//IGNORE",$a_str);
  }
}
// 抓取文章列表
function get_article_list(&$dbR, &$dbW, $uni_a, $a_info, $a_domain, $db_old_list, $timeout=60){
  $l_alist = array(); // 所有的文章列表
  $l_arti_url = array(); // 文章链接汇总数组
  $l_total = array(); // 文章总数等信息
  $l_hidden = 0;     // 隐藏的文章总数
  $l_url = trim($a_info["url"]);

  // 抓取指定网址
  echo date("Y-m-d H:i:s"). " " .$l_url.NEW_LINE_CHAR;  // 便于查看
  $l_h_u = array();
  $html_content = $this->content_conv(request_cont($l_h_u, $l_url, '', $timeout));
  //if ($GLOBALS['cfg']['DEBUG']) $html_content = file_get_contents('D:/www/dpa/daemon/grab/hxyl_1.html');

  if (""==$html_content) {  // bool(false)也能成立
    msg_http_request($dbR, $dbW, $a_info,$l_url);
    return $l_alist;
  }

  $l_sep = '<div class="entry">';
  if (false!==strpos($html_content,$l_sep)) {
    $page_id = 1;
    // 需要循环页码获取所有的列表
    for ($i=1;$i<=$page_id;$i++){
      $l_base_url_a = parse_url($l_url);
      $M_DOMAIN = $l_base_url_a["host"];
      $l_base_scheme = $l_base_url_a["scheme"];

      if ($i<=1) {
        $html_content2 = $html_content;  // 第一页直接使用抓取的默认页面即是
      } else {
      // 获取列表 http://hxyl.net/page/11
      $l_url2 = $l_base_scheme."://".$M_DOMAIN."/page/".$i."/";
      echo date("Y-m-d H:i:s"). " " .$l_url2.NEW_LINE_CHAR;  // 便于查看
      $l_h_u2 = array();
      $html_content2 = $this->content_conv(request_cont($l_h_u2,$l_url2, '', $timeout));
      //if ($GLOBALS['cfg']['DEBUG']) $html_content2 = file_get_contents('D:/www/dpa/daemon/grab/hxyl_1.html');
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
    echo date("Y-m-d H:i:s"). "  " ."no var _edi".NEW_LINE_CHAR;
  }
  $l_total["arti_hidden"] = $l_hidden;
  if(!array_key_exists("totalCount",$l_total)) $l_total["totalCount"] = count($l_alist);

  return array($l_alist,$l_total);
}

function proc_list($a_str,$htt_domain="",$a_domain){
  $l_arr = array();
  $l_hidden = 0;

  $l_sep = '<div class="entry">';

  if (false!==strpos($a_str,$l_sep)) {
    $l_tmp = explode($l_sep,$a_str);

    foreach ($l_tmp as $l_k=>$l_tbl){
      if ($l_k>0) {
        $l_tbl = $l_sep.$l_tbl;
        $i = $l_k - 1;
        $l_html = str_get_html($l_tbl);

        //
        if (false!==strpos($l_html->find("div.entry",0)->innertext,"<h2")) {
          if (false!==strpos($l_html->find("div.entry",0)->find("h2",0)->innertext,"<a")) {
            // 文章链接
            $link = $l_html->find("div.entry",0)->find("h2",0)->find("a",0)->href;
            $link = get_abs_url($htt_domain,$link);

            // 文章标题
            $title= $l_html->find("div.entry",0)->find("h2",0)->find("a",0)->plaintext;

            // short_text, 摘要部分介于</h2>和<p>Tags之间。
            // 1) 去掉其他script广告或功能方法
            if (false!==strpos($l_tbl,'<script')) {
              foreach($l_html->find("div.entry",0)->find("script") as $l_scr) $l_scr->outertext = "";
            }
            // 2) 替换末尾的‘继续阅读’
            if (false!==strpos($l_tbl, ' class="more-link">')) {
              $l_html->find("a.more-link",0)->outertext = '<span class="moretext"></span>';
            }

            $short_text = "";
            $l_aa = explode('</h2>',$l_html->innertext);
            if (false!==strpos($l_aa[1],'<p>Tags:')) {
              $l_bb = explode('<p>Tags:',$l_aa[1]);
              $short_text = $l_bb[0];
            }else if (false!==strpos($l_aa[1],'<p class="info">')) {
              $l_bb = explode('<p class="info">',$l_aa[1]);
              $short_text = $l_bb[0];
              $short_text = str_replace("<p></p>","",$short_text);// 替换掉空段落
            }

            $l_arr[$i]["title"] = trim($title);
            $l_arr[$i]["short_text"] = trim($short_text);
            $l_arr[$i]["url"] = trim($link);
          }
        }

        $l_html->clear();unset($l_html);  // 释放内存
      }
    }
  }else {
    // 页面空了
    echo " article_list empty!".NEW_LINE_CHAR;
  }

  return array($l_arr,$l_hidden);
}

function getArticleType(&$l_html,$l_spe,$l_elem,$tezhen="/category/"){
  // 分类
  $l_arr = array();
  $l_rlt = array();
  if (false!==strpos($l_html->innertext,$l_spe)) {
    if (false!==strpos($l_html->find($l_elem,0)->innertext,"<a")) {
      foreach ($l_html->find($l_elem,0)->find("a") as $l_a){
        if (false!==strpos($l_a->href, $tezhen)) {
          $l_arr["name"] = trim($l_a->plaintext);
          $l_arr["href"] = trim($l_a->href);
          $l_rlt[] = $l_arr;
        }
        $l_a_str_get = 1;
      }
      if ($l_a_str_get) $l_a->clear();unset($l_a);
    }
  }
  return $l_rlt;
}

function getArticleDati(&$l_html,$l_spe,$l_elem,$num=0){
  $l_mat = array();
  // 发表时间
  if (false!==strpos($l_html->innertext,$l_spe)) {
    $l_dati = $l_html->find($l_elem,$num)->plaintext;
    $l_dati = str_replace("&nbsp;","",$l_dati);
    $l_dati = str_replace(array("年","月","日"),"-",$l_dati);  // 替换其中的年月日

    if (preg_match("/(\d{4}-\d+-\d+)-[,\s]+(\d+:\d+(:\d+)?)/", $l_dati, $l_mat)) {
      return $l_mat;
    }else if( preg_match("/(\d{4}-\d+-\d+)-/", $l_dati, $l_madate) && preg_match("/(\d+:\d+(:\d+)?)/", $l_dati, $l_matime)){
      // 获取日期和时间
      $l_mat[1] = $l_madate[1];
      $l_mat[2] = $l_matime[1];
      // 下午则需要加上12小时
      if (false!==strpos($l_dati," 下午 ") || false!==strpos($l_dati," pm ")) {
        $l_tmp = explode(":",$l_mat[2]);
        if($l_tmp[0]<12) $l_tmp[0] = intval($l_tmp[0])+12;
        $l_mat[2] = implode(":",$l_tmp);
      }

      unset($l_madate);unset($l_matime);
    }else if( preg_match("/(\d+-\d+)-[,\s]+(\d+:\d+(:\d+)?)/", $l_dati, $l_mat) ){
      // 没有年份的格式表示, 在 darkfire 模板中有用到, 获取日期和时间
      return $l_mat;
    }
  }

  return $l_mat;
}


function get_arti_detail(&$dbR, &$dbW, $a_arr, $timeout=60){
  $l_arr = array();
  sleep(1);
  // 抓取指定网址, 是utf8编码的
  echo date("Y-m-d H:i:s"). " " .$a_arr["url"].NEW_LINE_CHAR;  // 便于查看
  $l_h_u = array();
  $html_content_utf8 = $this->content_conv(request_cont($l_h_u,$a_arr["url"], '',$timeout));

  $l_sep = '<div class="entry single">';

  if (false!==strpos($html_content_utf8,$l_sep)) {
    $html_content_utf8 = spider::content_replace_special($html_content_utf8);
    $l_html = str_get_html($html_content_utf8);

    $a_str = $l_html->find("div.single",0)->innertext;

    // 日期，时间
    $l_dati = $this->getArticleDati($l_html->find("div.single",0),'<p class="info">','p.info',0);
    //print_r($l_dati);

    // 作者
    $l_author = $this->getArticleType($l_html->find("div.single",0), '<em class="author">','em.author',"/author/");
    //print_r($l_author);

    // 标签, 分类
    $l_tag = array();
    $l_cate = "";
    if (false!==strpos($a_str,'<p')) {
      foreach ($l_html->find("div.single",0)->find("p") as $l_p){
        if (false!==strpos($l_p->plaintext,"Tags:")) {
          $l_a_str_get = 0;
          foreach ($l_p->find("a") as $l_a){
            if (false!==strpos($l_a->href, "/tag/")) $l_tag[] = trim($l_a->plaintext);
            // 分类只有一个
            if (false!==strpos($l_a->href, "/category/")) $l_cate = trim($l_a->plaintext);
            $l_a_str_get = 1;
          }
          if ($l_a_str_get) $l_a->clear();unset($l_a);
          $l_p->outertext = "";
        }
      }
    }

    // 去掉标题
    $l_title = $l_html->find("div.single",0)->find("h2",0)->innertext;
    if (false!==strpos($a_str,'<h2')) $l_html->find("div.single",0)->find("h2",0)->outertext = "";
    // 去掉日期，时间
    if (false!==strpos($a_str,'<p class="info">')) $l_html->find("div.single",0)->find("p.info",0)->outertext = "";
    // 去掉转发微博
    if (false!==strpos($a_str,'<p align="center">')) $l_html->find("div.single",0)->find("p[align=center]",0)->outertext = "";
    // style
    if (false!==strpos($a_str,'<span style="float:right">')) $l_html->find("div.single",0)->find("span[style=float:right]",0)->outertext = "";
    // 去掉div广告
    if (false!==strpos($a_str,'<div align="center">')) $l_html->find("div.single",0)->find("div[align=center]",0)->outertext = "";
    // 去掉其他script广告或功能方法
    if (false!==strpos($a_str,'<script')) {
      $l_a_str_get = 0;
      foreach($l_html->find("div.single",0)->find("script") as $l_scr) {
        $l_scr->outertext = "";
        $l_a_str_get = 1;
      }
      if ($l_a_str_get) $l_scr->clear();unset($l_scr);  // 释放内存
    }
    if (false!==strpos($a_str,'<div class="posts_lists">')) $l_html->find("div.single",0)->find("div.posts_lists",0)->outertext = "";
    if (false!==strpos($a_str,'<p class="entrynavigation">')) $l_html->find("div.single",0)->find("p.entrynavigation",0)->outertext = "";
    // 最后一个 http://www.bangnitao.net 和 原文连接字样
    $l_a_str_get = 0;
    foreach ($l_html->find("div.single",0)->find("a") as $l_a){
      if (false!==strpos($l_a->href, "http://www.bangnitao.net") ||
          false!==strpos($l_a->href, "http://feed.feedsky.com/kisshi")  ) {
            $l_a->outertext = "";
      }
      $l_a_str_get = 1;
    }
    if ($l_a_str_get) $l_a->clear();unset($l_a);  // 释放内存

    $l_content = trim($l_html->find("div.single",0)->innertext);
    // 原文链接：，转载请留下本文链接！更多精彩敬请订阅 等文本清除
    $l_content = str_replace(array('原文链接：', $l_title, '，转载请留下本文链接！更多精彩敬请订阅'), '', $l_content);
    $l_content = preg_replace('|<span id=[\'"]?more-\d+[\'"]?></span>|i', '<span id="more"></span>', $l_content, 1);
    if (!empty($l_content)) {
      if (!empty($l_dati)) {
        $l_arr["createdate"] = $l_dati[1];
        $l_arr["createtime"] = $l_dati[2];
      }
      if (!empty($l_author)) {
        $l_arr["author"] = $l_author[0]["name"];
      }
      if (!empty($l_tag)) {
        $l_arr["tags"] = $l_tag;
      }
      if (!empty($l_cate)) {
        $l_arr["atype"] = $l_cate;
      }
      // 所属栏目是必须项
      $l_arr["suoshulanmu"] = '笑话';

      $l_img_cont = spider::downimg_and_chgimg($a_arr["url"],$l_content,2222,false,IMAGE_SAVE_PATH_HOST."/".IMAGE_SAVE_PATH_RELATE."/blogs",IMAGE_SAVE_PATH_URL."/".IMAGE_SAVE_PATH_RELATE."/blogs");  // 下载图片和替换图片链接
      if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) $l_img_cont["content"] = cn_substr($l_img_cont["content"],700,'utf8',''); // windows下cmd长度有限制
      $l_arr["content"] = $l_img_cont["content"];
      $l_arr["img"] = $l_img_cont["img"];
    }

    $l_html->clear();unset($l_html);  // 释放内存
  }else {
    //
    echo date("Y-m-d H:i:s"). " url: ".$a_arr["url"]." blog article_content can not find ".$l_sep ."".NEW_LINE_CHAR;
    var_dump($html_content_utf8);
    //exit;
  }

  return $l_arr;
}

function is_web_exist($a_url, $a_domain="", $timeout=60){

}
}
