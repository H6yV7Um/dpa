<?php
/**
 * qiushibaike_com.cls.php
 * qiushibaike.com 糗事百科 文章抓取
 *

php /data0/deve/projects/daemon/grab/spider.cls.php -i 3 >> /data1/logs/qiushibaike.txt &
php D:/www/dpa/daemon/grab/spider.cls.php -i 3 >> D:/qiushibaike.txt

 */
class qiushibaike_com
{
  var $grab_page_num = 3;    // 最多抓取多少翻页

  // 进行转码均转为utf8. 特别地，会有一些特定词语用于判定。
  function content_conv($a_str){
    if (false===$a_str || ''==$a_str) {
      // 网络不通的时候, 返回的将是 bool(false)
      return $a_str;
    }
    // 能识别的就不用转码了
    if (false!==strpos($a_str,"糗事")) {
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

    $l_sep = '<div class="block untagged"';
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
          // 获取列表 http://www.qiushibaike.com/8hr/page/3
          $l_url2 = $l_base_scheme."://".$M_DOMAIN."/8hr/page/".$i;
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

    $l_sep = '<div class="block untagged"';

    if (false!==strpos($a_str,$l_sep)) {
      $l_tmp = explode($l_sep,$a_str);

      foreach ($l_tmp as $l_k=>$l_tbl){
        // 由于用的前缀分段，第一项(键值为0)不是想要的数据，因此从第二项开始
        if ($l_k>0) {
          $l_tbl = $l_sep.$l_tbl;
          $l_html = str_get_html($l_tbl);

          // 获取文章连接，通常通过id可以拼装出来
          if (false!==strpos($l_html->find("div.block",0)->innertext, '<div class="detail"')) {
            if (false!==strpos($l_html->find("div.block",0)->find("div.detail",0)->innertext,"<a")) {
              // 文章链接
              $link = $l_html->find("div.block",0)->find("div.detail",0)->find("a",0)->href;
              $link = get_abs_url($htt_domain,$link);

              // 确保链接是绝对链接，否则不得返回
              if (false !== strpos($link, "://")) {
                // 摘要和时间
                if (false!==strpos($l_html->find("div.block",0)->innertext, '<div class="content"')) {
                  // 摘要, 即是内容
                  $short_text = $l_html->find("div.block",0)->find("div.content",0)->innertext;

                  // 文章标题, 如果标题为空，则可以默认用内容逗号分割，或者截取固定长度的字数，例如15个汉字
                  $title= $l_html->find("div.block",0)->find("div.detail",0)->find("a",0)->plaintext;
                  if (""==trim($title)) {
                    $title = cn_substr(strip_tags($short_text),50);
                    if (""==trim($title)) $title = "__";  // 保证标题不空
                  }

                  // 检查是否有图片
                  $l_thumb = "";
                  if (false!==strpos($l_html->find("div.block",0)->innertext, '<div class="thumb"')) {
                    // 由于不确定其页面呈现相册的规则，因此采用全部遍历
                    foreach ($l_html->find("div.block",0)->find("div.thumb") as $l_ele){
                      // 无需检查是否有图片, 直接作为内容附加到内容后面
                      //if (false!==strpos($l_ele->innertext, '<img')) {
                      $l_thumb .= "<br />" . $l_ele->innertext;
                    }
                    if (isset($l_ele)) $l_ele->clear();unset($l_ele);  // 释放内存
                  }

                  $short_text .= $l_thumb;  // 同时需要将相册也附带上, 如果有的话

                  // 时间居然放在了title属性中, 如果没有title属性，返回的将是bool(false), trim一下false则变成了空串""
                  $l_dati = $l_html->find("div.block",0)->find("div.content",0)->title;
                  $l_dati = trim($l_dati);
                  if ( "" != $l_dati && preg_match("/(\d{4}-\d+-\d+)[,\s]+(\d+:\d+(:\d+)?)/", $l_dati, $l_mat)  ) {
                    $l_date = $l_mat[1];
                    $l_time = $l_mat[2];

                    // 都匹配上了以后，才进行赋值
                    $l_tmp_arr = array();
                    $l_tmp_arr["title"] = trim($title);
                    $l_tmp_arr["short_text"] = trim($short_text);
                    $l_tmp_arr["url"] = trim($link);
                    $l_tmp_arr["createdate"] = $l_date;
                    $l_tmp_arr["createtime"] = $l_time;

                    $l_arr[] = $l_tmp_arr;
                  }
                }
              }else {
                // 错误的信息的时候，需要输出错误信息
                echo date("Y-m-d H:i:s"). " ". __FILE__ . " ". __LINE__ . " URL_error! " . $l_tbl ." " . var_export(func_get_args(), true) . NEW_LINE_CHAR;
              }
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

  // 非常特殊，因正文页没有什么特别的，因此直接从数据库中获取摘要即可, 时间也使用之前列表抓取的时间
  // $a_arr 数组就是从数据库中获取的，存放着摘要、日期、时间，直接使用即可
  function get_arti_detail(&$dbR, &$dbW, $a_arr, $timeout=60){
    $l_arr = array();
    //sleep(1);

    $l_arr["createdate"] = $a_arr["createdate"];
    $l_arr["createtime"] = $a_arr["createtime"];

    // 所属栏目是必须项
    $l_arr["suoshulanmu"] = '笑话';

    $l_content = $a_arr["short_text"];  // 由于详细页同首页列表没有区别，因此直接使用之前抓取的摘要

    /*$l_img_cont = spider::downimg_and_chgimg($a_arr["url"],$l_content,2222,false,IMAGE_SAVE_PATH_HOST."/".IMAGE_SAVE_PATH_RELATE."/blogs",IMAGE_SAVE_PATH_URL."/".IMAGE_SAVE_PATH_RELATE."/blogs");  // 下载图片和替换图片链接
    if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)) ) $l_img_cont["content"] = cn_substr($l_img_cont["content"],700,'utf8',''); // windows下cmd长度有限制
    $l_arr["content"] = $l_img_cont["content"];
    $l_arr["img"] = $l_img_cont["img"];*/

    $l_arr["content"] = $l_content;

    return $l_arr;
  }
}
