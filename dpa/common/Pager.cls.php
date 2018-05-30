<?php
/**
 * Pager.cls.php
 * @author chengfeng
 * php4,php5 兼容
 */
class Pager {
  /**
     * url
     * @access private
     * @var string
     */
  var $url = null;
  /**
     * 条目偏移量
     * @access private
     * @var integer
     */
  var $offset = 0;
  /**
     * 条目总数
     * @access private
     * @var integer
     */
  var $itemSum;
  /**
     * 每页的条目数
     * @access private
     * @var integer
     */
  var $pageSize;
  /**
     * 总页数
     * @access private
     * @var integer
     */
  var $pageCount;
  /**
     * 当前页号码
     * @access private
     * @var integer
     */
  var $currentPageNumber;
  /**
     * url中的页码标志符
     * @access private
     * @var string
     */
  var $flag = 'p';
  /**
     * separator : url中的串联记号 (?|&)
     * @access private
     * @var string
     */
  var $separator = '?';
  /**
     * 翻页栏中显示页号的数量
     * @access private
     * @var integer
     */
  var $barNumberCount = 10;
  /**
     * 第一页和最后一页紧邻几个页码+省略 必须大于0
     */
  var $first_last_limit = 1;
  /**
     * 省略号
     */
  var $shenglue = "...";
  /**
     * 分页栏页面总数
     */
  var $barPageCount;
  /**
     * 当前的分页栏号
     */
  var $barCurrentPage;
  /**
   * 最终的翻页模板是否附带总数：true表示带上;false:表示不附带
   * 为了同以前的兼容，默认是要带上
   *
   * @var bool
   */
  var $l_if_end = false;

  var $l_static = array();

  /**
   * Constructor
   * @access public
   * @param string $url
   * @param integer $itemSum 条目总数
   * @param integer $pageSize 每页的条目数
   * @param integer $currentPageNumber 当前页号码
   * @param string $flag 放置在url中的翻页标识
   */
  function __construct($url, $itemSum, $pageSize = 10, $currentPageNumber = 1, $flag="p", $a_static=array(), $a_if_end=true){
    if (!empty($a_static)) {
      $this->l_static = $a_static;
      $this->l_extt   = substr(basename($url), strrpos(basename($url),".") );  // 后缀
    }
    $this->flag = $flag;
    if (false!==strpos($url,"?")) $this->separator = '&';
    $this->url = $url;
    $this->currentPageNumber = max(1, $currentPageNumber);
    $this->itemSum = $itemSum;
    $this->pageSize = $pageSize;
    $this->pageCount = ceil($itemSum/$pageSize);
    if($this->currentPageNumber > $this->pageCount){
      $this->currentPageNumber = $this->pageCount;
    }
    $this->offset = $pageSize *  ($this->currentPageNumber - 1);
    if (!empty($a_if_end)) $this->l_if_end = true;
    else $this->l_if_end = false;

    // 分页栏的总页数 : ceil($this->pageCount/$this->barNumberCount)
    // 分页栏的当前页数 ： ceil($this->currentPageNumber/$this->barNumberCount)
    $this->barPageCount = ceil($this->pageCount/$this->barNumberCount);
    $this->barCurrentPage = ceil($this->currentPageNumber/$this->barNumberCount);
  }
  // 兼容php4
  function Pager($url, $itemSum, $pageSize = 10, $currentPageNumber = 1, $flag="p", $a_static=array(), $a_if_end=true){
    $this->__construct($url, $itemSum, $pageSize, $currentPageNumber, $flag, $a_static, $a_if_end);
  }
  /**
     * 返回每页的条目数量
     * @access public
     * @return integer
     */
  function getPageSize(){
    return $this->pageSize;
  }
  /**
     * 返回条目的偏移量
     * @access public
     * @return integer
     */
  function getOffset(){
    return $this->offset;
  }

  //
  function getpreceding($preceding){
    // preceding
    // 如果当前页不等于1，这个链接生效
    if ($this->currentPageNumber > 1){
      return '<a href="'.$this->buildurl(array($this->flag=>($this->currentPageNumber - 1))).'">'.$preceding.'</a>';
    }else{
      return '<span class="pagedisabled">'.$preceding.'</span>';
    }
  }

  //
  function getfollowing($following){
    // following
    // 如果当前页不是最后一页，这个链接生效
    if ($this->currentPageNumber < $this->pageCount){
      return '<a href="'.$this->buildurl(array($this->flag=>($this->currentPageNumber + 1))).'">'.$following.'</a>';
    }else{
      return '<span class="pagedisabled">'.$following.'</span>';
    }
  }

  function getfirstpart($startNumber){
    // first
    $first = "";
    if ($startNumber > ($this->first_last_limit+1)) {
      for ($i=1;$i<=$this->first_last_limit;$i++){
        $first .= $this->buildi($i);
      }
      $first .= $this->shenglue;
    }else {
      for ($i=1;$i<$startNumber;$i++){
        $first .= $this->buildi($i);
      }
    }

    return $first;
  }

  function getlastpart($endNumber){
    // last
    // 先分出最后的barNumberCount个，如果当前页在此bar中，则特殊处理连起来
    // if ($this->currentPageNumber>($this->pageCount-barNumberCount)) { $last = ""; }
    $last = "";
    if ($endNumber < ($this->pageCount-$this->first_last_limit)) {
      $last = $this->shenglue;
      for ($i=$this->pageCount-$this->first_last_limit+1;$i<=$this->pageCount;$i++){
        $last .= $this->buildi($i);
      }
    }else {
      for ($i=$endNumber+1;$i<=$this->pageCount;$i++){
        $last .= $this->buildi($i);
      }
    }

    return $last;
  }

