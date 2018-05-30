<?php
require_once('common/lib/cFile.cls.php');
require_once('lang/chinese.utf8.lang.php');

class DbHelper{
  //
  var $ColumnTypes = array(
    "VARCHAR","TINYINT","TEXT","DATE",
    "SMALLINT","MEDIUMINT","INT","BIGINT","FLOAT","DOUBLE","DECIMAL",
    "DATETIME","TIMESTAMP","TIME","YEAR",
    "CHAR","TINYBLOB","TINYTEXT","BLOB","MEDIUMBLOB","MEDIUMTEXT",
    "LONGBLOB","LONGTEXT","ENUM","SET","BIT","BOOL","BINARY","VARBINARY");

  // 常量无法获取
  function get_s_ON_UPDATE_CURRENT_TIMESTAMP() {
    return '@@ON__CURRENT_TIMESTAMP@@';
  }

  // 将数据库project表的数组或mysql_config.ini中数组类型变为dsn字符串
  function getDSNstrByProArrOrIniArr($dsn){
    if (is_array($dsn)) {
      if (array_key_exists("db_pwd", $dsn)) {
        $dsn = "mysql://".$dsn["db_user"].":".$dsn["db_pwd"]."@".$dsn["db_host"].":".$dsn["db_port"]."/".$dsn["db_name"];
      }else if (array_key_exists("db_pass", $dsn)) {
        $dsn = "mysql://".$dsn["db_user"].":".$dsn["db_pass"]."@".$dsn["db_host"].":".$dsn["db_port"]."/".$dsn["db_name"];
      }
    }
    return $dsn;
  }

  /**
   * 拼装 别称 和 用于dbo字符串类型dsn(数组也要转化为字符串)
   * 别称一定要保留原味别称
   *
   * @param array,string $dsn: $dsn可能是数组、可能是mysql://root、也可能就是存在或不存在的别称三种情况
   * @param string $a_type:数据库连接类型,R:读库,W:写库
   * @return unknown
   */
  function FmtDSNAndGetMdb2NameAlias($dsn=array(),$a_type='R'){
    $l_name = "";
    if ('R'!=$a_type) $a_type='W';  // 强制只有两张类型

    if (!is_null($dsn) && !is_array($dsn)) {
      // dsn需要解析 , ???? 需要判断host是否为空
      if (false===strpos($dsn,"://")) {
        $l_name = $dsn;  // 当dsn就是昵称Alias的时候
        // 可能是某个dsn的别名，此时只需要获取到相应的dsn赋值给$dsn即可
        // /path/to/ini:biz_r 这样的，也可能是biz_r这样单串字符，当前只支持单串
        // 依然用默认的mysql_config.ini和路径
        if ('R'==$a_type) {
          __gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'], $GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],"",$dsn);
        }else {
          __gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'], $GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],$dsn,"");
        }
        $dsn = $_SERVER["SRV_DB_DSN_".$a_type];
      }
    }

    $dsn = empty($dsn) ? $_SERVER["SRV_DB_DSN_".$a_type]: $dsn;
    if (empty($dsn)){ echo "dsn error!"; exit;}

    // 兼容性修改, 兼容new dbR($p_arr)的关键字
    if (is_array($dsn)) {
      $dsn = DbHelper::getDSNstrByProArrOrIniArr($dsn);
    }
    // 此时的dsn已经被强制为string类型，并且是dsn串，类似: mysql://username:pass@.....
    // name变成了 localhost:3307:root:db_pass:dpa形式的别名, 当然如果外部强制给出了别名, 则以别名为主
    if (""==$l_name) {
      $l_name = cString::GetAliasByDsnString($dsn,$l_name);
    }

    return array('l_name'=>$l_name,'dsn'=>$dsn);
  }

  public function getAutocreamentDbname($a_proj, $a_f_name="db_name", $a_data=array(), $a_default=''){
    if (''==$a_default) $a_default = $GLOBALS['cfg']['DB_DEFALUT_TYPE'];

    // 如果提交了数据库连接信息$a_data，则需要联合这些信息判断，防止人为没有注册的数据库存在。今后完善之
    $l_p_arr = array(0, 0);  // max必须需要两个参数
    $l_f_name = empty($a_f_name)?"db_name":$a_f_name;
    if(!empty($a_proj)){
      foreach ($a_proj as $p_val){
        if($a_default==substr(strtolower(trim($p_val[$l_f_name])),0,strlen($a_default))){
          $l_p_arr[] = str_replace($a_default,"",strtolower(trim($p_val[$l_f_name]))) * 1;
        }
      }
    }
    $defaul_dbname = (max($l_p_arr)+1);
    $defaul_dbname = str_pad($defaul_dbname, 3, "0", STR_PAD_LEFT);  //将少于3位的1补全为001
    $defaul_dbname = $a_default.$defaul_dbname;
    return $defaul_dbname;
  }

  public function getAutocreamentTBname($a_proj, $a_f_name="Name", $a_data=array(), $a_default=''){
    if (''==$a_default) $a_default = $GLOBALS['cfg']['DB_TB_DEFALUT_TYPE'];
    return DbHelper::getAutocreamentDbname($a_proj, $a_f_name, $a_data, $a_default);
  }

  public function getAutocreamentFieldname($a_proj, $a_f_name="Field", $a_data=array(), $a_default=''){
    if (''==$a_default) $a_default = $GLOBALS['cfg']['DB_FIELD_DEFALUT_TYPE'];
    return DbHelper::getAutocreamentDbname($a_proj, $a_f_name, $a_data, $a_default);
  }

