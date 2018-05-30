<?php
require_once("common/Files.cls.php");

/**
 * 发布文档所用，可以是静态html、css、js，也可以是php文档
 * 一般先生成本地文件，然后rsync或者ftp到相应服务器上
 *
 */
class Publish
{
  function __construct(){

  }

  function Publish(){
    // 兼容php4
    $this->__construct($_arr, $options);
  }

  // 替换掉url中的变量
  function parseUrl() {

  }

  // 确定本地路径
  function getLocalPath(){

  }

  // 为了兼容先前的，因此$a_field有一个默认值
  function getUrl(&$arr,&$actionMap,&$actionError,&$request,&$response,$a_field='url_1'){
    // 首先替换掉变量值，因为变量值都是当前表的字段。
    if (isset($response["arithmetic"][$a_field]["pa_val"])) $l_url = $response["arithmetic"][$a_field]["pa_val"];
    else if (isset($arr["f_info"][$a_field][Parse_Arithmetic::getArithmetic_Result_str()]["value"])) $l_url = $arr["f_info"][$a_field][Parse_Arithmetic::getArithmetic_Result_str()]["value"];
    else $l_url='';

    // 替换掉${中英文变量}为相应的数值
    $l_url = str_replace(array_keys($arr['_STR_REPLACE_fields_VAL']),$arr['_STR_REPLACE_fields_VAL'],$l_url);
    if (false===strpos($referer,"://")) {
      $l_url = str_replace("//","/",$l_url);  // 将url中的//替换为/
    }
    // 是否需要将新的 $a_field字段 数据覆盖 _STR_REPLACE_fields_VAL 中去呢，后面好像没有用到，暂时不用覆盖回去。

    return $l_url;
  }

  function getRootPath(&$arr,&$actionMap,&$actionError,&$request,&$response){
    $l_root = "";
    if ( isset($arr["p_def"]["bendi_uri"]) ) {
      // 从项目定义处获取本地uri，不同的表发布的地址也许不一样，因此还需要在表定义字段中添加url，并进行相应的替换动作，哪个存在用哪个，都存在用表定义
      if (!empty($arr["p_def"]["bendi_uri"])) $l_root = $arr["p_def"]["bendi_uri"];
      if (isset($arr["t_def"]["bendi_uri"]) && !empty($arr["t_def"]["bendi_uri"])) $l_root = $arr["t_def"]["bendi_uri"];
      // 如果有算法，则优先使用算法中的结果, 以后完善之????
    }
    if (""==trim($l_root)){  // 保证根目录不为空
      if ('WIN' === strtoupper(substr(PHP_OS, 0, 3)))$l_root = "D:/www/ni9ni/htdocs/e.ni9ni.com/" ;// /data0/htdocs  /www /admin.ni9ni.com
      else $l_root = "/data0/htdocs/e.ni9ni.com/" ;
    }

    return $l_root;
  }

