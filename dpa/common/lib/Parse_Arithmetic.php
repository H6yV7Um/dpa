<?php
/**
 * 主要用于添加、修改的表单项执行算法获取相应的select框即取值来源和范围
 *
 */
require_once("common/Log.php");

class Form
{
  /**
   * 自动获取环境变量等数据， 例如 当前用户的用户名，当前系统时间等
   *
   * [expr]
   * <条件表达式，暂未实现>
   * [code]
   * <php code>
   * [html]
   * $_SESSION["user"]["username"]
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function CodeResult(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 获取算法
    $l_ath = trim($a_vals["arithmetic"]);
    if (""!=$l_ath) {
      // 首先将算法解析为一维数组
      $l_arr = Parse_Arithmetic::parse_like_ini_file($l_ath);

      // 将code部分放到一个function中去,function名称采用文件名加
      $l_html = Parse_Arithmetic::eval_code($a_arr,$a_key,$a_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_arr);

      // 作为外部附加值进行覆盖, 用于表单项不可修改显示
      $a_arr["default_over"][$a_key] = array("value"=>$l_html,"hidden"=>0);
      //print_r($a_vals);
      $a_arr["f_info"][$a_key][Parse_Arithmetic::getArithmetic_Result_str()] = array("value"=>$l_html);
      // 用获取的数据结果替换字段信息中的 type和length数据，强制进行替换
      //$response["arithmetic"][$a_key]["f_info"] = $a_vals;  // 该字段的信息
      $response["arithmetic"][$a_key]["pa_val"] = $l_html;  // 算法最终结果保留一份
      //if (!is_array($l_html)) Parse_Arithmetic::Int_FillREQUESTValue($a_arr,$response,$a_vals,array($a_key=>$l_html));
    }
  }
  /**
   * 提供是否产生密码工具的算法
   * [PwdGen]
   * md5 或 空(即原始字符不进行处理的)
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function Password(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 主要是将非空字符进行密码工具进行加密处理成新串
    // echo var_export($a_vals, true). "  key:" . $a_key . " FUNC:" . __FUNCTION__ ." FILE:" . __FILE__ ." LINE:" . __LINE__ ." \n";
    // 获取算法
    $l_ath = trim($a_vals["arithmetic"]);
    if (""!=$l_ath) {
      // 首先将算法解析为一维数组
      $l_arr = Parse_Arithmetic::parse_like_ini_file($l_ath);

      if (array_key_exists("PwdGen",$l_arr)){
        //
        $a_arr["f_info"][$a_key][Parse_Arithmetic::getArithmetic_Result_str()] = array("method2val"=>$l_arr["PwdGen"]);

        // 主要是在数据提交以后入库前进行算法执行, 在入库前执行加密算法即可
        //$response["arithmetic"][$a_key]["f_info"] = $a_vals;      // 该字段的信息
        $response["arithmetic"][$a_key]["pa_val"] = $l_arr["PwdGen"];  // 算法最终结果保留一份
      }
    }
  }

  /**
   * Html多行文本框, 算法的特征字段有：
   * [HTMLEditor]
   * Enabled=true
   * [Typeset]
   * Enabled=true
   * [Preview]
   * Enabled=true
   * [Search]
   * Enabled=true
   * [Keyword]
   * Enabled=true
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function TextArea(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 首先解析为二维数组, 没有什么特别的用处
    //$l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"],true);
    //var_dump($l_arr);
    //print_r($a_vals);
    /*
    [Search] => Array
    (
      [Enabled] => true
      [CGI] => http://pub.ni9ni.com:8080/cgi-bin/search/do_rel_result_new.cgi
      [Param] => mode{"按标题"=>"按标题","按全文"=>"按全文","按标题或全文"=>"按标题或全文"}
    )
    // 是否为该文本框提供相关检索功能，默认false
    if (array_key_exists("Search",$l_arr)) {
      //
      if ("true" === $l_arr["Search"]["Enabled"]) {
        // 以后完善之
      }
    }*/
    /*
    $response["arithmetic"][$a_key]["f_info"] = $a_vals;  // 该字段的信息
    $response["arithmetic"][$a_key]["pa_val"] = $a_arr["f_info"][$a_key]["length"];  // 算法最终结果保留一份
    */
    // echo var_export($a_vals, true). "  key:" . $a_key . " FUNC:" . __FUNCTION__ ." FILE:" . __FILE__ ." LINE:" . __LINE__ ." \n";
  }
  // 同TextArea，Preview功能不提供
  function HTMLEditor(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    self::TextArea($a_arr,$a_key,$a_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
  }

  /**
   * 从指定的数据表中获取到数据, 特征字段有：
   * display1,value1
   * display2,value2
   * ……
   * display3,value3
   *
   * 算法说明: display列指定列表框的显示值，value列指定每个列表框的实际值
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function Select(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 类似上面那样, 不过数据不需要另外从数据库获取，而只需直接从数组中
    $l_rlt = cArray::str2keyvalue($a_vals["arithmetic"],",",true);  // 解析成二维数组
    Parse_Arithmetic::fillInselect($a_arr,$a_key,$l_rlt,true);
    //$response["arithmetic"][$a_key]["f_info"] = $a_vals;  // 该字段的信息
    $response["arithmetic"][$a_key]["pa_val"] = $a_arr["f_info"][$a_key]["length"];  // 算法最终结果保留一份
    return $l_rlt;
  }

  /**
   * 从指定的数据表中获取到数据, 特征字段有：
   * [project]
   * name=<项目名称>
   * [polym]
   * name=<项目参考样式>
   * [query]
   * sql=select CONCAT(id,"-",name_cn),id from dpps_ order by id
   * [add_select]
   * 0,0
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function DB_Select(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    $l_err = array();
    $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"]);// 首先解析为一维数组

    // 如果设置了code, 这种方式就好比定义的一个function，html是其返回值。以后将尽量使用此种方式进行
    if (array_key_exists('code',$l_arr)){
      $l_rlt = Parse_Arithmetic::eval_code($a_arr,$a_key,$a_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_arr);
      if (null === $l_rlt) $l_rlt=array();  // 当没有返回数据的时候
    }else {
    $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"],true);// 首先解析为二维数组

    // 先进行项目判断
    //$l_arr["project"] = "name=测试CMS-用于线上";  // 人工指定一个
    if (array_key_exists("project",$l_arr)) {
      // 获取数据库连接信息
      $l_p_info = Parse_SQL::getPinfoByProjCNname($l_arr["project"]);
      $dbR = new DBR($l_p_info);
    }
    if (array_key_exists("polym",$l_arr)){
      // 样式暂时未实现
    }
    // sql语句分析, 需要将sql中文替换成字符串。只替换sql中的表名，
    if (array_key_exists("sql",$l_arr["query"])) {
      if (!isset($dbR)) $dbR = $a_arr["dbR"];  // 保证dbr存在

      // 表名替换、字段替换
      $l_sql = Parse_SQL::ReplaceSQlTblAndFieldname($dbR, $a_arr,$a_vals,$l_arr["query"]["sql"]);
      // sql语句中文替换成英文完成之后进行查询
      $l_rlt = $dbR->query_plan($l_sql,false);
      $l_err = $dbR->errorInfo();

      if (PEAR::isError($l_rlt)) {
        echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
        return ;
      }

      if ($l_err[1]>0){
             // sql有错误，则返回，中止后续执行
             return $l_err;
           }
    }
    }
    if (isset($l_rlt)) {
      // 算法可以自己添加外来的算法
      if (false!==strpos($a_vals["arithmetic"],'[add_select]')) {
        $l_tmp = explode('[add_select]',$a_vals["arithmetic"]);
        $l_tmp = cArray::str2keyvalue(ltrim($l_tmp[1]),",",true);  // 解析成二维数组
        cArray::array__unshift($l_rlt,$l_tmp,"ahead");  // 填充到数组的前端
      }
      // 用获取的数据结果替换字段信息中的 type和length数据，强制进行替换
      Parse_Arithmetic::fillInselect($a_arr,$a_key,$l_rlt,true);
      //$response["arithmetic"][$a_key]["f_info"] = $a_vals;  // 该字段的信息
      $response["arithmetic"][$a_key]["pa_val"] = $a_arr["f_info"][$a_key]["length"];  // 算法最终结果保留一份
    }

    return $l_err;
  }

  /**
   * 1) 该类模板域在添加表单时呈现为文件上传框
   * 2) 该类模板域在编辑表单时提供原始图片值并提供可选的替换功能
   * 3) 该类模板域主要处理文件的上传及图片等的显示等
   *
   * 算法模型：
   * [project]
   * name=<项目名称>
   * [polym]
   * name=<项目参考样式>
   * [query]
   * sql=<查询语句>
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function DB_RadioGroup(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    $l_err = array();
    $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"],true);// 首先解析为二维数组

    // 先进行项目判断
    //$l_arr["project"] = "name=测试CMS-用于线上";  // 人工指定一个
    if (array_key_exists("project",$l_arr)) {
      // 获取数据库连接信息
      $l_p_info = Parse_SQL::getPinfoByProjCNname($l_arr["project"]);
      $dbR = new DBR($l_p_info);
    }
    if (array_key_exists("polym",$l_arr)){
      // 样式暂时未实现
    }
    // sql语句分析, 需要将sql中文替换成字符串。只替换sql中的表名，
    if (array_key_exists("sql",$l_arr["query"])) {
      if (!isset($dbR)) $dbR = $a_arr["dbR"];  // 保证dbr存在

      // 表名替换、字段替换
      $l_sql = Parse_SQL::ReplaceSQlTblAndFieldname($dbR, $a_arr, $a_vals, $l_arr["query"]["sql"]);
      // sql语句中文替换成英文完成之后进行查询
      $l_rlt = $dbR->query_plan($l_sql,false);
      $l_err = $dbR->errorInfo();

      if (PEAR::isError($l_rlt)) {
        echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
      }else {
        // 算法可以自己添加外来的算法
        if (false!==strpos($a_vals["arithmetic"],'[add_select]')) {
          $l_tmp = explode('[add_select]',$a_vals["arithmetic"]);
          $l_tmp = cArray::str2keyvalue(ltrim($l_tmp[1]),",",true);  // 解析成二维数组
          cArray::array__unshift($l_rlt,$l_tmp,"ahead");  // 填充到数组的前端
        }
        // 用获取的数据结果替换字段信息中的 type和length数据，强制进行替换
        Parse_Arithmetic::fillInselect($a_arr,$a_key,$l_rlt,true,"other");
        //$response["arithmetic"][$a_key]["f_info"] = $a_vals;  // 该字段的信息
        $response["arithmetic"][$a_key]["pa_val"] = $a_arr["f_info"][$a_key]["length"];  // 算法最终结果保留一份
      }

      if ($l_err[1]>0){
             // sql有错误，则返回，中止后续执行
             return $l_err;
           }
    }

    return $l_err;
  }

  /**
   * 1) 该类模板域在添加表单时呈现为文件上传框
   * 2) 该类模板域在编辑表单时提供原始图片值并提供可选的替换功能
   * 3) 该类模板域主要处理文件的上传及图片等的显示等
   *
   * 算法模型：
   * [limit]
   * overwrite=true/false
   * maxsize=**(K/M)
   * [html]
   * ...
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function File(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 碰到以后具体再做
    //echo var_export($a_vals, true). "  key:" . $a_key . " FUNC:" . __FUNCTION__ ." FILE:" . __FILE__ ." LINE:" . __LINE__ ." \n";
  }

  /**
   * 该类模板域同Form::File类型相似, 提供了扩展功能如图片缩放等
   *
   * 从指定的数据表中获取到数据, 特征字段有：
   * [limit]
   * overwrite=true/false
   * maxsize=**(K/M)
   * [zoom]
   * geometry=220x420>
   * [html]
   * <a href="${焦点图片URL}"><img src="${焦点图片}"></a>
   *
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function ImageFile(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 在呈现的时候，主要是为了前端js做判断？ 也或者是后端才需要做的事情。所以可以先跳过

    // 获取算法参数
    // $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"],true);  // 解析成二维数组

    //
    //Parse_Arithmetic::fillInselect($a_arr,$a_key,$l_rlt);
    //print_r($l_arr);
    //return $l_arr;
  }
}


/**
 * 用于数据表数据添加、修改、删除影响到数据后的附加动作，
 * 这部分数据不在真实的数据表中。只存在于字段定义表
 *
 */
class Application
{
  /**
   * 纯粹的php代码的执行，就如同写php一样。此处避免使用中文名代替字段，全部必须使用英文，
   * 由于需要英文字段，因此必须是非常熟悉系统的人员来进行。
   * 今后尽量将其他的application的算法全部归到这个方法上
   *
   * [sql]
   * <涉及到的表和字段，用sql的方式进行区分开来，sql并非需要其结果。这样保证了可以用中文代替字段>
   * [code]
   * <php code>
   * [html]
   * $_SESSION["user"]["username"]
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function CodeResult(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    // 获取算法
    $l_ath = trim($a_vals["arithmetic"]);
    if (""!=$l_ath) {
      // 首先将算法解析为一维数组
      $l_arr = Parse_Arithmetic::parse_like_ini_file($l_ath);

      // 1 .先进行一些中文变量数值的替换工作,主要是f_data和request中的数值，因为${中文},
      if (array_key_exists('f_data',$a_arr)) $l_requ = $a_arr['f_data'];
      else $l_requ = &$request;

      // 首先需要进行转义, 并且单双引号需要分隔开来处理
      // 当$l_lanmu = '${所属栏目}' 的内容中有 单引号或双引号的时候，需要转义，否则eval报错
      // 1) 先处理单引号转义 $l_requ 的数据中如果有内容中包括单引号, 需要将哪项提取出，转义后进行替换动作
      $l_data_tmp = array_map('__call_back_addslashesSingleQuote2str', $l_requ);
      $l_f_quote_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_data_tmp,'name_eng','name_cn','__call_back_addSingleQuoteDollarBrace2str');
      $l_f_quote_eng_key = array_map('__call_back_addSingleQuote2str',$l_f_quote_eng_key);
      $l_arr['code'] = str_replace(array_keys($l_f_quote_eng_key),$l_f_quote_eng_key,$l_arr['code']);

      // 2) 同上，将数据中有双引号的提取出进行项目的替换工作。
      $l_data_tmp = array_map('__call_back_addslashesDoubleQuote2str', $l_requ);
      $l_f_quote_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_data_tmp,'name_eng','name_cn','__call_back_addDoubleQuoteDollarBrace2str');
      $l_f_quote_eng_key = array_map('__call_back_addDoubleQuote2str',$l_f_quote_eng_key);
      $l_arr['code'] = str_replace(array_keys($l_f_quote_eng_key),$l_f_quote_eng_key,$l_arr['code']);

      // 3) 单双引号的转义工作完成以后进行, 一般情况的中、英文变量值替换
      $l_f_1wei_cn_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ);
      $l_f_1wei_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ,'name_eng','name_eng');
      $l_f_1wei_cn_key = array_merge($l_f_1wei_cn_key,$l_f_1wei_eng_key);
      $l_arr['code'] = str_replace(array_keys($l_f_1wei_cn_key),$l_f_1wei_cn_key,$l_arr['code']);

      // 2. 然后才能进行字段替换，因为字段通常是没有美元符的{中文}，所以要先替换变量值，而后替换变量
      // code中涉及到的所有sql,均需要在[sql]中以select a.`f1`,b.`f2` from t1 as a,t2 as b limit 1这样的语句
      // 将[code]和[html]部分用sql的表和字段进行替换以后再才能执行。([html]里面如果很复杂就放到[code]里面)
      if ( array_key_exists("sql",$l_arr) && ''!= trim($l_arr['sql']) ){
        // 先分离出所有涉及到的表及表的所有字段
        $l_f_arr = Parse_SQL::BreakawayTblsAndFieldsBySelectSql($a_arr['dbR'], $a_arr, $l_arr["sql"], $request);

        // 分成两个部分, 带点}.{和不带点的。
        $l_f_for_replace_arr = Parse_SQL::pinzhuangTblsAndFields($l_f_arr,$l_arr['code']);

        // 1 先替换带点的，不然可能出现带点的前半部分替换而后面没有替换正确
        if (!empty($l_f_for_replace_arr[1])) {
          $l_arr['code'] = str_replace( $l_f_for_replace_arr[1], array_keys($l_f_for_replace_arr[1]), $l_arr['code']);
        }

        // 2 再替换不带点的部分
        // 所有特征被替换完成以后，才进行单个字段的替换，此时应该是没有大括号的中文字段了
        // 进行该表的中文字段替换，替换了有点的之后，再才进行没有点的替换，顺序别颠倒
        $l_arr['code'] = str_replace( $l_f_for_replace_arr[0], array_keys($l_f_for_replace_arr[0]), $l_arr['code']); // 保证字段名全部英文
      }

      // 将code部分放到一个function中去,function名称采用文件名加
      $l_html = Parse_Arithmetic::eval_code($a_arr,$a_key,$a_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie,$l_arr);

      $response["arithmetic"][$a_key]["pa_val"] = $l_html;  // 算法最终结果保留一份
      //if (!is_array($l_html)) Parse_Arithmetic::Int_FillREQUESTValue($a_arr,$response,$a_vals,array($a_key=>$l_html));
    }
    return ;
  }

  /**
   * 提供了二次嵌入开发能力，能在其中嵌入PHP脚本。一般用于显示
   *
   * 算法模型：
   * [project]
   * name={项目名称}
   * [polym]
   * name={样式名称}
   * [sql]
   * <指定需要执行的SQL语句，对返回的每条记录执行其中的code一次并重复输出html>
   * [code]
   * <指定该算法将要执行的内嵌PHP脚本代码>
   * [html]
   * <该算法的输出结果>
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function SQLResult(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    $l_err = array();
    // 首先将算法解析为一维数组
    $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"]);

    // 先获取项目信息，如果设置了项目的话
    // $l_arr["project"] = "name=测试CMS-用于线上";  // 人工指定一个
    if (array_key_exists("project",$l_arr)) {
      // 获取数据库连接信息
      $l_p_info = Parse_SQL::getPinfoByProjCNname($l_arr["project"]);
      $dbR = new DBR($l_p_info);
    }
    if (array_key_exists("polym",$l_arr)){
      // 样式暂时未实现
    }
    //print_r($l_arr);
    if (array_key_exists("sql",$l_arr)){
      if (!isset($dbR)) $dbR = $a_arr["dbR"];

      // 1 .先进行一些中文变量数值的替换工作,主要是f_data和request中的数值，因为${中文},
      if (array_key_exists('f_data',$a_arr)) $l_requ = $a_arr['f_data'];
      else $l_requ = &$request;

      // 首先替换掉变量值，因为变量值都是当前表的字段。
      $l_f_1wei_cn_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ);
      $l_f_1wei_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ,'name_eng','name_eng');
      $l_f_1wei_cn_key = array_merge($l_f_1wei_cn_key,$l_f_1wei_eng_key);
      $l_arr["sql"] = str_replace(array_keys($l_f_1wei_cn_key),$l_f_1wei_cn_key,$l_arr["sql"]);

      // 需要进行非空判断
      if (""!=$l_arr["sql"]) {
        // 表名替换、字段替换
        $l_sql = Parse_SQL::ReplaceSQlTblAndFieldname($dbR, $a_arr, $a_vals, $l_arr["sql"], $l_requ);

        //$l_sql = "select aups_t3.aups_f1273,aups_t3.aups_f1278 from aups_t3 where aups_t3.aups_f1275='1' and aups_t3.aups_f1273='外-汇' limit 1";
        // 执行sql查询操作, 其正确的结果将作为后面code代码的数据来源之一
        $l_rlt = $dbR->query_plan($l_sql);
        $l_err = $dbR->errorInfo();

        if (PEAR::isError($l_rlt)) {
          echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
        }

        if ($l_err[1]>0){
               // sql有错误，则返回，中止后续执行
               return $l_err;
             }
           }
    }
    // 如果设置了code, 采用文件包含的方式进行执行其代码。这种方式就好比定义的一个function，html是其返回值。
    if (array_key_exists('code',$l_arr) && array_key_exists("html",$l_arr)){
      // sql出来的结果, 可能为array()没有取到，也可能是二维
      // Array( [0] => Array( [aups_f1273] => 外汇 ,
            //                      [aups_f1278] => http://finance.ni9ni.com/forex/index.shtml ))
            // 1) 先替换变量
            $l_f_arr = Parse_SQL::getFieldsBySelectSql($l_arr["sql"]);// 获取所有的字段，包括字段的别名
            $l_arr['code'] = Parse_Arithmetic::PA_ReplaceCN2Value($l_arr['code'],$l_rlt,$l_f_arr);
            $l_html = Parse_Arithmetic::eval_code($a_arr,$a_key,$a_vals,$actionMap,$actionError,$l_requ,$response,$form,$get,$cookie,$l_arr);

      // 将结果注册到$response数组的arithmetic键名中去
      //$response["arithmetic"][$a_key]["f_info"] = $a_vals;
      $response["arithmetic"][$a_key]["pa_val"] = $l_html;
      //if (!is_array($l_html)) Parse_Arithmetic::Int_FillREQUESTValue($a_arr,$response,$a_vals,array($a_key=>$l_html));
    }

    return $l_err;
  }

  /**
   * 1) 该类模板域默认不入库。 2) 该类模板域处理文档向其他项目的跨项目分发。
   *
   * 算法模型：
   * [options]
   * disabled=yes|no
   * mode=clone|delivery|link
   * daemon=yes|no
   * ref_polym=…
   * trigger=yes|no
   * [target]
   * project=…
   * template=…
   * [bind]
   * srcField1=destField1
   * srcField2=destField2
   *
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   */
  function CrossPublish(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    /*var_dump($a_key);
    var_dump($a_vals);
    print_r($a_arr);*/
    // echo var_export($a_vals, true). "  key:" . $a_key . " FUNC:" . __FUNCTION__ ." FILE:" . __FILE__ ." LINE:" . __LINE__ ." \n";
  }


  /**
   * 处理文档的相关发布
   *
   * 算法模型：
   * allow=one,two,three
   *
   * [one]
   * expr=<PHP条件测试语句1>
   * where={项目名称 1}:{模板名称 1}:<条件语句 1>id=<d_id…>
   *
   * [two]
   * expr=<PHP条件测试语句2>
   * where={项目名称 2}:{模板名称 2}:<条件语句 2>
   * id=<d_id…>
   *
   * [three]
   * expr=<PHP条件测试语句3>
   * where={项目名称 3}:{模板名称 3}:<条件语句 3>
   * id=<d_id…>
   *
   *
   * @param array  $a_arr  宏数据
   * @param string $a_key 字段名
   * @param array  $a_vals 字段信息数组
   * 其他参数参考别的文档
   */
  function PostInPage(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie){
    $l_err = array();
    // 首先将算法解析为二维数组
    $l_arr = Parse_Arithmetic::parse_like_ini_file($a_vals["arithmetic"], true);

    // 先判断是否有allow
    if (array_key_exists("allow",$l_arr)){
      //
      $l_allow_arr = explode(",", trim($l_arr["allow"]));

      $dbR = &$a_arr["dbR"];

          // 1 .先进行一些中文变量数值的替换工作,主要是f_data和request中的数值，因为${中文},
      if (array_key_exists('f_data',$a_arr)) $l_requ = $a_arr['f_data'];
      else $l_requ = &$request;

      // 在allow数组中的语句才进行处理
      if (!empty($l_allow_arr)){
        //echo NEW_LINE_CHAR."------------".$a_key." begin------------".NEW_LINE_CHAR;
        //print_r($a_vals);
        $l_i = 0;
        foreach ($l_allow_arr as $l_allow){
          $l_i++;
          if (array_key_exists($l_allow, $l_arr) && array_key_exists("where",$l_arr[$l_allow])){
            // 逐一进行处理, 可能有expr和where
            $l_expr = true;  //
            if (array_key_exists("expr",$l_arr[$l_allow])){
              // 首先替换掉变量值，因为变量值都是当前表的字段。
              if (!isset($l_f_1wei_cn_key)) {
                $l_f_1wei_cn_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ);
                $l_f_1wei_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ,'name_eng','name_eng');
                $l_f_1wei_cn_key = array_merge($l_f_1wei_cn_key,$l_f_1wei_eng_key);
              }
              $l_expr = str_replace(array_keys($l_f_1wei_cn_key),$l_f_1wei_cn_key,$l_arr[$l_allow]["expr"]);

              $l_expr = Parse_Arithmetic::eval_expr($l_expr,$a_key,$a_vals,$l_allow);  // 返回bool值
            }

            // 在满足expr的情况下，执行where条件下的相关发布
            if ($l_expr){
              $l_p_id = $a_arr['p_def']['id'];
              $l_t_id = $a_arr['t_def']['id'];

              // 分解出 项目:表:字段条件
              $l_p_t_f_arr = explode(':',$l_arr[$l_allow]["where"]);

              $l_cont0 = count($l_p_t_f_arr);
              if ($l_cont0<=0 || $l_cont0>3 || ''==trim($l_arr[$l_allow]["where"])) {
                continue;
              }else if (1==$l_cont0){
                // 则默认当前项目，当前表, 基本很少会用到
                $l_where = $l_p_t_f_arr[0];
              }else if (2==$l_cont0){
                // 当前项目、某张表；此种情况较多, 即 表:条件
                $l_t_name_eng = trim($l_p_t_f_arr[0],' {}');  // 中文或英文的

                // 直接使用t_all_数组还是通过数据表查询？????以后具体进行效率优化，当前采用 t_all_数组，某个项目下的表数量不会太多
                // 先判断是英文还是中文的表名
                if (1 == cString_num::Check_stringType($l_t_name_eng)){
                  // 可以认定为英文表名, 就算是表名的中文名，那也是中英同名，在此步骤进行也不会有错

                }else {
                  // 表的中文名称
                  $l_t_name_cn = cArray::Index2KeyArr($a_arr['t_all_'],array("key"=>"name_cn", "value"=>'name_eng'));
                  $l_t_name_eng = $l_t_name_cn[$l_t_name_eng];  // 保证为英文名
                }
                $l_t_name_all = cArray::Index2KeyArr($a_arr['t_all_'],array("key"=>"id", "value"=>'name_eng'));
                // $l_t_name_eng其实就是英文的了
                if (in_array($l_t_name_eng,$l_t_name_all)) {
                  $l_t_id = array_search($l_t_name_eng,$l_t_name_all);

                  // 同时获取该表所有字段定义信息, 多行数据
                  $dbR->table_name = TABLENAME_PREF."field_def"; // 字段定义表的数据必须获取到
                  $l_t_f_info = $dbR->getAlls(" where t_id = ".$l_t_id . " and status_='use' order by list_order ",'id,name_eng,name_cn');  // 字段定义表中的定义
                }else {
                  continue;
                }
                // 表id获取完成end

                $l_where = $l_p_t_f_arr[1];

              }else if (3==$l_cont0){
                continue ;   // 当前先不处理，以后完善之???? 下面写了一些，不过不完善, 没时间测试
                /*$dbR->dbo = &DBO($GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']);
                $l_p_name = trim($l_p_t_f_arr[0],' {}');

                // 查找到项目ID, 找不到则继续下一个
                $dbR->table_name = TABLENAME_PREF."project";
                $l_p_s1 = $dbR->getOne(" where name_cn='".$l_p_name ."' or db_name='".$l_p_name ."' and status_!='stop' limit 1");
                $l_rlt["p_def"] = $l_p_s1;
                // 指定的项目
                $l_p_id  = $l_p_t_f_arr[0];// 需要替换为对应的id
                $l_t_id  = $l_p_t_f_arr[1];// 需要替换为对应的id
                $l_where = $l_p_t_f_arr[2];

                $dbR->dbo = &DBO($l_name0);  // 用完就恢复一下*/
              }
              // 统一对条件语句 $l_where 进行变量替换 类似 expr的方法。
              if (!isset($l_f_1wei_cn_key)) {
                $l_f_1wei_cn_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ);
                $l_f_1wei_eng_key = Parse_Arithmetic::getDollarBraceNameCnArr($a_key,$a_arr["f_info"], $l_requ,'name_eng','name_eng');
                $l_f_1wei_cn_key = array_merge($l_f_1wei_cn_key,$l_f_1wei_eng_key);
              }
              // 1) 先替换字段变量值
              $l_where = str_replace(array_keys($l_f_1wei_cn_key),$l_f_1wei_cn_key,$l_where);
              // 2) 再替换另外一张表的字段名 where={栏目页}:{栏目名称}='${所属栏目}' , 字段 {栏目名称}其实是 {栏目页} 表的字段
              $l_f2_1wei= Parse_Arithmetic::array__map($l_t_f_info, "name_eng", "name_cn", '__call_back_addBrace2str');
              $l_where = str_replace( $l_f2_1wei, array_keys($l_f2_1wei), $l_where); // 保证表名全部英文

              // 3) 将where条件查询出相应的id列表来，最多限制为1条，多了则报警或通知管理员
              // 从数据库中获取到相应的条件。然后逐一执行相关发布
              // 为了防止短期内多个相同的相关发布进行,甚至可能是循环的相关发布，建议采用redis进行存储相关发布请求队列。
              $l_rlt = array();
              if (!empty($l_where)) {
                $dbR->table_name = $l_t_name_eng;  // 英文表名
                $l_rlt = $dbR->getAlls(" where ".$l_where . ( (false===stripos($l_where,' limit ')) ? ' limit 1' : ''), 'id');
                // echo $dbR->getSQL();print_r($l_rlt);
              }

              // 获取到相关的id以后
              if (!empty($l_rlt)) {
                $l_tmp_arr = cArray::Index2KeyArr($l_rlt,array('value'=>'id'));  // 变为数字键名的一维数组 array(3);
                $l_where = $l_tmp_arr[0];

                // 采用memcache，将其放到队列中，由专门的发布程序进行发布成功后记录到数据库中
                // 注册到memcache数组中去, [xiangmuID][biaoID] => 条件表达式
                require_once("DataDriver/db/Nosql.cls.php");
                $l_nosql = new Nosql("memcache");

                // key如何确定?
                $l_mem_key = "_lanmu_publish_";
                //if (1==$l_i) $l_nosql->delete($l_mem_key);
                $get_result = $l_nosql->get($l_mem_key);

                if (empty($get_result)) {
                  // bool(false)的话会进行此步骤，向其中注册该数据
                  $get_result = array();
                  $get_result[$l_p_id][$l_t_id][] = $l_where;
                  $l_nosql->set($l_mem_key, $get_result);
                }else {
                  if (!isset($get_result[$l_p_id][$l_t_id])) {
                    $get_result[$l_p_id][$l_t_id][] = $l_where;
                    $l_nosql->set($l_mem_key, $get_result);
                  }else {
                    if (!in_array($l_where, $get_result[$l_p_id][$l_t_id])) {
                      $get_result[$l_p_id][$l_t_id][] = $l_where;  // 不存在则加入进去
                      $l_nosql->set($l_mem_key, $get_result);
                    }
                  }
                }
              }
            }
          }
        }
        //echo NEW_LINE_CHAR."------------".$a_key ." end------------".NEW_LINE_CHAR;

      }

      //$dbR->dbo = &DBO($l_name0);  // 恢复原来的数据库连接信息
    }
  }
}