  function getcenter($startNumber, $endNumber){
    // center bar
    $numberLine = ''; // 页码数字链接
    for ($i=$startNumber;$i<=$endNumber;$i++){
      if ($i != $this->currentPageNumber){
        $numberLine .= $this->buildi($i);
      } else {
        $numberLine .= '<span class="pagecurr">'.$i.'</span>';
      }
    }

    return $numberLine;
  }
  /**
   * 取翻页栏的html页面代码
   * @access public
   * @return string
   */
  function getBar() {
    $template = '';
    if ($this->itemSum == 0) return $template;

    $preceding = '上一页';
    $following = '下一页';
    $preceding = $this->getpreceding($preceding);
    $following = $this->getfollowing($following);

    // 先判断特殊的，即当当前页在最后的 barNumberCount 中时，特殊处理
    /*if($this->currentPageNumber >= ($this->pageCount - $this->barNumberCount +1) && $this->currentPageNumber <= $this->pageCount){
      //
      $startNumber = $this->pageCount - $this->barNumberCount +1;
      $startNumber = $startNumber>0?$startNumber:1;
      $endNumber = $this->pageCount;
    } else {
      // bar's start and end Number

      $startNumber = ($this->barCurrentPage-1) * $this->barNumberCount + 1;
      // page numbers
      if ( $this->barCurrentPage < $this->barPageCount ){
        // 有 $this->barNumberCount 个号码
        $endNumber = $startNumber+$this->barNumberCount-1;
      } else {
        // 有 $this->pageCount-ceil($this->currentPageNumber/$this->barNumberCount) 个页号码
        $endNumber = $this->pageCount;
      }
    }*/
    $startNumber = $this->currentPageNumber-4;
    $startNumber = $startNumber<1?1:$startNumber;
    $endNumber = $this->currentPageNumber+4;
    $endNumber = $endNumber>$this->pageCount?$this->pageCount:$endNumber;

    $first = $this->getfirstpart($startNumber);
    $last  = $this->getlastpart($endNumber);
    $numberLine = $this->getcenter($startNumber, $endNumber);

    // 模板代码
    if ($this->l_if_end) {
      $template = $preceding.$first.$numberLine.$last.$following;
    }else {
      $template = $preceding.$first.$numberLine.      $following;
    }

    return $template;
  }

  function buildi($i){
    return '<a href="'.$this->buildurl(array($this->flag=>$i)).'">'.$i.'</a>';
  }

  function buildurl($add_arr=""){
    if (is_array($add_arr) && !empty($add_arr)) {
      if (is_array($this->l_static) && !empty($this->l_static)) {
        // 第一页要做特殊处理，即不用数字
        if (1==$add_arr[$this->flag]) {
          $url = $this->url;
        }else {
        // l_static 用于指定静态参数的顺序 array('p','id','name')
        $l_str = '';
        foreach ($this->l_static as $l_k){
          if (array_key_exists($l_k,$add_arr)) {
            $l_str .= '_'.$add_arr[$l_k];
          }else {
            $l_str .= '_';
          }
        }
        $url = str_replace($this->l_extt, $l_str . $this->l_extt, $this->url);
        }
      }else {
        $new_arr = array();
        // 先分解原来url中的各种参数,并注入到数组中,
        $pos = strpos($this->url,"?");
        if (false!==$pos) {
          $url_pre = substr($this->url,0,$pos);
          parse_str(substr($this->url,$pos+1),$__arr);
          foreach ($__arr as $key=>$val){
            $new_arr[$key] = $val;
          }
        }else {
          $url_pre = $this->url;
        }

        // 合并数组然后重新拼装新url
        $new_arr = array_merge($new_arr,$add_arr);
        $url = $url_pre."?".http_build_query($new_arr);
      }
    }else {
      $url = $this->url;
    }

    return $url;
  }
}
/*
// 1. 没有任何样式的
//$page = new Pager('',97,1,$_GET['p']);
//echo $pagebar = $page->getBar();

// 样式更好的翻页条
$pagebar_css = '
<style type="text/css">
.pages {height:30px; text-align:center; line-height:30px; margin:10px 0 5px;}
.pages span, .pages a {margin-right:4px; padding:2px 6px;}
.pages span {border:1px solid #D4D9D3; color:#979797;}
.pages a {border:1px solid #9AAFE4;}
.pages a:link {color:#3568B9; text-decoration:none;}
.pages a:visited {color:#3568B9; text-decoration:none;}
.pages a:hover {color:#000; text-decoration:none; border:1px solid #2E6AB1;}
.pages a:active {color:#000; text-decoration:none; border:1px solid #2E6AB1;}
.pages a.now:link, .pages a.now:visited, .pages a.now:hover, .pages a.now:active {text-decoration:none; background:#2C6CAC; border:1px solid #2C6CAC; color:#fff; cursor:default;}
</style>
';

$l_get = $_GET;
unset($l_get['p']);
$l_file = '';
if (!empty($l_get)) {
  $l_file = "?".http_build_query($l_get);
}

// 2. 有页码总数的
$page = new Pager($l_file,97,1,$_GET['p']);
$pagebar = $pagebar_css.'<div class="pages">';
$pagebar .= $page->getBar();
$pagebar .= '</div>';
echo $pagebar;

// 3. 没有页码总数的
$page = new Pager('index.shtml',97,1,$_GET['p'],'p',array('p'),'');
$pagebar = $pagebar_css.'<div class="pages">';
$pagebar .= $page->getBar();
$pagebar .= '</div>';
echo $pagebar;
*/