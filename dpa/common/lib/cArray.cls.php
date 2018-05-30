<?php
/**
 * 兼容PHP4的代码
 *
 */
class cArray
{
  /**
   * 索引的二维数组转变成按照某个字段唯一标识的二维数组
   * 由getFieldArr而来
   * @param array $field_arr 必须是二维数组
   * @param string $ziduan   存在的唯一的key
   * @return array 二维数组或一维
   */
  function Index2KeyArr($field_arr, $a_val=array("key"=>"Field", "value"=>"Field")){
    $arr = array();
    if(!empty($field_arr))
    foreach ($field_arr as $_f){
      if (is_array($_f)){
        if (!isset($a_val["key"]) || empty($a_val["key"])) {
          // 则为数字索引
          if (is_array($a_val["value"])) {
            //
            $l_val = $_f;
          }else if (is_string($a_val["value"])) {
            $l_val = $_f[$a_val["value"]];
          }
          $arr[] = $l_val;
        }else {
          if (array_key_exists($a_val["key"], $_f)) {
            $l_key = $_f[$a_val["key"]];
            if (is_array($a_val["value"])) {
              //
              $l_val = $_f;
            }else if (is_string($a_val["value"])) {
              $l_val = $_f[$a_val["value"]];
            }
            $arr[$l_key] = $l_val;
          }
        }
      }
    }
    return $arr;
  }

  // 将对象转换为多维数组
  public static function ObjectToArray($object) {
      if (is_object($object))
          $object = get_object_vars($object);
      else if (is_array($object))
          return array_map(array(__CLASS__, __FUNCTION__), $object);
      return $object;
  }

  // 将数组转换为对象
  public static function ArrayToObject($arr) {
      if (!is_array($arr))
          return $arr;
     $arr = json_encode($arr); // 转json
     $arr = json_decode($arr); // 转成对象
     return $arr;
  }

  /**
   * 从数组中获取指定字段的一段
   * 例如,从request数组中只需要如下字段的数据:
   *     $l_allow_ziduan = array('news_p_id','news_t_id','news_id','parent_id','ip','l');
   *
   * @param array $a_request 大数组
   * @param array $a_ziduan_arr 需要的字段
   * @return array
   */
  function array__slice($a_request, $a_ziduan_arr=array("id")){
    $l_new_arr = array();

    if (!empty($a_ziduan_arr)) {
      foreach ($a_ziduan_arr as $l_ziduan){
        if (array_key_exists($l_ziduan, $a_request)) {
          $l_new_arr[$l_ziduan] = $a_request[$l_ziduan];
        }
      }
    }

    return $l_new_arr;
  }

  function get_opt($a_argv, $a_para_short='i:d:t:',$a_para_long=array()){
    // PHP5.3以上直接有现成的方法
    if (function_exists('getopt'))
      return getopt($a_para_short, $a_para_long);

    // 获取参数列表
    require_once 'Console/Getopt.php';
    $_options = Console_Getopt::getopt($a_argv, $a_para_short, $a_para_long);
    $_o = array();
    if (!PEAR::isError($_options)) {
      foreach ($_options[0] as $l_v){
        $_o[$l_v[0]] = $l_v[1];
      }
    }
    return $_o;
  }

  // 将类似1-4,1,6,8 这样的多id类型转换分解为独立的单个id列表数组
  function getIdsByStr($a_str){
    $l_arr = array();  // 结果数组

    // 如果是单一的数字，直接返回, 后面的处理将非数字的也返回到数组
    if (is_numeric($a_str)) {
      $l_arr[] = $a_str;
      return $l_arr;
    }

    // 如果有逗号，则分成多项
    $l_sep = ",";
    if (false!==strpos($a_str, $l_sep)) {
      $l_1tmp = explode($l_sep,$a_str);

      // 逐项进行判断
      foreach ($l_1tmp as $l_1v){
        // 如果有-，则表示一个范围
        $l_arr = array_merge($l_arr, cArray::sepretNumByStr($l_1v));
      }
    }else {
      // 如果仅仅只包含有 -
      if (false!==strpos($a_str, "-")) {
        $l_arr = array_merge($l_arr, cArray::sepretNumByStr($a_str));
      }else {
        $l_arr[] = $a_str;
      }
    }

    return $l_arr;
  }