class Parse_SQL
{
  function ReplaceSQlTblAndFieldname(&$dbR, &$a_arr, $a_vals,$a_str, $request=array()){
    if (""==$a_str) return $a_str;  // 直接返回

    // 依据不同特征，确定不同的处理方法
    if (false!==stripos($a_str,"select") && preg_match("/select\s+\S+\s+from\s+/i",$a_str)) {
      // 可能是一段select的sql语句，那么就需要找到这样的特征
      // eg：select CONCAT({媒体名称},"-",{英文缩写}),{媒体名称} from {媒体配置} order by {显示顺序}
      $a_str = Parse_SQL::MatchTblnameFieldBySelectSql($dbR, $a_arr, $a_str,$request);

    } else if (preg_match("/\}(\s+)?:(\s+)?\{/",$a_str)){
      // 如果不是select_sql语句，仅仅是表达式。同样首先找出表名，然后才是字段名，最后是字段的数值
      // 也可能是{栏目页}:{栏目名称}='${所属栏目}'
      $a_str = Parse_SQL::MatchTblnameFieldByTblCondition($dbR, $a_arr, $a_str,$request);

    } else if (false!==strpos($a_str,":")){
      //echo $a_str;
      $a_str = Parse_SQL::MatchTblnameFieldByExpr($dbR, $a_arr, $a_str,$request);
      // print_r($a_str);
    } else {
      var_dump($a_vals);echo " FILE:" . __FILE__ . " Line:" . __LINE__;exit;
    }

    return $a_str;
  }
  // 替换sql语句的所有字段，并且最好都带上表名加点
  function ReplaceSQLFieldname(&$dbR, &$a_arr, $a_str,$a_from_sql,$a_tblname_arr,$a_tbl_arr,$a_t_all,$request=array()){
    $a_tblname_arr = array_flip($a_tblname_arr);
    $a_t_all = cArray::Index2KeyArr($a_t_all,array("key"=>"name_eng", "value"=>array()));

    if (!empty($a_tbl_arr)) {
      $dbR->table_name = $a_arr["FLD_def"];

      // 继续替换其中的别名或字段中的中文表名+点
      foreach ($a_tbl_arr as $l_tbl => $l_v){
        $l_f_all = $dbR->getAlls("where t_id=".$a_t_all[$l_tbl]["id"] . " and status_='use'");
        $l_f_1wei = cArray::Index2KeyArr($l_f_all,array("key"=>"name_eng","value"=>"name_cn"));

        // 逐项检查并替换, 虽然效率不高，以后再优化之
        if (is_array($l_f_all)) {
        foreach ($l_f_all as $l_fis){
          $l_f_name_cn = $l_fis["name_cn"];

          // 如果sql语句中有点，通常表示有多张表，则逐个字段进行替换中文名称
          if (false !== strpos($a_str,".{")) {
            //echo $l_f_name_cn."\n";
            if (!empty($l_v)) {
              // 如果有别名，替换相应的字段，但还保留别名
              $l_alias = trim($l_v[0]);
              $a_str = str_replace("{".$l_alias."}".".{".$l_f_name_cn."}", $l_alias.".".$l_fis["name_eng"],$a_str);
              $a_str =         str_replace($l_alias.".{".$l_f_name_cn."}", $l_alias.".".$l_fis["name_eng"],$a_str);
            }
          }
          // 字段中文名必须替换之, 中文替换为英文的
          if (isset($a_tblname_arr[$l_tbl])) {
              $l_alias = $a_tblname_arr[$l_tbl];  // 中文字段名称
              $a_str = str_replace("{".$l_alias."}".".{".$l_f_name_cn."}", $l_tbl.".".$l_fis["name_eng"],$a_str);
              $a_str =         str_replace($l_alias.".{".$l_f_name_cn."}", $l_tbl.".".$l_fis["name_eng"],$a_str);
          }
          $a_str =                   str_replace("{".$l_f_name_cn."}", $l_tbl.".".$l_fis["name_eng"],$a_str);
        }
        }
        // 所有特征被替换完成以后，才进行单个字段的替换，此时应该是没有大括号的中文字段了
        // 进行该表的中文字段替换，替换了有点的之后，再才进行没有点的替换，顺序别颠倒
        $a_str = str_replace( $l_f_1wei, array_keys($l_f_1wei), $a_str); // 保证字段名全部英文
      }
    }

    return $a_str;
  }