  function getAllDB(&$dbR, $l_no_need_db = array("information_schema", "mysql", "test", "performance_schema")){
    $l_rlt = array();
    $l_tmp = $dbR->SHOW_DATABASES();
    foreach ($l_tmp as $l_arr){
      $l_d_n = trim($l_arr["Database"]);
      // 过滤掉一些默认的表
      if (!in_array($l_d_n, $l_no_need_db)) $l_rlt[] = $l_d_n;
    }

    return $l_rlt;
  }
  /**
   * 创建基本表
   *
   * @param array $data_arr
   * @param string $a_sql 额外的需要执行的sql语句
   * @param sting $db_charset
   */
  public function createDBandBaseTBL($data_arr, $a_sql="", $db_charset="utf8", $source="db", $a_e_wai=true){
    if (!array_key_exists('db_name', $data_arr) && !array_key_exists('db_port', $data_arr)) return ;
    $db_name = $data_arr["db_name"];

    // 在指定的主机上建库
    // 创建数据库的时候不能有数据库，
    unset($data_arr["db_name"]);  // 注销后，数据库连接就不会预先连接数据库了
    $dbW = new DBW($data_arr);
    if( ! $dbW->dbo->connection ){
      $response['html_content'] = " can not connect db! ";
          return null;
    }

    // 此处可以判断一下数据库是否存在，当然放在下面进行判断也行
    $dbR = new DBR($data_arr);
    if( ! $dbR->dbo->connection ){
      // 数据库连接失败后
      $response['html_content'] = " can not connect db! ";
          return null;
    }

    $l_tmp = DbHelper::getAllDB($dbR);
    if ( !in_array($db_name,$l_tmp) ) {
      // 不存在则创建之
      $rlt = $dbW->create_db($db_name, $db_charset);
      $l_err = $dbW->errorInfo();
      if ($l_err[1]>0){
        // 需要进行错误处理，稍后完善???? sql有错误，后面的就不用执行了。
        //echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " _arr:" . var_export($_arr, TRUE);
        return $l_err[2];
      }
    }
    //unset($dbR);

    // 连接上以后,然后才设置数据库, 选择数据库
    $dbW->setDatabase($db_name);
    $data_arr["db_name"] = $db_name;  // 之前注销的变量应当立即复原，后面要用到


    // 依据项目的类型，确定需要建立哪几张基本表
    switch (strtoupper($data_arr["type"])){
      case "PHP_PROJECT":
        $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/cms.sql");

        // 同时创建资源库和基本表
             // $db_name_res = $form["db_name"]."_res";
             $l_e_wai = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/php_project_init.sql");
        break;
      case "CMS":
        $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/cms.sql");

        // 同时创建资源库和基本表
             // $db_name_res = $form["db_name"]."_res";
             $l_e_wai = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/cms_init_insert.sql");
        break;
      case "SYSTEM":
        $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/dpa.sql");

        $l_e_wai = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/dpa_init_insert.sql");

            $data_arr["name_cn"] = convCharacter($GLOBALS['language']['SYSTEM_NAME_STR'],true);
            if (!array_key_exists("id",$data_arr)) {
              $data_arr["id"] = 1;  // 应当自动获取其id，暂时先手动指定
            }
        break;
      case "NORMAL":

        break;
      case "PUB":

        break;
      case "RES":
        // 具备文件目录结构的数据表, 此数据表各项需要重新整理一下
        $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/file_dir_res.sql");
        break;
      case "GRAB":
        $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/grab.sql");

        $l_e_wai = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/grab_init.sql");

        break;
      default:

        break;
    }
    // 如果字段定义表指定在本项目内, 则同时需要创建表定义表和字段定义表

    // 所有的数据库都必须有这两张表
    $a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/table_field.sql");
    //$a_sql .= file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/tmpl_design.sql");

    // 首先创建相应的数据表
    DbHelper::execDbWCreateInsertUpdate($dbW, $a_sql);

    // 如果a_sql中包含table_def和field_def两张表则需要进行自动填充数据的操作
    if (preg_match("/\s+`?(\w*table_def)`?\s+/", $a_sql, $match_tbl) && preg_match("/\s+`?(\w*field_def)`?\s+/", $a_sql, $match_fld))
    {
        $table_def = $match_tbl[1];
        $field_def = $match_fld[1];
        $a_data_arr = array("source"=>$source,"creator"=>($_SESSION["user"]["id"]>0)?$_SESSION["user"]["id"]:1);  // 能在外部增加字段的
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($data_arr);
        $dbR->dbo = &DBO('', $dsn);
        DbHelper::fill_table($dbR,$dbW,$a_data_arr,"all",$field_def,$table_def,$data_arr["id"]);
        DbHelper::fill_field($dbR,$dbW,$a_data_arr,"all",$field_def,$table_def);

        // 作为表定义表的一部分，通常情况下需要进行字段算法更新的
        if ($a_e_wai) {
          $l_e_tmpl = file_get_contents($GLOBALS['cfg']['PATH_RUNTIME']."/DataDriver/sql/tmpl_design_init_insert.sql");
          DbHelper::execDbWCreateInsertUpdate($dbW, $l_e_tmpl,array("INSERT INTO ","REPLACE INTO ","UPDATE "));
        }
    }
    else
    {
      //
    }

    // ------ 如果有额外的初始化数据需要insert或update的时候
    if ($a_e_wai && isset($l_e_wai) && ""!=$l_e_wai) {
      // insert或update一些初始数据
      DbHelper::execDbWCreateInsertUpdate($dbW, $l_e_wai,array("INSERT INTO ","REPLACE INTO ","UPDATE "));
    }


    // 如果重新创建的系统，则需要修改mysql数据库连接信息初始值
    if ("SYSTEM"==strtoupper($data_arr["type"])) {
      cFile::modifyMysqlConfigIniAndLANGConfigFileWhenCreateSYSTEM($data_arr);
    }

    $dbW->Disconnect();  $dbR->Disconnect();// 断开数据库连接
    return ;
  }

