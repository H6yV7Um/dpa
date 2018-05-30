<?php
/**
 * 文章抓取
 */
class URL_10_77_130_11
{
  // 获取详细信息
  function getdetail(&$dbR, &$dbW, $content, $a_url) {
    $l_r = array();
    if (false !== strpos($content, "<table") && false !== strpos($content, "<tr")) {
      // 所有有href属性的a标签
      $l_xml = str_get_html($content);

      // Parent Directory 需要过滤掉
      $l_tmp = parse_url($a_url);

      foreach($l_xml->find("tr td a") as $l_a) {
        // Parent Directory 需要过滤掉
        if ('Parent Directory' == $l_a->innertext && false !== strpos($l_tmp['path'], $l_a->href))
          continue;
        if ('../' == $l_a->href)
          continue;

        $l_vali_url = self::proc_href($l_a->href, $a_url);
        if ($l_vali_url)
          $l_r[] = $l_vali_url;

        unset($l_vali_url);
        $l_a->clear();unset($l_a);
      }
      // 清下内存
      $l_xml->clear();unset($l_xml);
    }
    return $l_r;
  }


  /**
   * // 相对url转绝对url，如果是js，则不记录
   *
   * @param string address $a_str
   * @param string $p_url
   * @return string
   */
  function proc_href(&$a_str, $p_url){
    $l_str = strtolower(trim($a_str));
    if ("http://" ==substr($l_str, 0, 7) || "https://"==substr($l_str, 0, 8))
      return $a_str;

    // 如果 $a_str 是js 或锚点也不用
    if ("javascript:"==substr($l_str,0,11) || "#"==substr($l_str,0,1))
      return ;

    $l_p_url = parse_url($p_url);
    // 如果是绝对链接或相对链接
    if ("/" == substr(trim($a_str), 0, 1)) {
      return $l_p_url["scheme"] . "://" . $l_p_url["host"] . trim($a_str);
    } else {
      // 相对链接,以后修改下
      return self::transxiangdui2jue($a_str, $p_url);
    }
  }

  function transxiangdui2jue($a_str, $p_url){
    $l_p_url = parse_url($p_url);//echo $p_url ;print_r($l_p_url);echo 111;exit;
    if (!array_key_exists("query", $l_p_url))
      $l_p_url["query"] = "";

    if (!array_key_exists("fragment",$l_p_url))
      $l_p_url["fragment"] = "";

    $l_s = str_replace(array($l_p_url["query"], $l_p_url["fragment"]), "", $p_url);
    // $l_s = str_replace($l_p_url["path"], $l_p_url["path"] . "/" . $a_str, $l_s);
    $l_s = rtrim($l_s, '/') . "/" . $a_str ;

    return $l_s;
  }
}