    // 从sql语句的table_references 部分解析出所有的表
  function ReplaceSQLFieldname2(&$dbR, &$a_arr, $a_str,$a_from_sql,$a_tblname_arr,$a_tbl_arr,$a_t_all,$request=array()){
    $a_tblname_arr = array_flip($a_tblname_arr);
    $a_t_all = cArray::Index2KeyArr($a_t_all,array("key"=>"name_eng", "value"=>array()));

    if (!empty($a_tbl_arr)) {
      $dbR->table_name = $a_arr["FLD_def"];
      // print_r($a_arr["p_def"]["id"]);  // 当前项目id
      // 继续替换其中的别名或字段中的中文表名+点
      foreach ($a_tbl_arr as $l_tbl => $l_v){
        $l_f_all = $dbR->getAlls("where t_id=".$a_t_all[$l_tbl]["id"] . " and status_='use'");
        $l_f_1wei = cArray::Index2KeyArr($l_f_all,array("key"=>"name_eng","value"=>"name_cn"));

        // 逐项检查并替换, 虽然效率不高，以后再优化之
        if (is_array($l_f_all)) {
        foreach ($l_f_all as $l_fis){
          $l_f_name_cn = $l_fis["name_cn"];

          // 如果sql语句中有点，通常表示有多张表，则逐个字段进行替换中文名称
          if (false !== strpos($a_str,".{")) {
            //echo $l_f_name_cn."\n";
            if (!empty($l_v)) {
              // 如果有别名，替换相应的字段，但还保留别名
              $l_alias = trim($l_v[0]);
              $a_str = str_replace("{".$l_alias."}".".{".$l_f_name_cn."}", $l_alias.".".$l_fis["name_eng"],$a_str);
              $a_str =         str_replace($l_alias.".{".$l_f_name_cn."}", $l_alias.".".$l_fis["name_eng"],$a_str);
            }
          }
          // 字段中文名必须替换之, 中文替换为英文的
          $l_alias = $a_tblname_arr[$l_tbl];  // 中文字段名称
          $a_str = str_replace("{".$l_alias."}".".{".$l_f_name_cn."}", $l_tbl.".".$l_fis["name_eng"],$a_str);
          $a_str =         str_replace($l_alias.".{".$l_f_name_cn."}", $l_tbl.".".$l_fis["name_eng"],$a_str);
          // 替换其中的数值，如果有数值时候--------------------
          if (!empty($request)) {
            //print_r($request);exit;
          }
          //$a_str =                   str_replace('${'.$l_f_name_cn.'}',        "`".$l_fis["name_eng"]."`",$a_str);

          $a_str =                   str_replace("{".$l_f_name_cn."}",        "`".$l_fis["name_eng"]."`",$a_str);
        }
        }
        // 所有特征被替换完成以后，才进行单个字段的替换，此时应该是没有大括号的中文字段了
        // 进行该表的中文字段替换，替换了有点的之后，再才进行没有点的替换，顺序别颠倒
        $a_str = str_replace( $l_f_1wei, array_keys($l_f_1wei), $a_str); // 保证字段名全部英文
      }
    }

    return $a_str;
  }