  // 带索引的插入数据头部, 直接对数组用加法就能保持索引进行合并数组
  function array_unshift_assoc(&$arr, $key, $val) {
    return $arr = array($key => $val) + $arr;
    /*
    // 或者用下面这些代码进行反转也可
    $arr = array_reverse($arr, true);
    $arr[$key] = $val;
    return array_reverse($arr, true);
    */
  }

  // 将类似1-4的整型数字范围返回到数组中去，前后顺序没有关系, 返回数组
  function sepretNumByStr($a_str){
    $l_arr = array();

    $l_sep = "-";
    if (false!==strpos($a_str,$l_sep)) {
      $l_2tmp = explode($l_sep,$a_str);
      $l_max = max($l_2tmp) + 0;  // 确保为数字
      $l_min = min($l_2tmp) + 0;
      for ($i=$l_min;$i<=$l_max;$i++){
        $l_arr[] = $i;
      }
    }else {
      // if (is_numeric($a_str))
      $l_arr[] = $a_str;
    }

    return $l_arr;
  }

  function getTimezone(){
    return array(
      "12"=>"东12区",
      "11"=>"东11区",
      "10"=>"东10区",
      "9"=>"东9区",
      "8"=>"东8区",
      "7"=>"东7区",
      "6"=>"东6区",
      "5"=>"东5区",
      "4"=>"东4区",
      "3"=>"东3区",
      "2"=>"东2区",
      "1"=>"东1区",
      "0"=>"GTM",
      "-1"=>"西1区",
      "-2"=>"西2区",
      "-3"=>"西3区",
      "-4"=>"西4区",
      "-5"=>"西5区",
      "-6"=>"西6区",
      "-7"=>"西7区",
      "-8"=>"西8区",
      "-9"=>"西9区",
      "-10"=>"西10区",
      "-11"=>"西11区",
      "-12"=>"西12区"
    );
  }
  function getTTypeCNnameArr(){
    return GSPS_CONS::getTTypeCNnameArr();
  }

  function getTempTypeCNnameArr(){
    return GSPS_CONS::getTempTypeCNnameArr();
  }

  // allow=post1,post2这样的字符串转成array("allow"=>"post1,post2")的数组
  // 支持多行解析
  function str2keyvalue($a_str,$a_sep="=",$a_2wei=false){
    $l_arr = array();
    $l_tmp = explode("\n", $a_str);
    if (!empty($l_tmp)) {
      // 逐行检查是否特征字符串
      foreach ($l_tmp as $l_num => $l_line){
        $l_ll = trim($l_line);
        $l_pos = strpos($l_line,$a_sep);
        if (false!==$l_pos) {
          $l_k = trim( substr($l_line,0,$l_pos) );
          $l_v = trim( substr($l_line,$l_pos+1) );
          if ($a_2wei) {
            $l_arr[$l_num] = array("key"=>$l_k,"value"=>$l_v);
          }else {
            $l_arr[$l_k] = $l_v;
          }
        }
      }
    }
    return $l_arr;
  }

  // 在数字键名的数组前或后追加另一个数组的数据合并成新数组。
  // 将$a_arr2放到$a_arr1的ahead 还是 after
  function array__unshift(&$a_arr1, $a_arr2, $a_pos="ahead"){

    if (is_array($a_arr1) && is_array($a_arr2)) {
      if ("ahead"==$a_pos) {
        if ( !empty($a_arr2) ) {
          // 需要将数组顺序颠倒一下，不然前几个顺序跟2颠倒了，不过整体还是在前面
          foreach ( array_reverse($a_arr2) as $l_v){
            array_unshift($a_arr1,$l_v);
          }
        }
      }else if ("after"==$a_pos) {
        if ( !empty($a_arr2) ){
          foreach ($a_arr2 as $l_v){
            $a_arr1[] = $l_v;
          }
        }
      }
    }

    return count($a_arr1);
  }