  //
  function execDbWCreateInsertUpdate(&$dbW, $a_sql, $a_spe_arr=array("CREATE ","INSERT INTO ","UPDATE ","REPLACE INTO ")){
    // 换另外一种更合理的算法
    $a_sql = cString::lineDelBySpe($a_sql,"--");  // 仅仅去掉行注释
    $a_sql = str_replace('ON UPDATE CURRENT_TIMESTAMP',DbHelper::get_s_ON_UPDATE_CURRENT_TIMESTAMP(),$a_sql);  // 替换掉其中的ON UPDATE CURRENT_TIMESTAMP为特定字符串执行sql的时候然后替换回来，因为里面还有sql关键词'update '

    if (!empty($a_spe_arr)) {
      $l_str = implode("|",$a_spe_arr);
      $l_str = "/($l_str)/i";

      if( preg_match_all($l_str,trim($a_sql),$l_matches,PREG_SET_ORDER) ) {
        $l_arr = preg_split($l_str, $a_sql);  // 正则分割成多块
        //
        foreach ($l_matches as $l_k => $l_v){
          $l_arr[$l_k+1] = $l_v[1] . $l_arr[$l_k+1]; // 字符串补全，还原
        }
      }
    }

    // 然后逐一执行
    foreach ($l_arr as $l_sql) {
      if (""!=trim($l_sql)) {
        $l_sql = str_replace(DbHelper::get_s_ON_UPDATE_CURRENT_TIMESTAMP(),'ON UPDATE CURRENT_TIMESTAMP',$l_sql);  // sql字符串复原
        $dbW->Query($l_sql);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          // 需要进行错误处理，稍后完善???? sql有错误，后面的就不用执行了。
          echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . "sql: $l_sql,  _arr:" . var_export($l_err, TRUE);
          exit;
          return $l_err[2];
        }
      }
    }
  }

  /**
   * 各个项目自动检测
   * 数据表中可以为空的可以不必填写
   * 数据表中不能为空的必须填写，没有填写的采用默认值，没有默认值的一定要填写，否则返回错误。
   *
   * @param array $form  提交的表单数组
   * @param array $f_info  字段数组
   * @param bool $with_null 是否携带空值, 修改的时候需要修带，而增加的时候无需空值. 默认全带
   * @param bool $if_edit 是否编辑数据, 编辑数据的时候，一些必须字段如果并不存在于form数组中，则认为该字段不做修改
   * @param $_FILES $a_files 上传文件，图片和其他文件
   * @return array    结果数组, 也可能是错误信息
   */
  function getInsertArrByFormFieldInfo($form, $f_info, $with_null=true, $if_edit=false, $a_files=array()){
    $data_arr = array();

    // 图片上传处理 begin  （TODO 目前只做图片，附件以后做）
    $upload_result = upload_imgs($a_files, $GLOBALS['cfg']['IMG_UPLOAD_PATH'], $GLOBALS['cfg']['IMG_URL_PRE']);
    if (isset($upload_result['url']) && !empty($upload_result['url'])) {
      // 有图片上传, 需要将相应字段填充上返回的数值
      foreach ($upload_result['url'] as $l_up_field => $img_url) {
        $l__field_name = substr($l_up_field, strlen($GLOBALS['cfg']['UPLOADIMG_PRE'])); // 去掉前缀
        // 检查字段是否存在于现有字段中
        if (array_key_exists($l__field_name, $f_info)) {
          // 如果强制使用上传文件，则覆盖form中的图片路径
          if (1 == $form[$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'] . $l__field_name] || '' == trim($form[$l__field_name])) {
            $form[$l__field_name] = $img_url;
          }
        }
      }
    }
    // 图片上传处理 end

    //
    foreach ($f_info as $l_arr){
      // 给出的字段可能是中文、英文两种可能
      if (array_key_exists($l_arr["name_cn"], $form)) {
        $form_d = $form[$l_arr["name_cn"]];
        $form[$l_arr["name_eng"]] = $form_d;// 用英文字段替换掉中文字段
        // unset($form[$l_arr["name_cn"]]);  // 可以去掉中文的字段
      }
      if (!array_key_exists($l_arr["name_eng"], $form)) {
        // 如果form中不存在该字段，要么赋空值，要么就跳过--编辑数据的时候可以不用包含全部数据
        if ($if_edit) continue;
        $form_d = null;
      }else {
        $form_d = $form[$l_arr["name_eng"]];  // 不能使用trim, 需要保持提交数据的原始性
      }

      // 如果有算法得到的数值
      if ( array_key_exists(Parse_Arithmetic::getArithmetic_Result_str(),$l_arr) ) {
        if ( array_key_exists("value",$l_arr[Parse_Arithmetic::getArithmetic_Result_str()]) ) {
          $form_d = $l_arr[Parse_Arithmetic::getArithmetic_Result_str()]["value"];
        }else if (array_key_exists("method2val",$l_arr[Parse_Arithmetic::getArithmetic_Result_str()])) {
          if ("Form::Password"==$l_arr['f_type'] && strlen($form_d)>=32) {
            // 如果是密码类型的，当长度大于30的时候，认为是已经加密过的，则无需进行重复加密算法
          }else {
          $l_func = $l_arr[Parse_Arithmetic::getArithmetic_Result_str()]["method2val"];
          $form_d = $l_func($form_d);
          }
        }
      }

      if (""===$form_d || is_null($form_d)) { // 千万不要用empty进行判断，因为0也是empty
        if ("NO"==strtoupper( $l_arr["is_null"] )) {
          // 不为空的字段必须填写或采用默认值
          if (""!=($l_arr["default"]) && "CURRENT_TIMESTAMP"!=strtoupper(trim($l_arr["default"]))) {
            // not null类型的默认值不可能为null，因此只需要判断""即可 剔除特殊情况默认 CURRENT_TIMESTAMP
            $data_arr[$l_arr["name_eng"]] = $l_arr["default"];
          }else if ( "ENUM"==strtoupper(trim($l_arr["type"])) ){
            if (is_array($l_arr["length"])) {
              // 数组中找不到空值则说明有错
              if (!in_array("", $l_arr["length"])) {
                $data_arr["___ERR___"][] = $l_arr["name_eng"];
              }
            }else {
              // 枚举串中找不到空值则说明有错
              if ( false===strpos( $l_arr["length"], "''")) {
                $data_arr["___ERR___"][] = $l_arr["name_eng"];
              }
            }
            // 如果是枚举型，并且有一个''的枚举项，不填写数据也不需要写该字段sql也能入库。
            //$data_arr[$l_arr["name_eng"]] = '';  // 是否赋值无关紧要,sql没有此字段也能入库。
          }else {
            // 自增和时间类型的可以不用填写, 否则返回一个错误
            if ("auto_increment"!=strtolower(trim($l_arr["extra"])) && "timestamp" != strtolower(trim($l_arr["type"]))) {
              $data_arr["___ERR___"][] = $l_arr["name_eng"];
            }
          }
        }else {
          // 可以赋值为空，因为修改数据的时候，从有到无进行修改的时候就需要这个字段，否则会丢失此字段
          // 另一方法，也可能是后台发布，没有经过前端算法进行赋值的，也能运行到此步骤
          if ($with_null) {
            if (''===$form_d) $form_d = null;
            $data_arr[$l_arr["name_eng"]] = $form_d;
          }
        }
      }else {
        // 添加或修改数据的时候，先进行还原数据, 保证数据在入库之前先要进行htmlspecialchars一下
        // 实际测试中发现不需要 htmlspecialchars_decode($form_d) 这样，也许有其他地方进行了转换。以后研究????
        $data_arr[$l_arr["name_eng"]] = $form_d;
      }
    }

         return $data_arr;
  }

  // 自动填充 table_def 表
  function fill_table(&$dbR, &$dbW, $data_arr, $tbl_name="all", $f_def="field_def", $t_def="table_def", $p_id=0, $no_table=array()){
    $if_repair = true;
    if (""!=$tbl_name) {
      // 先获取所有的表
      $all_table = $dbR->getDBTbls();
      if (PEAR::isError($all_table)){
            echo var_export($dbR->errorInfo(), true). " error sql:" .$dbR->getSQL() ." FILE:".__FILE__." LINE:".__LINE__.NEW_LINE_CHAR;
            return null;
          }
      //if ("all"==$tbl_name) {
      // 循环插入
      if (!empty($all_table)){
          foreach ($all_table as $l_table){
            // 如果是特定的或者是全部则允许通过
            if ($l_table["Name"] == $tbl_name || "all"==$tbl_name) {
            if (!in_array($l_table["Name"],$no_table)) {
              $l_comment = trim($l_table["Comment"]);
              if(""!=$l_comment){
                $l_data_arr = array("description"=>$l_comment);// 表的注释部分写入描述字段中去
              }else{
                $l_data_arr = array();
              }
              DbHelper::ins2table_def($dbR,$dbW,array_merge($l_data_arr,$data_arr), $l_table["Name"], $f_def, $t_def, $p_id);
            }
            }
          }
          // end

          if ($if_repair) {
            // 还需要进行修复, 对于废弃的表需要删除或改为废弃状态，当前直接删除
            // 对于在字段定义表中属于多余表的那些字段，统统删除
            $dbR->table_name = $t_def;
            $l_old_tbls = $dbR->getAlls("where p_id = $p_id and status_='use' order by id");
            $l_old_tbls = cArray::Index2KeyArr($l_old_tbls, array("key"=>"name_eng", "value"=>"name_eng"));
            $all_table = cArray::Index2KeyArr($all_table, array("key"=>"Name", "value"=>"Name"));
            $l_duo = array_diff($l_old_tbls,$all_table);  // 在old中，但不在实际的表结构中
            // 多出的字段需要删除或修改为废弃状态
            foreach ($l_duo as $l_tbl){
              $l_row = $dbR->getOne("where p_id = $p_id and name_eng = '".$l_tbl."' and status_='use' ");

              if (!empty($l_row)) {
                $dbW->table_name = $t_def;
                //$dbW->delOne(array("id"=>$l_row["id"]),"id");
                $dbW->updateOne(array('status_'=>'del'), "id=".$l_row["id"]);
                // 同时还需要删除字段定义表中的该表的所有字段
                $dbW->table_name = $f_def;
                //$dbW->delOne(array("t_id"=>$l_row["id"]),"t_id");
                $dbW->updateOne(array('status_'=>'del'), "t_id=".$l_row["id"]);
              }
            }
          }
      }
    }
    return null;
  }

  // 往表定义表中插入数据
  function ins2table_def(&$dbR, &$dbW, $a_data_arr, $a_tablename, $f_def="field_def", $t_def="table_def",$p_id=0){
    $name_eng = $a_tablename;   //
    $name_cn = $name_eng;      // 暂时用英文的或者用表注释
    if(isset($a_data_arr["description"]) && ""!=$a_data_arr["description"]) $name_cn = $a_data_arr["description"];

    $dbW->table_name = $t_def;
    if($dbW->getExistorNot("name_eng='".$name_eng."'")){
      // 表如果存在是否需要进行修复???? 对于tpl_type等的修改基于什么呢？如何获取这样的数据呢？
      // 暂时先不更新存在中的数据表
    } else {
      // 不存在则插入数据库中
      $data_arr = array(
        "p_id"        => $p_id,
        "field_def_table"=> $f_def,
        "creator"     => convCharacter($_SESSION["user"]["id"],true),
        "createdate"    => date("Y-m-d"),
        "createtime"    => date("H:i:s"),
        "name_eng"     => trim($name_eng),
        "name_cn"     => convCharacter($name_cn,true)
      );
      $data_arr = array_merge($data_arr,$a_data_arr);  // 外面给出的数据可修改里面的参数
      if ($dbW->insertOne($data_arr)) {
        $last_id = $dbW->LastID();
      }else {
        echo $dbW->getSQL();
        echo "insert error!";
        print_r($dbW->errorInfo());
      }
    }
    usleep(300);
    return $dbW->errorInfo();
  }

  // 自动填充 field_def 表
  function fill_field(&$dbR, &$dbW, $data_arr, $want_tbl="all", $f_def="field_def", $t_def="table_def", $if_repair=true){
    // 先获取表
    if (""!=$want_tbl) {

        // 自动完成所有表的导入，包括自身也需要导入
        $dbR->table_name = $t_def;
        if ("all"==$want_tbl) {
          $_tbls = $dbR->getAlls();
        }else {
          $_tbl = $dbR->getOne("where name_eng = '$want_tbl'");
          $_tbls = array($_tbl);
        }
        // 需要for 循环
        foreach ($_tbls as $_tbl){
          if ($_tbl["id"]>0) {
            DbHelper::ins2field_def($dbR,$dbW,$data_arr,$_tbl["id"],$f_def,$t_def,$if_repair);
          }
        }

    }
  }

  // 往字段定义表中插入数据
  function ins2field_def(&$dbR, &$dbW, $a_data_arr, $t_id=17, $f_def="field_def", $t_def="table_def",$if_repair=true,$delete_duo_field=false){
    // 在 table_def 中的 id
    $dbR->table_name = $t_def;
    $_tbl_name = $dbR->getOne("where id = $t_id");
    $all_field = $dbR->getTblFields($_tbl_name["name_eng"]);

    // 获取数组的维度
    $l_depth=cString_num::get_array_depth($a_data_arr);

    $dbW->table_name = $f_def;
    // 循环插入
    if (!empty($all_field)) {
    foreach ($all_field as $l_arr){
      $name_eng   = strtolower($l_arr["Field"]);   // 很特殊的key Tables_in_auto
      $name_cn   = $name_eng;     // 暂时用英文的
      $l_jiben = DbHelper::getFieldDefBixu($l_arr);
      if (false!==strpos($l_jiben["type"],"text")) $l_jiben["f_type"] = "Form::TextArea";  // 增加一个数据

      // 如果字段有Comment信息，则将中文名替换为描述的前部分
      if (array_key_exists("Comment",$l_arr)) {
        $l_comment = trim($l_arr["Comment"]);
        if(""!=$l_comment){
          $l_jiben["description"] = $l_comment;// 字段的注释部分写入描述字段中去
          // 同时将中文名使用描述信息的前半部分
          $l_tmp = preg_split("/[,.:; ]/",$l_comment,-1,PREG_SPLIT_NO_EMPTY);// 暂时只能用半角符号。测试发现全角标点符号，。：；经常匹配出乱码，导致入库sql报错而影响入库
          if (""!=trim($l_tmp[0])) $name_cn = trim($l_tmp[0]);
        }
      }

      if($l_db_row = $dbW->getExistorNot("t_id = $t_id  and name_eng='".$name_eng."' and status_ = 'use' ")){
        // 进行修复，只修改同表结构相关的字段
        if ($if_repair) {
          $data_arr = array(
            "mender"     => convCharacter($_SESSION["user"]["id"],true),
            "menddate"    => date("Y-m-d"),
            "mendtime"      => date("H:i:s"),
            "name_cn"     => $name_cn
          );
          $data_arr = array_merge($data_arr,$l_jiben);
          $dbW->table_name = $f_def;
          $dbW->updateOne($data_arr, "id=".$l_db_row["id"]);
          $l_err = $dbW->errorInfo();
          if ($l_err[1]>0){
            // 需要进行错误处理，稍后完善???? sql有错误，后面的就不用执行了。
            echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " sql: ". $dbW->getSQL() ." _err:" . var_export($l_err, TRUE);
            //
          }
        }
        //echo "t_id = $t_id  and name_eng='".$name_eng."' exist!".NEW_LINE_CHAR;
        continue;
      } else {
        // 不存在则插入数据库中
        $data_arr = array(
          "creator"     => convCharacter($_SESSION["user"]["id"],true),
          "createdate"    => date("Y-m-d"),
          "createtime"    => date("H:i:s"),
          "t_id"        => $t_id,
          "name_eng"     => trim($name_eng),
          "name_cn"     => $name_cn
        );
        $l_data_arr = array();
        if (array_key_exists($name_eng,$a_data_arr) && is_array($a_data_arr[$name_eng])) {
          $l_data_arr = $a_data_arr[$name_eng];
        }else {
          if (1==$l_depth) $l_data_arr = $a_data_arr;
        }

        $data_arr = array_merge($data_arr,$l_jiben,$l_data_arr);  // 外面给出的数据可修改里面的参数
        $dbW->insertOne($data_arr);
        $l_err = $dbW->errorInfo();
        if ($l_err[1]>0){
          // 需要进行错误处理，稍后完善???? sql有错误，后面的就不用执行了。
          echo "\r\n".  date("Y-m-d H:i:s") . " FILE: ".__FILE__." ". " FUNCTION: ".__FUNCTION__." Line: ". __LINE__."\n" . " sql: ". $dbW->getSQL() ." _err:" . var_export($l_err, TRUE);
          //
        }else {
          $last_id = $dbW->LastID();
        }
      }
      usleep(300);
    }
    }
    // 还需要进行修复, 对于废弃的字段需要删除或改为废弃状态，当前直接删除，可以准备着update语句
    //  万万不能进行自动的更新或删除。这个只是一时使用，长远的根本不需要这样做。
    /*if ($delete_duo_field) {
      $dbR->table_name = $f_def;
      $l_old_fields = $dbR->getAlls("where t_id = $t_id order by id");
      $l_old_fields = cArray::Index2KeyArr($l_old_fields, $a_val=array("key"=>"name_eng", "value"=>"name_eng"));
      $all_field = cArray::Index2KeyArr($all_field, $a_val=array("key"=>"Field", "value"=>"Field"));
      $l_duo = array_diff($l_old_fields,$all_field);  // 在old中，但不在实际的表结构中
      // 多出的字段需要删除或修改为废弃状态
      foreach ($l_duo as $l_f){
        $l_row = $dbR->getOne("where t_id = $t_id and name_eng = '".$l_f."' and status_='use' ");

        if (!empty($l_row)) {
          $dbW->table_name = $f_def;
          //$dbW->delOne(array("id"=>$l_row["id"]),$id_ziduan="id");
          // 如果只是更新，则可以修改状态，用下面的语句
          //$dbW->updateOne(array('status_'=>'del'), "id=".$l_row["id"]);
        }
      }
    }*/
  }

  // 通过父级id数组，逐级获取并最终获取到最后一级的数据
  function getProTblFldArr(&$dbR, $a_data, $a_p_self_ids=array()){
    $l_rlt = array();  // 返回所有级别的数组，并以

    if (empty($a_p_self_ids) || !is_array($a_p_self_ids)) {
      return $l_rlt;
    }

    $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
    $dbR->dbo = &DBO($l_name0_r);
    $l_srv_db_dsn = $dbR->getDSN("array");
    if (!empty($l_srv_db_dsn["database"])) $dbR->SetCurrentSchema($l_srv_db_dsn["database"]);
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      echo date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      return $l_rlt;
    }

    // 父级id都是1开始, 加上自身，因此至少有一项
    // 先手动写，以后完善之???? foreach去掉了对$a_p_self_ids索引的依赖
    $i=1;
    foreach ($a_p_self_ids as $l_p_self_id){
      // 数组中必须包含此字段的数据，否则跳出本来循环
      if (empty($a_data[$l_p_self_id["ziduan"]])) {
        continue;
      }

      if (1==$i) {
        // 逐级循环, 第一级是项目表，包含了数据库连接信息
        $l_tbl_name = (empty($l_p_self_id["table_name"]))?TABLENAME_PREF."project":$l_p_self_id["table_name"];
        $dbR->table_name = $l_tbl_name;
        // 都是id，整型数据，因此无需引号

        $l_p_s1 = $dbR->getOne(" where id=".$a_data[$l_p_self_id["ziduan"]]);
        $l_rlt["p_def"] = $l_p_s1;


        // 该项目下所有的表
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($l_p_s1);
        $dbR->dbo = &DBO('', $dsn);
        $dbR->SetCurrentSchema($l_p_s1['db_name']);
        $dbR->table_name = empty($l_p_self_id["t_table_name"]) ? TABLENAME_PREF."table_def":$l_p_self_id["t_table_name"];
        $l_t_all = $dbR->getAlls(" where status_!='stop'", 'id, name_eng, name_cn');
        if (!PEAR::isError($l_t_all)) {
              if (!empty($l_t_all)) $l_rlt["t_all_"] = $l_t_all;
            }

        /*$dbR->table_name = $l_p_self_id["table_name"];
        $l_p_s1 = $dbR->getOne(" where ".$l_p_self_id["ziduan"]." = ".$a_data[$l_p_self_id["ziduan"]]);
        $l_rlt[$l_p_self_id["ziduan"]] = $l_p_s1;*/
      }else if (2==$i) {
        // 在此处会存在两种可能：1 表定义表；2 该表的文档列表
        // 某个模板id，即数据库下的哪张表，应该去访问表定义表
        $dsn = DbHelper::getDSNstrByProArrOrIniArr($l_p_s1);
        $dbR->dbo = &DBO('', $dsn);
        $dbR->SetCurrentSchema($l_p_s1['db_name']);
        //$dbR = null;$dbR = new DBR($l_p_s1);  // 涉及到数据库重连的问题，包含了数据库连接信息
        $dbR->table_name = $l_tbl_name = empty($l_p_self_id["table_name"]) ? TABLENAME_PREF."table_def":$l_p_self_id["table_name"]; // 表定义表的数据必须获取到
        $l_t_def_arr = $dbR->getOne(" where id = ".$a_data[$l_p_self_id["ziduan"]]);  // 在没有$a_p_self_ids设置的情况下也能获取到数据
        $l_rlt["t_def"] = $l_t_def_arr;

        // 需要将表级别的信息也一同获取到，例如表模板设计表的数据也需要一同获取到其信息
        $l_real_tbls = $dbR->getDBTbls();
             $l_real_tbls = cArray::Index2KeyArr($l_real_tbls,array("key"=>"Name","value"=>"Name"));
             $l_tmpl_design = TABLENAME_PREF . "tmpl_design";
             if (array_key_exists($l_tmpl_design,$l_real_tbls)) {
               $dbR->table_name = $l_tmpl_design;
               $l_tmpl_design = $dbR->getAlls("where tbl_id=".$a_data[$l_p_self_id["ziduan"]]." and status_='use'");
          if (!empty($l_tmpl_design))$l_rlt["t_def"]["tmpl_design"] = $l_tmpl_design;
             }

        // 如果指定了字段，或者不获取的字段则需要进行范围确定
        $l_f_range = "";
        if (array_key_exists("nof",$a_data)) {
          require_once("common/lib/cString.cls.php");
          $l_nof = cString_SQL::decodestr2sql($a_data["nof"]);
          if(""!=$l_nof) $l_f_range = " and name_eng not in ($l_nof)";
        }else if (array_key_exists("fid",$a_data)) {
          require_once("common/lib/cString.cls.php");
          $l_fid = cString_SQL::decodestr2sql($a_data["fid"]);
          if(""!=$l_fid) $l_f_range = " and name_eng in ($l_fid)";
        }

        // 同时获取该表所有字段定义信息, 多行数据
        $dbR->table_name = empty($l_p_self_id["table_name2"]) ? TABLENAME_PREF."field_def":$l_p_self_id["table_name2"]; // 字段定义表的数据必须获取到
        $l_f_def_tbl = $dbR->getAlls(" where t_id = ".$a_data[$l_p_self_id["ziduan"]] . $l_f_range. " order by list_order asc,id asc ");  // 字段定义表中的定义. 在没有$a_p_self_ids设置的情况下也能获取到数据
        $l_f_def_real= DbHelper::getFieldInfoByTbl($dbR, $l_t_def_arr["name_eng"]); // 实际、真实的表获取
        // 同时获取实际表(真实表,实际的表,真实的表)表结构中的字段信息,然后组合成完整的信息
        // 兼容以前的必须保留之前的字段
        $l_tmp_arr = DbHelper::BaseReplaceDuo($l_f_def_real, $l_f_def_tbl, DbHelper::getField7Attribute());
        $l_rlt["f_info"] = $l_tmp_arr[0];
        $l_rlt["f_def_duo"] = $l_tmp_arr[1];
        $l_rlt["f_def_stop"] = $l_tmp_arr[2];
        unset($l_tmp_arr);unset($l_f_def_tbl);unset($l_f_def_real);
        // 返回两个区别对待的数组

        // 涉及到分页， 应当在具体的业务中进行

      }else if (3==$i){
        // 具体某张表中的具体数据了，不过先需要去字段定义表中获取到字段的算法以后，再才从具体表中获取数据
        $l_tmp_tbl = empty($l_rlt["t_def"]["name_eng"])? TABLENAME_PREF."field_def" : $l_rlt["t_def"]["name_eng"];
        $dbR->table_name = empty($l_p_self_id["table_name"]) ? $l_tmp_tbl : $l_p_self_id["table_name"]; // 字段定义表
        $l_f_def_arr = $dbR->getOne(" where id=".$a_data[$l_p_self_id["ziduan"]]);
        $l_rlt["f_data"] = $l_f_def_arr;  // 非表定义表字段定义表中具体的单行数据


      }else {
        // 更多级别以后完善之

      }

      $i++;
    }

    return $l_rlt;
  }

  // 将字段定义表中的数据依据实际表的字段信息进行替换并返回实际表组成的.内部使用
  function BaseReplaceDuo($a_base_arr, $a_mult_arr, $a_base_replace_mult_key){
    // 数字索引变为字段索引
    $l_base_tmp = cArray::Index2KeyArr($a_base_arr, array("key"=>"name_eng", "value"=>array()));
    $l_mult_arr = cArray::Index2KeyArr($a_mult_arr, array("key"=>"name_eng", "value"=>array()));

    $l_base_arr  = array();  // 按照字段定义表中的顺序重新组织基本字段数组
    $l_stop_arr = array();  // 停用的字段
    $l_duo_arr  = array();  // 数值的差

    // 将多字段的数组中的部分字段替换为base中的字段数值
    if(!empty($l_mult_arr)){
      foreach ($l_mult_arr as $l_f=>$l_val){
        // 先替换字段定义表中的很多字段
        if ("use"!=$l_val["status_"]) {
          $l_stop_arr[$l_f] = $l_val;
        }else if (array_key_exists($l_f,$l_base_tmp)) {
          // 字段也在真实表中，则需要进行替换工作
          if (is_array($a_base_replace_mult_key)) {
            // 基于base数组进行值替换
            foreach ($a_base_replace_mult_key as $l_k){
              $l_mult_arr[$l_f][$l_k] = $l_base_tmp[$l_f][$l_k];
            }
          }
          // 同时也需要重组基本表, 相当于增加了字段，中文名也一同给替换了
          $l_base_arr[$l_f] = $l_mult_arr[$l_f];
        }else {
          $l_duo_arr[$l_f] = $l_val;
        }
      }
    }
    return array($l_base_arr,$l_duo_arr,$l_stop_arr);
  }
  // 字段定义表中的字段七大基本属性字段
  function getField7Attribute(){
    return array("is_null","key","extra","type","length","attribute","default");
  }
  // 获取并分解表字段的7大特征属性：Null,Key,Extra,type,length,attribute,Default
  function getFieldDefBixu($l_arr){
    $data_arr = array();
    if (array_key_exists("Field", $l_arr)) {
      $_type_all   = DbHelper::explode_type_length_attribute($l_arr);
      $data_arr = array(
        "is_null"      => $l_arr["Null"],
        "key"        => $l_arr["Key"],
        "extra"        => $l_arr["Extra"],
        "type"        => $_type_all["type"],
        "length"      => $_type_all["length"],
        "attribute"      => $_type_all["attribute"],
        "default"      => convCharacter($l_arr["Default"],true)
      );
    }

    return $data_arr;
  }

  // 依据数据表英文名，获取到字段全部信息，类似field_def中的字段
  function getFieldInfoByTbl(&$dbR, $tbl_name_eng, $a_field_def_arr=array()){
    $l_rlt = array();

    // 先获取字段定义表的数据, 只需要其中中文名称即可
    if(!empty($a_field_def_arr)) $l_name_arr = cArray::Index2KeyArr($a_field_def_arr, array("key"=>"name_eng", "value"=>"name_cn"));
    else $l_name_arr = array();

    $all_field = $dbR->getTblFields($tbl_name_eng);

    // 获取不同字段
    if (!empty($all_field)) {
      foreach ($all_field as $l_arr){
        $name_eng   = strtolower($l_arr["Field"]);   // 很特殊的key Tables_in_auto
        $name_cn   = array_key_exists($name_eng,$l_name_arr)?$l_name_arr[$name_eng]:$name_eng;  // 中文名不存在则用英文名
        $l_jiben = DbHelper::getFieldDefBixu($l_arr);

        $data_arr = array(
          "name_eng"     => trim($name_eng),
          "name_cn"     => convCharacter($name_cn,true)
        );
        $data_arr = array_merge($data_arr,$l_jiben);
        $l_rlt[] = $data_arr;
      }
    }

    //
    return $l_rlt;
  }

  /**
   * 在数据库不为空，并且没有默认值的非自增字段
   * 可以放在判断是否执行提交操作的地方
   * 可以用于validate类里面去做自动的校验，当然也可以人工写
   *
   * @param obj $dbR 数据库连接对象
   * @param array $a_arr  预留的，可以输入很多参数的数组
   * @return array
   */
  function getBiXuFields(&$dbR, $a_arr=array()){
    $l_rlt = array();

    //
    if (array_key_exists("f_info", $a_arr) && !empty($a_arr["f_info"])) {
      foreach ($a_arr["f_info"] as $l_f_arr) {
        if ("NO"==strtoupper($l_f_arr["is_null"]) && ""==trim($l_f_arr["default"])) {
          $l_rlt[] = trim($l_f_arr["name_eng"]);

          // 只需要返回一个
          return $l_rlt;
        }
      }
    }

    // 获取表名, 进行相关字段的判断过程类似上面。以后完善之????
    /*if(array_key_exists("table_name", $a_arr)) {
      $dbR->table_name = $a_arr["table_name"];
    }*/

    // 获取数据表结构

    return $l_rlt;
  }




  //define("PMA_MYSQL_INT_VERSION",51000);
  function explode_type_length_attribute(&$row){
    $type             = $row['Type'];
    $type_and_length  = DbHelper::PMA_extract_type_length($row['Type']);

    // reformat mysql query output - staybyte
    // loic1: set or enum types: slashes single quotes inside options
    if (preg_match('@^(set|enum)\((.+)\)$@i', $type, $tmp)) {
      $tmp[2]      = substr(preg_replace('@([^,])\'\'@', '\\1\\\'', ',' . $tmp[2]), 1);
      $type         = $tmp[1] . '(' . str_replace(',', ', ', $tmp[2]) . ')';
          $type         = htmlspecialchars($type);  // for the case ENUM('&#8211;','&ldquo;')
      $binary       = 0;
      $unsigned     = 0;
      $zerofill     = 0;
      $timestamp    = 0;
    } else {
      // strip the "BINARY" attribute, except if we find "BINARY(" because
      // this would be a BINARY or VARBINARY field type
      if (!preg_match('@BINARY[\(]@i', $type)) {
        $type     = preg_replace('@BINARY@i', '', $type);
      }
      $type         = preg_replace('@ZEROFILL@i', '', $type);
      $type         = preg_replace('@UNSIGNED@i', '', $type);
      if (empty($type)) {
        $type     = ' ';
      }

      if (!preg_match('@BINARY[\(]@i', $row['Type'])) {
        $binary           = stristr($row['Type'], 'blob') || stristr($row['Type'], 'binary');
      } else {
        $binary           = false;
      }

      $unsigned     = stristr($row['Type'], 'unsigned');
      $zerofill     = stristr($row['Type'], 'zerofill');
      $timestamp    = ("timestamp"==$row['Type'])?true:false;
    }

    $attribute     = ' ';
    if ($binary) {
      $attribute = 'BINARY';
    }
    if ($unsigned) {
      $attribute = 'UNSIGNED';
    }
    if ($zerofill) {
      $attribute = 'UNSIGNED ZEROFILL';
    }
    // MySQL 4.1.2+ TIMESTAMP options
    // (if on_update_current_timestamp is set, then it's TRUE)
    if ($timestamp) {
      $attribute = 'ON UPDATE CURRENT_TIMESTAMP';
    }

    if (""==trim($row['Default'])) {
      if ($row['Null'] == 'YES') {
        $row['Default'] = 'NULL';
      }
    }

    if ($type_and_length['type'] == 'bit') {
      $row['Default'] = DbHelper::PMA_printable_bit_value($row['Default'], $type_and_length['length']);
    }
    return array_merge($type_and_length,array("attribute"=>$attribute));
  }
  /**
   * Converts a bit value to printable format;
   * in MySQL a BIT field can be from 1 to 64 bits so we need this
   * function because in PHP, decbin() supports only 32 bits
   *
   * @uses    ceil()
   * @uses    decbin()
   * @uses    ord()
   * @uses    substr()
   * @uses    sprintf()
   * @param   numeric $value coming from a BIT field
   * @param   integer $length
   * @return  string  the printable value
   */
  function PMA_printable_bit_value($value, $length) {
    $printable = '';
    for ($i = 0; $i < ceil($length / 8); $i++) {
      $printable .= sprintf('%08d', decbin(ord(substr($value, $i, 1))));
    }
    $printable = substr($printable, -$length);
    return $printable;
  }
  /**
   * Extracts the true field type and length from a field type spec
   *
   * @uses    strpos()
   * @uses    chop()
   * @uses    substr()
   * @param   string $fieldspec
   * @return  array associative array containing the type and length
   */
  function PMA_extract_type_length($fieldspec) {
    $first_bracket_pos = strpos($fieldspec, '(');
    if ($first_bracket_pos) {
      $length = chop(substr($fieldspec, $first_bracket_pos + 1, (strpos($fieldspec, ')') - $first_bracket_pos - 1)));
      $type = chop(substr($fieldspec, 0, $first_bracket_pos));
    } else {
      $type = $fieldspec;
      $length = '';
    }
    return array(
      'type' => $type,
      'length' => $length
    );
  }

}