  // 从下面那个方法的逆向
  function pinzhuangTblsAndFields($l_f_arr,$a_str=''){
    // 拼装一些可能的组合进行字符串的替换工作
    $l_f_for_replace = array();
    $l_f_for_replace2 = array();

    // 只有一张表的时候, 通常不需要}.{
    if (!empty($l_f_arr)) {
      // 多张表的时候，字段通常需要带上表中文名
      foreach ($l_f_arr as $l_t_eng => $l_tmp){
        // 分解成多个项
        $l_t_cn = $l_tmp["name_cn"];
        $l_fiels = $l_tmp["fields"];
        $l_f_for_replace[$l_t_eng] = $l_t_cn;
        $l_f_for_replace = array_merge($l_f_for_replace,$l_f_arr[$l_t_eng]['fields']);

        if (false!==strpos($a_str,'.{') || false!==strpos($a_str,'*')) {
          if (is_array($l_fiels)) {
          foreach ($l_fiels as $l_f_eng=>$l_f_cn){
            // 被替换部分可能有4种情况, 这里主要处理两张常见情况: {中文表名}.{中文字段} {中文表名}.{英文字段}
            $l_f_for_replace2[$l_t_eng.'.`'.$l_f_eng.'`'] = $l_t_cn.'.'.$l_f_eng;
            $l_f_for_replace2[$l_t_eng.'.`'.$l_f_eng.'`'] = $l_t_cn.'.'.$l_f_cn;
          }
          }
        }
      }
    }

    return array($l_f_for_replace, $l_f_for_replace2);
  }
  /**
   * 分离出sql中的所有数据表及其表的所有字段
   * 这样一句 select {保存路径},{栏目名称} from {栏目配置} as a,{媒体配置} as b
   * 得到的结果类似于
Array
(
    [aups_t3] => Array
        (
            [name_cn] => {栏目配置}
            [fields] => Array
                (
                    [aups_f70] => {栏目名称}
                    [aups_f73] => {保存路径}
                )

        )

    [aups_t4] => Array
        (
            [name_cn] => {媒体配置}
            [fields] => Array
                (
                )

        )

)
   *
   */
  function BreakawayTblsAndFieldsBySelectSql(&$dbR, &$a_arr, $a_str, $request){
    $l_rlt = array();
    $l_match = array();

    // 为了安全考虑当前只支持select语句，否则退出不执行
    if (!preg_match("/^select/i",ltrim($a_str))) {
      return $l_rlt;
    }
    // 将语句从from处截取, 按照select语句的语法规定进行的截取
    if (false!==strpos($a_str, 'from ')) {
      $l_tmp = explode('from ',$a_str);
      $l_reg = "/where|limit|order|group|HAVING|PROCEDURE|for/i";
      $l_match = preg_split($l_reg,$l_tmp[1],-1,PREG_SPLIT_NO_EMPTY);
    }
    // 获取$l_match--空数组或一个完整的匹配以及单纯的没有点和大括号.{}等字符的表名

    $l_tbl_arr = array();  // 先找到涉及到的所有表
    if (!empty($l_match)) {
      // 需要从数据库中获取所有的表名，并完成英文替换。涉及到中文名，因此只能从表定义表中获取
      $dbR->table_name = $a_arr["TBL_def"];
      $l_t_all = $dbR->getAlls();
      $l_t_1wei = cArray::Index2KeyArr($l_t_all,array("key"=>"name_eng","value"=>"name_cn"));
      $l_t_1wei_key = array_map("__call_back_addBrace2str", $l_t_1wei);

      // 进行数据表名的中文替换为英文操作。多张表名能同时替换，单个替换用array_search($l_tmp[1], $l_t_1wei)进行
      $l_from_sql = str_replace( $l_t_1wei_key, array_keys($l_t_1wei_key), $l_match[0]); // 保证表名全部英文

      // 从sql语句的table_references 部分，分离出所有的表（全部为英文的）。不允许额外别名，这里携带的别名就是表的中文名
      $l_tbl_arr = Parse_SQL::getTblsByFromSql($l_from_sql,$l_t_1wei_key);
    }

    // 然后再找涉及到的字段
    if (!empty($l_tbl_arr)) {
      // 依据表名获取到全部的字段
      $l_rlt = Parse_SQL::BreakawayFieldname($dbR,$a_arr,$a_str,$l_tmp[0],$l_t_1wei,$l_tbl_arr,$l_t_all,$request);
    }

    return $l_rlt;
  }
  function BreakawayFieldname(&$dbR, &$a_arr, $a_str,$a_select_sql,$a_tblname_arr,$a_tbl_arr,$a_t_all,$request=array()){
    $l_rlt = array();
    $a_tblname_arr = array_flip($a_tblname_arr);
    $a_t_all = cArray::Index2KeyArr($a_t_all,array("key"=>"name_eng", "value"=>array()));

    // 分离字段
    $l_reg = "/select|from/i";
    $l_match = preg_split($l_reg,ltrim($a_str),-1,PREG_SPLIT_NO_EMPTY);

    if (!empty($a_tbl_arr) && !empty($l_match)) {
      // $l_f_rela -->  Array([{保存路径}] => ,[{栏目名称}] => )
      //$l_f_rela = Parse_SQL::getAlias(explode(",", $l_match[0]));
      //$l_f_rela_keys = array_keys($l_f_rela);  // 得到字段名或中文名

      $dbR->table_name = $a_arr["FLD_def"];

      // 继续替换其中的别名或字段中的中文表名+点
      foreach ($a_tbl_arr as $l_tbl => $l_v){
        $l_f_all = $dbR->getAlls("where t_id=".$a_t_all[$l_tbl]["id"] . " and status_='use'");
        $l_f_1wei_key = Parse_Arithmetic::array__map($l_f_all,'name_eng','name_cn','__call_back_addBrace2str');

        // 如果sql语句中有点，则将全部的字段收录
        /*if (false !== strpos($l_match[0],".{") || false!==strpos($a_str,'*')) {
          // 全部字段，无需交集，当然使用的时候也记得要带上表名+点
          $l_new_arr = $l_f_1wei_key;
        }else {
          // 取交集是为了最小限度地知道涉及到表的部分字段，防止全部字段可能跟其他元素在替换的时候的冲突，被错误覆盖。
          //$l_new_arr = array_intersect($l_f_1wei_key, $l_f_rela_keys);
        }*/

        $l_rlt[$l_tbl] = array('name_cn'=>$l_v,'fields'=>$l_f_1wei_key);
      }
    }

    return $l_rlt;
  }