  // 生成文档，并同步到相应地方去
  // 也有可能是删除文件, 同步出去
  // $if_delete 参数表示‘删除’处理
  function toPublishing(&$arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie,$data_arr,$a_tmpl_design,$l_url='',$if_delete=false){
    // 1 从数据库中获取模板、url和本地地址等，如果某项为空，则不予生成，并显示错误信息。
    // 依据nginx.conf配置中关于该web前端项目的项目对应的本地目录
    // 配置信息以后放到数据库中去，当前先写成现成的文件。
    // 确定一下用于存放发布成功以后的字段位置问题，如果没有存放的字段，则拒绝发布
    $l_field = "";
    if (array_key_exists("default_field", $a_tmpl_design)){
      // 该字段还还必须是真实存在的字段，否则不认
      if (array_key_exists($a_tmpl_design["default_field"], $arr["f_info"])){
        $l_field = $a_tmpl_design["default_field"];
      }
    }
    if (""==$l_field) return "";  // 字段不存在则退出发布工作

    // 1) url地址替换, 如果外部指定了就用外部地址
    if (""==$l_url) $l_url = Publish::getUrl($arr,$actionMap,$actionError,$request,$response,$l_field);
    if (""==$l_url) return "";  // $l_url 一定不为空，否则不能进行发布

    // 2) 通过url对应到本地目录和文件,在数据库或者用配置文件进行指定一个
    $l_root = Publish::getRootPath($arr,$actionMap,$actionError,$request,$response);

    $l_local_path = $l_root . $l_url;  // 生成到本地文件

    // 如果单篇文章有分页，分页标记：<!--[@@[Page]@@]-->, 需要进行特殊处理。
    $l_files = new Files();
    $l_sep = '<!--[@@[Page]@@]-->';
    if (isset($arr['_STR_REPLACE_fields_VAL']['${正文}']) && false!==strpos($arr['_STR_REPLACE_fields_VAL']['${正文}'],$l_sep)) {
      $l_content_all = $arr['_STR_REPLACE_fields_VAL']['${正文}'];
      require_once("common/Pager.cls.php");
        // 分解成多块，并且利用同样的模板将页面分解成多块
      $l_tmp_arr = explode($l_sep, $l_content_all);
      // 进行分页处理
      Publish::toPaging($arr, $l_files, $l_tmp_arr, $a_tmpl_design, $l_local_path, $if_delete);

      // 内容恢复为完整的内容
      $arr['_STR_REPLACE_fields_VAL']['${正文}'] = $l_content_all;
    }else {
      Publish::toOne($arr, $l_files, $a_tmpl_design, $l_local_path, $if_delete);
    }

    // 用替换好了的l_url更新文档表中的 url_1,url_2,url_3...字段, 只有在发布成功以后才能使用
    // 如果新的同旧的不一样，则需要更新之。一样则无需更新。另：注意去掉可能的前缀。
    if (isset($arr["f_data"][$l_field]) && $arr["f_data"][$l_field]!=$l_url) {
      // 当修改数据的时候，数据库中原有的如果同新的不一样需要进行更新，否则无需更新此字段
      if (isset($arr["dbW"])) $dbW = $arr["dbW"];
      else $dbW = new DBW($arr["p_def"]);
      $dbW->table_name = $arr["t_def"]["name_eng"];
      $conditon = " id = ".$arr["f_data"]["id"]." ";
      $l_data_arr = array($l_field=>$l_url);
      $l_rlt = $dbW->updateOne($l_data_arr, $conditon);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        // 更新失败后
        $response['html_content'] = var_export($l_err, true). " 更新数据发生错误,sql: ". $dbW->getSQL();
        return null;
      }
      $dbW = null;unset($dbW);
    }

  }

  function toPaging(&$arr, &$l_files, $l_tmp_arr, $a_tmpl_design, $l_local_path, $if_delete=false){
    if (!empty($l_tmp_arr)) {
      $l_flag = 'p';  // 分页flag

      // 总数
      $l_itemSum = count($l_tmp_arr);
      // 页面条目数, 必须是1
      $l_pageSize = 1;

      // 逐一发布
      foreach ($l_tmp_arr as $l_k => $l_v){
        // 内容中要有分页代码
        $l_p = $l_k+1;  // 分页码，即第几页
        $l_p = ($l_p>ceil($l_itemSum/$l_pageSize))?ceil($l_itemSum/$l_pageSize):$l_p;
        $l_p = ($l_p<1)?1:$l_p;

        $l_filename = basename($l_local_path);  //

        if (false!==strpos($a_tmpl_design["default_html"], '.pages span {')) {
          $pagebar_css = '';
        }else {
        // 样式更好的翻页条. css
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
        }
        $page = new Pager($l_filename,$l_itemSum,$l_pageSize,$l_p,$l_flag,array($l_flag),'');
        $pagebar = $pagebar_css. '<div class="pages">'. $page->getBar() .'</div>';

        // 内容替换
        $arr['_STR_REPLACE_fields_VAL']['${正文}'] = $l_v . $pagebar;  // 作为模板中正文内容
        // 文件路径修改 $l_local_path 不能为空，空的时候强制退出，以后完善
        // 当前的文件名 1则不需要, 从_2开始
        $l_curr = $page->currentPageNumber;
        if ($l_curr>1) {
          $l_extt = substr( $l_filename, strrpos($l_filename,".") );
          $l_filename = str_replace($l_extt, "_".$l_curr . $l_extt, $l_filename);
        }

        Publish::toOne($arr, $l_files, $a_tmpl_design, dirname($l_local_path).'/'.$l_filename, $if_delete);
      }
    }
  }

    /*print_r($arr);
    print_r($response);
    print_r($request);
    print_r($data_arr);
    print_r($a_tmpl_design);

     $arr
        f_data           3735  [aups_f56]
      dbw           3952,3984  [last_query],[sql]
      _STR_REPLACE_fields_VAL 4221  [${aups_f56}]
      _STR_REPLACE_fields_VAL 4278  [${正文}]
      _STR_REPLACE_fields_VAL 4323  [${aups_f40}]
      _STR_REPLACE_fields_VAL 4364  [${新版正文显示}]

  $response [arithmetic]
          [aups_f40][pa_val]      4695
  $request
          [aups_f56]        4788
  $data_arr
          [aups_f56]        4866

    */

  //
  function toOne(&$arr, &$l_files, $a_tmpl_design, $l_local_path, $if_delete=false){
    // 如果url里面含有参数，则生成的静态文件不能包含参数 /data0/htdocs/www/zhanhui/20160421/1339661.shtml?id=45
    // 需要用 $l_arr = parse_url($str); 解析之后 用 $l_arr['path']部分
    // 这里简单地用字符串查找替换, 这是windows和linux的区别，linux上文件名可以有问号等特殊字符
    if (false !== strpos($l_local_path, '?'))
        $l_local_path = substr($l_local_path, 0, strpos($l_local_path, '?'));

    if ($if_delete) {
      if (file_exists($l_local_path)) {
        unlink($l_local_path);
      }
    }else {
      // 2 用现成的字段模板中的变量，然后将内容填充到文件中去
      $l_html = str_replace(array_keys($arr['_STR_REPLACE_fields_VAL']),$arr['_STR_REPLACE_fields_VAL'],$a_tmpl_design["default_html"]);// 替换中文时间和部分变量
      // 写到本地文件中去
      $l_files->overwriteContent($l_html,$l_local_path);
    }

    // 3 将文件rsync或ftp到前端等机器上去, 每个文件都要进行同步到前端, 可以单独发布也可放在外面统一发布过个分页
    //DIRECTORY_SEPARATOR;
    //echo $dbR->getSQL();exit;
  }

  // 获取模板，并填充内容，返回静态文件
  function getAndFillTmpl(){

  }

  //
  function __destruct(){}
}