  /**
   * 见cString里面的说明
   *
   * @param array $a_array_combine
   * @param string $a_str
   * @return array
   */
  function str__replace($a_array_combine,$a_str){
    if (is_array($a_array_combine) && !empty($a_array_combine)) {
      if (array_values($a_array_combine) == $a_array_combine) {
        // 说明是数字索引，则替换和被替换的一样
        $a_array_combine = array_combine($a_array_combine,$a_array_combine);
      }

      $letters = array_keys($a_array_combine);
      $l_str = implode("|",$letters);
      $l_str = "/($l_str)/i";
      if( preg_match_all($l_str,trim($a_str),$l_matches,PREG_SET_ORDER) ) {
        $l_arr = preg_split($l_str, $a_str);  // 正则分割成多块
        //
        foreach ($l_matches as $l_k => $l_v){
          $l_arr[$l_k+1] = $a_array_combine[$l_v[1]] . $l_arr[$l_k+1]; // 字符串补全，还原
        }
      }
    }else {
      $l_arr = array($a_str);
    }
    return $l_arr;
  }

  /**
   * 读取一个cookie文件，并解析成一个cookie数组
   * 自动判断是否过期等
   *
   * 结果如下：
Array
(
    [PHPSESSID] => 1ffa4b66598a86e039b826a2d6e0f962
    [uid] => 2
    [email] => admin@test.com
    [sid] => bdd05ecd21622566ece291c35f4dbfe42
)
   *
   *
uid
2
ni9ni.com/
1536
4207192704
30258751
2184331504
30185326
*
   *
cookie格式：
第1行为 Cookie 名称
第2行是 Cookie 的值
第3行是 Cookie 所属站点的地址
第4行是个标记值
第5行为超时时间的低位(Cardinal/DWORD)
第6行为超时时间的高位
第7行为创建时间的低位
第8行为创建时间的高位
第9行固定为 * ，表示一节的结束
需要注意的是这里使用的时间并非 Delphi 的 TDateTime，而是 FILETIME(D里为对应的TFileTime)
一个文件可能包含有多个节，按上面的格式循环即可
   */
  function parse_cookiefile($cookie_file){
    //
    $l_arr = array();
    if (file_exists($cookie_file)) {
      $l_tmp = file($cookie_file);

      // 时间也域名判断以后进行????
      foreach ($l_tmp as $k=>$v){
        if (0==$k%9) {
          $l_k = trim($v);
        }
        if (1==$k%9) {
          $l_arr[$l_k] = trim($v);
        }
      }
    }

    return $l_arr;
  }

  // 剔除新数组中同旧数组具有相同数值的单元。
  function delSameValue(&$a_new, $a_old){
    if (is_array($a_old)) {
      foreach ($a_new as $l_k => $l_v){
        // 剔除掉没有数据修改的字段
        if (array_key_exists($l_k, $a_old) && $l_v===$a_old[$l_k]) {
          unset($a_new[$l_k]);
        }else if(""==$a_old[$l_k] && ""==$l_v){
          // 包含了四种情况,即本系统对外不严格区分null和'',null和''被认为是相同的
          // 对于可以为空的字段来说, null和''被认为是相同的，旧数据为null,则此字段一定不为空
          // 当然能带上字段的非空属性的话将更严密????
          unset($a_new[$l_k]);
        }
      }
    }
    return $a_new;
  }

  //
  function array__merge($a_1, $a_2){
    $rlt = array();
    if (is_array($a_1) && is_array($a_2)) {
      $rlt = array_merge($a_1, $a_2);
    }else if (is_array($a_1)) {
      $rlt = $a_1;
    }else if (is_array($a_2)) {
      $rlt = $a_2;
    }

    return $rlt;
  }
  // 带索引的合并数组，不能用array_merge（因数字索引会重新从0开始索引）
  function array_merge_assoc($arr, $ahead_arr) {
    return $ahead_arr + $arr;
  }

  // 使用一个字符串分割另一个字符串. 同时还要填充分割字符串, 返回一个数组, 行为类似explode
  function explode_str2arr($a_separator,$a_str,$a_pos="after"){
    $l_rlt = array();
    if (false!==strpos($a_str,$a_separator)) {
      $l_tmp = explode($a_separator,$a_str);

      $l_total = count($l_tmp);  // 总个数
      $l_k = 0;
      foreach ($l_tmp as $l_val){
        $l_str = $l_val;
        if ("ahead"==$a_pos) {
          // 第一项以后才能添加在前面
          if ($l_k>0) $l_str = $a_separator.$l_val;
        } else {
          // 最后一项后面不需要加
          if ( $l_k < $l_total-1 ) $l_str = $l_val.$a_separator;
        }
        $l_rlt[] = $l_str;

        $l_k++;
      }

    }else {
      $l_rlt[] = $a_str;
    }

    return $l_rlt;
  }