  // 此语句只从sql中截取过from table_references 这段sql中，
  // FROM employee AS t1, info AS t2 也可能是中文的
  function getTblsByFromSql($a_str,$a_cn_arr=array()){
    $l_rlt = array();

    $a_str = str_ireplace("from ","",$a_str);
    $l_tmp = explode(",", $a_str);  // 可能有多张数据表

    $l_rlt = Parse_SQL::getAlias($l_tmp,$a_cn_arr);

    return $l_rlt;
  }

  // 从select语句中分离出字段来
  // 从from截断，找到字段以及其别名
  function getFieldsBySelectSql($a_str){
    $l_rlt = array();

    $l_tmp = array();
    if (false!==stripos($a_str,"select") && preg_match("/^select\s+(\S+)\s+from\s+/i",ltrim($a_str),$l_preg )) {
      // 分解
      $l_tmp = explode(",", $l_preg[1]);  // 可能有多个字段
    }

    // 进行逐行分解
    $l_rlt = Parse_SQL::getAlias($l_tmp);

    return $l_rlt;
  }

  // {栏目页}:{栏目名称}='${所属栏目}'这样的语句
  function MatchTblnameFieldByTblCondition(&$dbR, &$a_arr, $a_str, $request){
    $l_match = array();

    // 为了安全考虑进行严格限制，否则退出不执行
    if (!preg_match("/\}(\s+)?:(\s+)?\{/",$a_str)) {
      return $a_str;
    }
    // 将语句进行分解，分离表名和字段名，先替换掉表名，字段可以留在下一步进行替换
    $l_match = cArray::str2keyvalue($a_str,":");

    if (!empty($l_match)) {
      $l_tmp = array_keys($l_match);
      $l_tname = str_replace(array("{","}"), "", $l_tmp[0]);  // 去掉大括号
      $l_match[0] =  $l_tmp[0];
      $l_match[1] = $l_tname;
    }
    // $l_match是空数组或一个完整的匹配以及单纯的没有点和大括号.{}等字符的表名

    if (!empty($l_match)) {
      // 需要从数据库中获取所有的表名，并完成英文替换。涉及到中文名，因此只能从表定义表中获取
      $dbR->table_name = $a_arr["TBL_def"];
      $l_t_all = $dbR->getAlls();
      $l_t_1wei = cArray::Index2KeyArr($l_t_all,array("key"=>"name_eng","value"=>"name_cn"));

      // 进行数据表名的中文替换为英文操作。多张表名能同时替换，单个替换用array_search($l_tmp[1], $l_t_1wei)进行
      $l_tbl_part = str_replace( $l_t_1wei, array_keys($l_t_1wei), $l_match[1]); // 保证表名全部英文
      // 从sql语句的table_references 部分
      $l_tbl_arr = Parse_SQL::getTblsByFromSql($l_tbl_part);  // 分离表名的别名

      // 找到以后，需要进行整句替换.返回完整的语句中全部是英文表名
      $a_str = str_replace($l_match[0], $l_tbl_part, $a_str);  //(替换掉了中文表名的整句)

      // 替换sql中的字段名。
      // 先找出相应的表名，依据表名获取到全部的字段还要执行中文的字段名替换
      $a_str = Parse_SQL::ReplaceSQLFieldname2($dbR,$a_arr,$a_str,$l_tbl_part,$l_t_1wei,$l_tbl_arr,$l_t_all,$request);
    }

    return $a_str;
  }

  //
  function MatchTblnameFieldByExpr(&$dbR, &$a_arr, $a_str, $request){
    $l_match = array();

    // 将语句进行分解，分离表名和字段名，先替换掉表名，字段可以留在下一步进行替换
    $l_match = cArray::str2keyvalue($a_str,":");

    if (!empty($l_match)) {
      $l_tmp = array_keys($l_match);
      $l_tname = str_replace(array("{","}"), "", $l_tmp[0]);  // 去掉大括号
      $l_match[0] =  $l_tmp[0];
      $l_match[1] = $l_tname;
    }
    // $l_match是空数组或一个完整的匹配以及单纯的没有点和大括号.{}等字符的表名

    if (!empty($l_match)) {
      // 需要从数据库中获取所有的表名，并完成英文替换。涉及到中文名，因此只能从表定义表中获取
      $dbR->table_name = $a_arr["TBL_def"];
      $l_t_all = $dbR->getAlls();
      $l_t_1wei = cArray::Index2KeyArr($l_t_all,array("key"=>"name_eng","value"=>"name_cn"));

      // 进行数据表名的中文替换为英文操作。多张表名能同时替换，单个替换用array_search($l_tmp[1], $l_t_1wei)进行
      $l_tbl_part = str_replace( $l_t_1wei, array_keys($l_t_1wei), $l_match[1]); // 保证表名全部英文
      // 从sql语句的table_references 部分
      $l_tbl_arr = Parse_SQL::getTblsByFromSql($l_tbl_part);  // 分离表名的别名

      // 找到以后，需要进行整句替换.返回完整的语句中全部是英文表名
      $a_str = str_replace($l_match[0], $l_tbl_part, $a_str);  //(替换掉了中文表名的整句)

      // 替换sql中的字段名。
      // 先找出相应的表名，依据表名获取到全部的字段还要执行中文的字段名替换
      $a_str = Parse_SQL::ReplaceSQLFieldname2($dbR,$a_arr,$a_str,$l_tbl_part,$l_t_1wei,$l_tbl_arr,$l_t_all,$request);
    }

    return $a_str;
  }

  // 从sql语句中获取表英文名或表id。暂不支持多表
  function MatchTblnameFieldBySelectSql(&$dbR, &$a_arr, $a_str, $request){
    $l_match = array();
    // 为了安全考虑当前只支持select语句，否则退出不执行
    if (!preg_match("/^select/i",ltrim($a_str))) {
      return $a_str;
    }
    // 将语句从from处截取, 按照select语句的语法规定进行的截取
    $l_reg = "/from\s+(\S+[^where|limit|order|group|HAVING|PROCEDURE|for])/i";
    preg_match($l_reg,$a_str,$l_match);

    // 获取$l_match--空数组或一个完整的匹配以及单纯的没有点和大括号.{}等字符的表名

    if (!empty($l_match)) {
      // 需要从数据库中获取所有的表名，并完成英文替换。涉及到中文名，因此只能从表定义表中获取
      $dbR->table_name = $a_arr["TBL_def"];
      $l_t_all = $dbR->getAlls();
      $l_t_1wei = cArray::Index2KeyArr($l_t_all,array("key"=>"name_eng","value"=>"name_cn"));
      $l_t_1wei_cn_key = array_map("__call_back_addBrace2str", $l_t_1wei);
      //$l_t_1wei_cn_key = array_flip($l_t_1wei_cn_key);  无需键值对调

      // 进行数据表名的中文替换为英文操作。多张表名能同时替换，单个替换用array_search($l_tmp[1], $l_t_1wei)进行
      $l_from_sql = str_replace( $l_t_1wei_cn_key, array_keys($l_t_1wei_cn_key), $l_match[1]); // 保证表名全部英文

      // 从sql语句的table_references 部分，分离出所有的表（全部为英文的）
      $l_tbl_arr = Parse_SQL::getTblsByFromSql($l_from_sql);

      // 找到以后，需要进行整句替换.返回完整的sql语句
      $a_str = str_replace($l_match[0], "from ".$l_from_sql, $a_str);  //(替换掉了中文表名的整句)

      // 替换sql中的字段名。
      // 先找出相应的表名，依据表名获取到全部的字段还要执行中文的字段名替换
      $a_str = Parse_SQL::ReplaceSQLFieldname($dbR,$a_arr,$a_str,$l_from_sql,$l_t_1wei,$l_tbl_arr,$l_t_all,$request);
    }

    return $a_str;
  }

  // 通过项目中文名获取项目信息
  function getPinfoByProjCNname($a_str_project){
    // 需要获取到项目id等数据，暂时不支持这样，只有在跨项目的时候才允许，或者以后再完善
    $dbR = new DBR($GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']);  // 连项目本身， 去获取项目名称等数据
    $dbR->table_name = TABLENAME_PREF."project";
    $l_p_arr = $dbR->getAlls("","id,name_cn");
    if (PEAR::isError($l_p_arr)) {
      echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
      return array();
    }

    // 获取id组成的一维数组 Array([通用发布系统] => 1 [财汇数据库] => 2)得到这样的数据
    $l_p_arr = cArray::Index2KeyArr($l_p_arr,array("key"=>"name_cn","value"=>"id"));

    // 匹配出项目中文名称
    if (!is_array($a_str_project)) $a_str_project = cArray::str2keyvalue($a_str_project);
    $l_p_name = convCharacter(trim($a_str_project["name"]));

    $l_p_id = $l_p_arr[$l_p_name];
    $l_p_id += 0;
    if ($l_p_id>0) {
      $l_p_arr = $dbR->getOne("where id=".$l_p_id);
      if (PEAR::isError($l_p_arr)) {
        echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
        return array();
      }
    }else {
      $l_p_arr = array();
    }
    return $l_p_arr;
  }


  function getAlias($a_arr,$a_cn_arr=array()){
    $l_rlt = array();
    if (is_array($a_arr)) {
    foreach ($a_arr as $l_val){
      $l_v = trim($l_val);
      $l_vv = '';

      //
      if (preg_match("/(\s+as\s+)/i",$l_v,$l_match)) {
        // 说明有别名
        $l_tmp2 = explode($l_match[1], $l_v);
        $l_v = trim($l_tmp2[0]);
        $l_vv = trim($l_tmp2[1]);
      }

      if (!empty($a_cn_arr) && array_key_exists($l_v,$a_cn_arr)) {
        $l_vv = $a_cn_arr[$l_v];
      }

      $l_rlt[$l_v] = $l_vv;
    }
    }

    return $l_rlt;
  }
}



class Parse_Arithmetic
{
  //
  function getArithmetic_Result_str(){
      // 因为在没有new一个对象的时候，不能使用对象中的全局属性，所以采用此方法
      return "pa_arithmetic_result_";
    }

    function array__map($a_f_info,$a_key_key="name_eng", $a_val_key="name_cn",$a_map_method='__call_back_addDollarBrace2str'){
      $l_f_1wei = cArray::Index2KeyArr($a_f_info,array("key"=>$a_key_key,"value"=>$a_val_key));
    return array_map($a_map_method, $l_f_1wei);
    }
    // 将英文键名转换成中文键名并且加${}，而数值则是提供的英文字段对应的数值，例如request数组
    // 替换掉代码中的变量为相应数据 ${所属栏目}替换为相应的post数值
  function getDollarBraceNameCnArr($a_key,$a_f_info,$a_data_arr,$a_key_key="name_eng", $a_val_key="name_cn",$a_map_method='__call_back_addDollarBrace2str'){
    //if ("aups_f023"==$a_key) print_r($a_data_arr);
    $l_f_1wei_cn_key = Parse_Arithmetic::array__map($a_f_info,$a_key_key, $a_val_key,$a_map_method);
    $l_f_1wei_cn_key = array_flip($l_f_1wei_cn_key);  // 数组键值互换，转为中文键名
    array_walk($l_f_1wei_cn_key,"__call_back_ReplaceValue", $a_data_arr);// 结合post数组，

    return $l_f_1wei_cn_key;
  }

  function Int_FillREQUESTValue(&$arr, &$response, $a_fils_info, $a_data_arr){
    // 此处的注册可以从_STR_REPLACE_fields_VAL获取到，其他地方因为有额外添加前缀
    //if (!array_key_exists('_STR_REPLACE_fields_info',$arr)) $arr['_STR_REPLACE_fields_info'] = array();
    //$arr['_STR_REPLACE_fields_info'] = array_merge($arr['_STR_REPLACE_fields_info'],$a_data_arr);

    // 中文字段也同时需要填充上去
    $l_data_cn  = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillREQUESTValue',$a_fils_info,$a_data_arr,'name_eng','name_cn');
    $l_data_eng = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillREQUESTValue',$a_fils_info,$a_data_arr,"name_eng",'name_eng');

    // 注册到数组中去
    if (!array_key_exists('_STR_REPLACE_fields_VAL',$arr)) {
      $arr['_STR_REPLACE_fields_VAL'] = array();
    }
    // 将中文和英文字段都添加进去
    $arr['_STR_REPLACE_fields_VAL'] = array_merge($arr['_STR_REPLACE_fields_VAL'],$l_data_eng,$l_data_cn);
    return ;
  }

  function Int_FillPROJECTValue(&$arr, &$response){
    $l_p_def = $arr['p_def'];
    // 将表定义中的部分信息也放进来, 除了有一项模板设计的数据是数组的不能放进来
    if (isset($arr['t_def']['id']))  $l_p_def['TABLE_id'] = $arr['t_def']['id'];
    if (isset($arr['t_def']['name_eng']))$l_p_def['TABLE_name_eng'] = $arr['t_def']['name_eng'];
    if (isset($arr['t_def']['name_cn']))$l_p_def['TABLE_name_cn'] = $arr['t_def']['name_cn'];

    if (!array_key_exists('_STR_REPLACE_fields_info',$arr)) $arr['_STR_REPLACE_fields_info'] = array();
    $arr['_STR_REPLACE_fields_info'] = array_merge($arr['_STR_REPLACE_fields_info'],$l_p_def);

    $l_keys_cn = array_map('__call_back_addDollarBracePRO2str', array_keys($l_p_def));

    if (!array_key_exists('_STR_REPLACE_fields_VAL',$arr)) $arr['_STR_REPLACE_fields_VAL'] = array();
    $arr['_STR_REPLACE_fields_VAL'] = array_merge($arr['_STR_REPLACE_fields_VAL'], array_combine($l_keys_cn,array_values($l_p_def)) );
    return ;
  }