  // TODO or call_user_func_array('iconv2gb2312', $fn, $v)
  // 递归地将数组或对象的节点使用用户函数,例如字符编码转换
  function array_map_recursive($fn, $arr, $with_key = false) {
    //$rarr = array();
    foreach ($arr as $k => $v) {
      if ($with_key) {
        unset($arr[$k]); // 会被重新创建key,value，而key可能是新的key，所以unset
        $k = $fn($k);
      }
      if (is_object($arr)) {
        if (is_array($v) || is_object($v))
          $arr->$k = cArray::array_map_recursive($fn, $v);
        else
          $arr->$k = $fn($v);
      } else {
        if (is_array($v) || is_object($v))
          $arr[$k] = cArray::array_map_recursive($fn, $v);
        else
          $arr[$k] = $fn($v);
      }
    }
    return $arr;
  }
}

//
class GSPS_CONS
{
  // 模板类型
  function getTTypeCNnameArr(){
    $arr = array(
      "00"=>"空白模板",
      "01"=>"正文模板",
      "02"=>"索引模板",
      //
      "21"=>"专题正文",
      "22"=>"专题栏目",
      "23"=>"专题首页",
      //
      "31"=>"栏目正文",
      "32"=>"子栏目",
      "33"=>"栏目首页"
    );

    return $arr;
  }

  // 模板域类型
  function getTempTypeCNnameArr(){
    $arr = array(
      "Form::TextField"=>array("name_cn"=>"单行文本框","if_into_db"=>1),
      "Form::Password"=>array("name_cn"=>"口令框","if_into_db"=>1),
      "Form::TextArea"=>array("name_cn"=>"多行文本框","if_into_db"=>1),
      "Form::HTMLEditor"=>array("name_cn"=>"HTML编辑器","if_into_db"=>1),
      //
      "Form::Select"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::DB_Select"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::URL_Select"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::DB_DSN_Select"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      //
      "Form::CheckBoxGroup"=>array("name_cn"=>"检查框","if_into_db"=>1),
      "Form::DB_CheckBoxGroup"=>array("name_cn"=>"检查框","if_into_db"=>1),
      "Form::URL_CheckBoxGroup"=>array("name_cn"=>"检查框","if_into_db"=>1),
      "Form::DB_DSN_CheckBoxGroup"=>array("name_cn"=>"检查框","if_into_db"=>1),
      // 选项框
      "Form::RadioGroup"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::DB_RadioGroup"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::URL_RadioGroup"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      "Form::DB_DSN_RadioGroup"=>array("name_cn"=>"下拉列表框","if_into_db"=>1),
      // 文件
      "Form::ImageFile"=>array("name_cn"=>"图片文件","if_into_db"=>1),
      "Form::AudioFile"=>array("name_cn"=>"音频文件","if_into_db"=>1),
      "Form::VedioFile"=>array("name_cn"=>"视频文件","if_into_db"=>1),
      "Form::File"=>array("name_cn"=>"其他文件","if_into_db"=>1),
      //
      "Form::CGICall"=>array("name_cn"=>"CGI表单接口","if_into_db"=>1),
      "Form::CrossPublish"=>array("name_cn"=>"跨项目发布","if_into_db"=>1),
      // 自动获取全局宏
      "Form::CodeResult"=>array("name_cn"=>"自动获取全局宏","if_into_db"=>1),
      //
      "Application::CodeResult"=>array("name_cn"=>"万能的非中文算法","if_into_db"=>0),
      "Application::SQLResult"=>array("name_cn"=>"SQLResult","if_into_db"=>0),
      "Application::Ext_SQLResult"=>array("name_cn"=>"SQLResult","if_into_db"=>0),
      "Application::PostInPage"=>array("name_cn"=>"相关发布","if_into_db"=>0),
      "Application::CrossPublish"=>array("name_cn"=>"跨项目发布","if_into_db"=>0),
      "Application::AutoAddDoc"=>array("name_cn"=>"自动添加文档","if_into_db"=>0),
      "Application::HTTPCall"=>array("name_cn"=>"HTTP获取器","if_into_db"=>0),
      "Application::ImageZoom"=>array("name_cn"=>"图片缩放","if_into_db"=>0),
      "Application::GotoPage"=>array("name_cn"=>"分页对象","if_into_db"=>0)
    );

    return $arr;
  }
}