  // 填充用户名信息 $a_data_arr 通常是 session['user']
  function Int_FillUSERValue(&$arr, &$response, $a_data_arr){
    // 主要用到的用户也就这两项了
    $l_user_f_info = array(
      "_USER_id"    =>array("name_eng"=>"_USER_id","name_cn"=>"当前用户ID"),
      "_USER_username"=>array("name_eng"=>"_USER_username","name_cn"=>"当前用户名")
    );
    if (!array_key_exists('_STR_REPLACE_fields_info',$arr)) $arr['_STR_REPLACE_fields_info'] = array();
    $arr['_STR_REPLACE_fields_info'] = array_merge($arr['_STR_REPLACE_fields_info'],$l_user_f_info);

    // 获取对应的数值
    $l_data = array();
    $l_data["_USER_id"]     = $a_data_arr['id'];
    $l_data["_USER_username"] = $a_data_arr['username'];

    // 中文字段也同时需要填充上去
    $l_data_cn  = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillUSERValue',$l_user_f_info,$l_data,'name_eng','name_cn');
    $l_data_eng = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillUSERValue',$l_user_f_info,$l_data,"name_eng",'name_eng');

    // 注册到数组中去
    if (!array_key_exists('_STR_REPLACE_fields_VAL',$arr)) {
      $arr['_STR_REPLACE_fields_VAL'] = array();
    }
    // 将中文和英文字段都添加进去
    $arr['_STR_REPLACE_fields_VAL'] = array_merge($arr['_STR_REPLACE_fields_VAL'],$l_data_eng,$l_data_cn);
    return ;
  }

  // 填充用户名、时间信息到用于模板替换的数组中去,字段_STR_REPLACE_fields_info,包括中英键名
  function Int_FillDataTimeValue(&$arr, &$response, &$request, $a_requ_key=array('createdate','createtime')){
    // 将创建时间进行分解成 YYYY mm dd HH ii ss 这六个变量
    $l_date_time_f_info = array(
      "_SYSTEM_date"=>array("name_eng"=>"_SYSTEM_date","name_cn"=>"系统当前日期"),
      "_SYSTEM_time"=>array("name_eng"=>"_SYSTEM_time","name_cn"=>"系统当前时间"),
      "YYYY"=>array("name_eng"=>"YYYY","name_cn"=>"创建年份"),
      "mm"=>array("name_eng"=>"mm","name_cn"=>"创建月份"),
      "dd"=>array("name_eng"=>"dd","name_cn"=>"创建日"),
      "HH"=>array("name_eng"=>"HH","name_cn"=>"创建小时"),
      "ii"=>array("name_eng"=>"ii","name_cn"=>"创建分钟"),
      "ss"=>array("name_eng"=>"ss","name_cn"=>"创建秒")
    );
    if (!array_key_exists('_STR_REPLACE_fields_info',$arr)) $arr['_STR_REPLACE_fields_info'] = array();
    $arr['_STR_REPLACE_fields_info'] = array_merge($arr['_STR_REPLACE_fields_info'],$l_date_time_f_info);

    if (!empty($a_requ_key) && array_key_exists($a_requ_key[0],$request)) {
      $l_date = ("0000-00-00"==$request[$a_requ_key[0]]) ? date("Y-m-d") : $request[$a_requ_key[0]];
      $l_time = ("00:00:00"==$request[$a_requ_key[1]])   ? date("H:i:s") : $request[$a_requ_key[1]];
    }else {
      $l_date = date("Y-m-d");
      $l_time = date("H:i:s");
    }
    list($YYYY,$mm,$dd) = explode("-",$l_date);
    list($HH,$ii,$ss) = explode(":",$l_time);

    // 获取对应的数值
    $l_YmdHis = array();
    $l_YmdHis["_SYSTEM_date"] = date("Y-m-d");
    $l_YmdHis["_SYSTEM_time"] = date("H:i:s");
    $l_YmdHis["YYYY"] = $YYYY;
    $l_YmdHis["mm"] = $mm;
    $l_YmdHis["dd"] = $dd;
    $l_YmdHis["HH"] = $HH;
    $l_YmdHis["ii"] = $ii;
    $l_YmdHis["ss"] = $ss;

    // 中文字段也同时需要填充上去
    $l_YmdHis_cn = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillDataTimeValue',$l_date_time_f_info,$l_YmdHis);
    $l_YmdHis_eng = Parse_Arithmetic::getDollarBraceNameCnArr('Int_FillDataTimeValue',$l_date_time_f_info,$l_YmdHis,"name_eng",'name_eng');

    // 注册到数组中去
    if (!array_key_exists('_STR_REPLACE_fields_VAL',$arr)) {
      $arr['_STR_REPLACE_fields_VAL'] = array();
    }
    // 将中文和英文字段都添加进去
    $arr['_STR_REPLACE_fields_VAL'] = array_merge($arr['_STR_REPLACE_fields_VAL'],$l_YmdHis_eng,$l_YmdHis_cn);
    return ;
  }

  function Int_FillDefDuo(&$arr, &$response, &$request){
    // 继续注册 算法字段的数据进来, 在各个算法的时候已经注册了
    $l_pa_arr1 = array();
    if (is_array($response["arithmetic"])) {
    foreach ($response["arithmetic"] as $l_fi=>$l__v){
      if (is_array($l__v)) {
        if (array_key_exists("pa_val",$l__v)) {
          if (is_array($l__v["pa_val"])) {
            if (array_key_exists($l_fi,$request)) {
              $l_pa_arr1[$l_fi] = $request[$l_fi];
            }
          }else {
            $l_pa_arr1[$l_fi] = $l__v["pa_val"];
          }
        }
      }else {
        $l_pa_arr1[$l_fi] = $l__v;
      }
    }
    }
    if (!empty($l_pa_arr1)) {
      Parse_Arithmetic::Int_FillREQUESTValue($arr, $response, $arr['f_def_duo'], $l_pa_arr1);
    }
  }

  function Int_FillALL(&$arr, &$response, &$request){
    // 注册或获取需要替换的字段中文名=>数值等数组, 填充文档创建时间或系统时间
    Parse_Arithmetic::Int_FillUSERValue($arr, $response, $_SESSION['user']);
    Parse_Arithmetic::Int_FillDataTimeValue($arr, $response, $request, array('createdate','createtime'));

    // 注册项目相关的数据进来
    Parse_Arithmetic::Int_FillPROJECTValue($arr, $response, $request, array('createdate','createtime'));

    //
    Parse_Arithmetic::Int_FillREQUESTValue($arr, $response, $arr['f_info'], $arr['f_data']);

    Parse_Arithmetic::Int_FillDefDuo($arr, $response, $request);
  }

  // 基于真实字段的算法分析
  function parse_for_list_form(&$a_arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie) {
    // 主要依据f_type动态地将type和length变成新数据，便于生成html，必要的时候增加一些字段。
    if (!empty($a_arr["f_info"])) {
      $l_class = $l_func = "";
      foreach ($a_arr["f_info"] as $l_key => $l_vals){
        // 先按照f_type进行判断，如果有这个字段的话
        if (array_key_exists("arithmetic", $l_vals) && ""!=trim($l_vals["arithmetic"]) && ""!=trim($l_vals["f_type"])) {
          // 依据不同值进行不同的算法
          $l_tmp = explode("::",trim($l_vals["f_type"]));
          $l_class = $l_tmp[0];
          $l_func  = $l_tmp[1];
          $l_class = new $l_class;
          $l_rlt = $l_class->$l_func($a_arr,$l_key,$l_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
        }else {
          // 当没有f_type类型定义的时候，沿用以前的方式。不做任何处理
        }
      }
    }
  }

  // 添加或修改成功后的算法，主要是执行触发器功能，完成各种功能算法。
  function do_arithmetic_by_add_action(&$a_arr,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie) {
    // 主要依据实际表中不存在的字段的算法进行相应处理
    if (!empty($a_arr["f_def_duo"])) {
      $l_class = $l_func = "";
      foreach ($a_arr["f_def_duo"] as $l_key => $l_vals){
        // 先按照f_type进行判断，如果有这个字段的话
        $l_no_act = array("","Application::SQLResult");
        $l_no_act = array("");
        if (array_key_exists("arithmetic", $l_vals) && ""!=trim($l_vals["arithmetic"]) && !in_array($l_vals["f_type"],$l_no_act)) {
          // 依据不同值进行不同的算法
          $l_tmp = explode("::",trim($l_vals["f_type"]));
          $l_class = $l_tmp[0];
          $l_func  = $l_tmp[1];
          $l_class = new $l_class;
          $l_rlt = $l_class->$l_func($a_arr,$l_key,$l_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
        }else {
          // 当没有f_type类型定义的时候，沿用以前的方式。不做任何处理
        }
      }
    }
  }

  // 将类似ini的文件解析成一维数组, 特别的allow=post1,post2这样的也支持, 也同时支持转为二维
  function parse_like_ini_file($l_str,$a_2erwei=false) {
    $l_arr = array();
    $l_tmp = explode("\n", $l_str);
    if (!empty($l_tmp)) {
      $l_first_str = "";  // 第一个数组键名
      $l_k = "";  // 初始化特征索引
      foreach ($l_tmp as $l_line){
        // 逐行检查是否特征字符串
        $l_ll = trim($l_line);

        if (preg_match('/^\[(\w+)\](<\?(php)?(\s+)?)?$/',$l_ll,$matches)) {
          //if ('[code]<?php' == $l_ll) $matches = array('[code]<?php','code');
          $l_n = trim($matches[1]);
          if (""==$l_first_str) $l_first_str=$l_n;  // 找到第一个
          if ($l_n!==$l_k && array_key_exists($l_k,$l_arr)) {
            // 每次当发现了新的键名的时候, 需要做一些处理
            Parse_Arithmetic::func1($l_arr, $l_k, $a_2erwei);
          }
          $l_k = $l_n;
        }else {
          if (""!==$l_k) {
              if (isset($l_arr[$l_k]))
                  $l_arr[$l_k] .= $l_line."\n";
              else
                  $l_arr[$l_k] = $l_line."\n";
          }
        }
      }
      if (!empty($l_arr)) {
        // 由于最后的键名没有进行处理，还需要对最后的键名做同样处理，这才完整
        Parse_Arithmetic::func1($l_arr, $l_k, $a_2erwei);
      }
      // end

      // 对上面没有[]的数据进行逐行分解key value
      if (""!=$l_first_str){
        $l_tmp = explode("[$l_first_str]", $l_str);
        $l_tmp = cArray::str2keyvalue($l_tmp[0]);
        $l_arr = array_merge($l_tmp,$l_arr);
      }
    }

    return $l_arr;
  }
  // 内部使用的方法，仅限于上面对于字符串的处理
  function func1(&$l_arr, $l_k, $a_2erwei){
    if ("\r\n"==substr($l_arr[$l_k], -2)) {
      $l_arr[$l_k] = substr($l_arr[$l_k], 0, -2);
    }else if ("\n"==substr($l_arr[$l_k], -1)) {
      $l_arr[$l_k] = substr($l_arr[$l_k], 0, -1);
    }

    // 如果转二维，则需要进行处理
    if ($a_2erwei) {
      $l_arr[$l_k] = cArray::str2keyvalue($l_arr[$l_k]);
    }
  }

  // 填充字段的类型和长度，便于呈现下拉框, 通常要强制性执行替换字段类型为enum
  function fillInselect(&$a_arr,$a_key,$l_rlt,$a_force=true, $a_field_type="enum"){
    if (!empty($l_rlt) || $a_force) {
      //$l_fi_ty = strtoupper($a_arr["f_info"][$a_key]["type"]);
      //if (false!==strpos( $l_fi_ty,"INT") || in_array($l_fi_ty, array("FLOAT","DOUBLE","DECIMAL")) ) $l_tmp = array("0"=>"0");
      //else $l_tmp = array(""=>"");  // 为了下拉框有空值，必须这样。以后适当的时候去掉????

      if (!empty($l_rlt)) {
      foreach ($l_rlt as $l_val){
        if (is_array($l_val)) {
          // 填充数组
          $l_ttmp = array_values($l_val);
          // 数值的个数
          $l_num = count($l_ttmp);
          if ($l_num<=0) {
            $l_tmp[""] = "";
          }else if ($l_num==1) {
            // 如果数据项不够，通常由于两个key一样所致，需要进行合并
            // 更严格的应该进行sql语句检查，是否确实select f1,f1 from 获取了两次
            $l_tmp[$l_ttmp[0]] = $l_ttmp[0];
          }else {
            $l_tmp[$l_ttmp[0]] = $l_ttmp[1];
          }
        }
      }
      }
      if ("enum" == $a_field_type) {
        // 需要将数据中的数据进行覆盖即可
        $a_arr["f_info"][$a_key]["type"]   = "enum";  // 枚举型
        $a_arr["f_info"][$a_key]["length"]  = $l_tmp;
      }else {
        //$a_arr["f_info"][$a_key]["type"]   = "VARCHAR";// 变量类型
        $a_arr["f_info"][$a_key]["length"]  = $l_tmp;
      }
    }
    return ;
  }

  // 这种类型算法字符串的解析，sql
  /*function DB_Select(&$a_arr,$a_key,$a_vals){
    echo var_export($a_vals, true). "  key:" . $a_key . " FUNC:" . __FUNCTION__ ." FILE:" . __FILE__ ." LINE:" . __LINE__ ." \n";
  }*/



  // 将字符串中的中文变量替换为相应的数值, 是解析算法中需要用到的 ParseArithmetic
  function PA_ReplaceCN2Value($a_str, $a_rlt, $a_f_arr){

    // 有别名的以后再处理，????将最简单的先实现了。
    // 依据sql出来的结果进行相应字段值的替换
    if (count($a_rlt)==1) {
      $l_arr = array_values($a_rlt[0]);
      $l_i = 0;
      if (is_array($a_f_arr)) {
      foreach ($a_f_arr as $l_k => $l_val){
        $a_f_arr[$l_k] = $l_arr[$l_i];

        $l_i++;
      }
      }
    }

    $a_str = str_replace(array_keys($a_f_arr),$a_f_arr,$a_str);

    return $a_str;
  }

  // 较其他方法，最后多了一个l_arr的参数
  function eval_code(&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie,$l_arr){
    $l_func = preg_replace('/\W/', "_", basename(__FILE__) . "_" .$a_arr['t_def']['p_id']."_" .$a_vals["t_id"]."_".$a_key."_". utime());
    $l_func_str = pinzhuangFunctionStr($l_arr, $l_func, '&$a_arr,$a_key,$a_vals,&$actionMap,&$actionError,&$request,&$response,$form,$get,$cookie');
    if ((isset($GLOBALS['cfg']['log_all']) && $GLOBALS['cfg']['log_all']) || file_exists($GLOBALS['cfg']['LOG_PATH'] . '/log_all')) {
      // 只记录一次
      if (isset($GLOBALS['cfg']['log_all'])) unset($GLOBALS['cfg']['log_all']);
      if (file_exists($GLOBALS['cfg']['LOG_PATH'] . '/log_all'))
        @unlink($GLOBALS['cfg']['LOG_PATH'] . '/log_all');

      // @file_put_contents($GLOBALS['cfg']['LOG_PATH'] . '/all_var_'.$a_vals["p_id"]."_" .$a_vals["t_id"].'.txt',
      @file_put_contents($GLOBALS['cfg']['LOG_PATH'] . '/all_var.txt',
      date("Y-m-d H:i:s") . " " .  __FILE__ . " " . __LINE__ . " \r\n"
      . '$l_func_str: ' . print_r($l_func_str, true) . " \r\n"
      . '$a_arr: ' . print_r($a_arr, true) . " \r\n"
      . '$a_key: ' . print_r($a_key, true) . " \r\n"
      . '$a_vals: ' . print_r($a_vals, true) . " \r\n"
      . '$actionMap: ' . print_r($actionMap, true) . " \r\n"
      . '$actionError: ' . print_r($actionError, true) . " \r\n"
      . '$request: ' . print_r($request, true) . " \r\n"
      . '$response: ' . print_r($response, true) . " \r\n"
      . '$form: ' . print_r($form, true) . " \r\n"
      . '$get: ' . print_r($get, true) . " \r\n"
      . '$cookie: ' . print_r($cookie, true) . " \r\n"
      . '$l_arr: ' . print_r($l_arr, true) . " \r\n"
      . '============================  fen ge xian  ==========================' . " \r\n"
      , FILE_APPEND);
    }

    if (!function_exists($l_func)) {
      Log::Info("\r\n eval__function: " . $l_func . "\r\n");
      eval($l_func_str);  // 执行加载一下
    }
    return $l_func($a_arr,$a_key,$a_vals,$actionMap,$actionError,$request,$response,$form,$get,$cookie);
  }

  // expr=${所属专题} ne '' || ${所属专题2} ne '' 这样的语句判断其真伪, 需要用到下面的
  // if ( 今日看点 == '今日看点' ) 和 if ('今日看点' == '今日看点' ) 两个表达式均为真。有无引号均可
  function eval_expr($a_expr,$a_key,$a_vals,$a_allow){
    $l_func = preg_replace('/\W/',"_",basename(__FILE__) . "_" .$a_vals["t_id"]."_".$a_key."_".$a_allow."_". utime());

    if (!defined("NEW_LINE_CHAR")) {
      define("NEW_LINE_CHAR","\r\n");
    }
    $l_func_str = NEW_LINE_CHAR . "function ".$l_func.'(){'.NEW_LINE_CHAR;
    $l_func_str .= "if ( $a_expr ) {
      return true;
    }else {
      return false;
    }";
    $l_func_str .= "}";
    if (!function_exists($l_func)) eval($l_func_str);  // 执行加载一下
    return $l_func();
  }
}


function __call_back_addSingleQuote2str($a_str){
  return '\''.$a_str.'\'';
}
function __call_back_addDoubleQuote2str($a_str){
  return '"'.$a_str.'"';
}
function __call_back_addBrace2str($a_str){
  return '{'.$a_str.'}';
}
function __call_back_addDollarBrace2str($a_str){
  return '${'.$a_str.'}';
}
function __call_back_addSingleQuoteDollarBrace2str($a_str){
  return '\'${'.$a_str.'}\'';
}
function __call_back_addDoubleQuoteDollarBrace2str($a_str){
  return '"${'.$a_str.'}"';
}
function __call_back_addDollarBracePRO2str($a_str){
  return '${_PROJECT_'.$a_str.'}';
}
function __call_back_ReplaceValue(&$item1, $key, $prefix){
  $item1 = $prefix[$item1];
}

function __call_back_addslashesSingleQuote2str($a_str){
  return str_replace("'","\'",$a_str);
}
function __call_back_addslashesDoubleQuote2str($a_str){
  return str_replace('"','\"',$a_str);
}

