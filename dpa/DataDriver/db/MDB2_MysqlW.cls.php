<?php
/**
* DAO.cls.php
* @abstract  操作MySQL数据库,希望扩展出两个子类，分别是读写mysql的；不需要格式化外来数据，因为语句中已经有检查,除del操作没检查
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
require_once("configs/MDB2_db.conf.php");
require_once("common/lib/dbhelper.php");
require_once("MDB2.php");

class MysqlW
{
  var $dbo = null;
  var $sql = null;
  var $schema = null;
  var $assoc = false;
  var $isconnectionW = false;  // 仅仅标识是否连接上数据库
  var $connectError = array();// 连接的错误信息
  //var $connectionW = null;

  function __construct($_arr=null, $options=false){
    $this->ConnectW($_arr, $options);
  }
  // for php4
  function MysqlW($_arr=null, $options=false){
    $this->__construct($_arr, $options);
  }

  /**
   * 主库连接
   *
   * @return resource
   */
  function ConnectW($dsn=array(), $options=false){
    $l_name_dsn = DbHelper::FmtDSNAndGetMdb2NameAlias($dsn,'W');

    $this->dbo = &DBO($l_name_dsn['l_name'], $l_name_dsn['dsn'], $options);
    $this->dbo->getConnection();  // 进行连接操作
    // 立即检查是否连接上.因为mysql_errno仅返回最近一次 MySQL 函数的执行(不包括mysql_errno自身)
    if ($this->isConnection()) {
      $this->setCharset();
      $this->isconnectionW = true;
      $l_srv_db_dsn = $this->getDSN("array");
      // 由于数据库切换的时候，mdb2对于同一个主机、端口、用户的连接认为是一个连接，但是数据库却不会自动切换，需要执行use db语句
      if (!empty($l_srv_db_dsn["database"])) {
        $this->SetCurrentSchema($l_srv_db_dsn["database"]);
      }
      //$this->schema = $this->GetCurrentSchema();//如果dsn中有数据库则初始化一下
    }

    //$this->connectError = $this->errorInfo();
  }
  // 如何判断是否连接上的方法, var_dump( $dbo->connection )有两种值int(0) 或 resource(60) of type (mysql link)
  function isConnection(){
    $dbo =& $this->dbo;
    if( $dbo->connection ){
      return true;
    } else {
      return false;
    }
  }
  function errorInfo($error = null){
    $dbo =& $this->dbo;
    return $dbo->errorInfo($error);
  }
  /**
     * 设置当前的 schema
     * @access public
     * @param string  $schema
     * @return boolean
     */
  function SetCurrentSchema($schema ){
    return $this->setDatabase($schema);
  }
  /**
     * 取得当前的 dsn
     * @access  $type string  format to return ("array", "string")
     * @access  $hidepw bool
     * @return string|array
     */
  function getDSN($type = 'array', $hidepw = false){
    $dbo =& $this->dbo;
    return $dbo->getDSN($type, $hidepw);
  }
  /**
     * 插入
     * @param string $sql 插入命令
     * @return boolean
     */
  function &InsertIntoTbl($tablename, $ar){
    if (!is_array($ar)||empty($ar)) {  // 确保$ar为非空数组
      return false;
    }

    $i=0;
    $ziduan = "";
    $vals = "";
    foreach ($ar as $key => $val){
      if ($i > 0){
        $ziduan .= ",";
        $vals .= ","; // 至少第一项以后才能有逗号
      }
      $ziduan .= cString_SQL::FormatField(trim($key));
      $vals   .= cString_SQL::FormatValue(trim($val), 'string');
      $i++;
    }
    $sql = "insert into {$tablename} ( $ziduan ) values ( $vals )";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"201")) echo $sql.NEW_LINE_CHAR;
    $affected =& $this->exec($sql);
    return $affected;
  }
  /**
     * 取得上一步 INSERT 操作产生的 ID
     * @access public
     * @return integer
     */
  function LastID(){
    $dbo =& $this->dbo;
    return $dbo->lastInsertID();
  }
  /**
   * 彻底删除记录，谨慎操作
   *
   * @param string $tablename
   * @param string $condition
   * @param string $limit
   * @return resource|boolean
   */
  function &DeleteData($tablename, $condition, $limit = ""){
    $sql = "delete from {$tablename} $condition $limit ";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"202")) echo $sql.NEW_LINE_CHAR;
    $affected =& $this->exec($sql);
    return $affected;
  }
  /**
   * 更新命令
   * 也可用来--增加计数（也可以用来减，+负数就是了）
   *
   * @param string $tablename
   * @param string $condition
   * @param array $ar
   * @param boolean $bAdd
   * @return boolean
   */
  function &UpdateTableArray($tablename, $ar, $condition, $if_addcount = false){
    if (!is_array($ar)||empty($ar)) {  // 确保$ar为非空数组
      return false;
    }
    $sql = "update {$tablename} set ";
    $sql .= cString_SQL::FmtFieldValArr2Str($ar, ",", $if_addcount);
    $sql .= " where {$condition}";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"203")) echo $sql.NEW_LINE_CHAR;
    $affected =& $this->exec($sql);
    return $affected;
  }

  /**
    * 执行一条查询命令,只能用于update操作
    * @access private
    * @param string $sql sql
    * @return resource|boolean
    */
  function &exec($sql){
    // 事先判断 查询语句是否是 update ，不是就返回false
    $dbo =& $this->dbo;  $this->setCharset();// 兼容php4的做法
    $sql = ltrim($sql);$this->sql = $sql;
    $affected =& $dbo->exec($sql);
    return $affected;
  }
  function &Query($sql){
    // 调用的时候为了兼容其他地方
    $affected =& $this->exec($sql);
    return $affected;
  }
  /**
   * 用于注册，为保证用户唯一性，验证用户是否存在需要在主库进行，而不是去从库查询信息
   *
   * @param string $sql
   * @return unknown
   */
  function Query_master_select($sql){
    // 事先判断 查询语句是否是 update ，不是就返回false
    $sql = ltrim($sql);$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"204")) echo $sql.NEW_LINE_CHAR;

    $dbo =& $this->dbo;$this->setCharset();
    if($this->assoc) {
      $dbo->setOption('portability',MDB2_PORTABILITY_FIX_ASSOC_FIELD_NAMES);
      $dbo->setFetchMode(MDB2_FETCHMODE_ASSOC);
    }
    else $dbo->setFetchMode(MDB2_FETCHMODE_DEFAULT);
    $row = $dbo->queryRow($sql);

    return $row;
  }

  /**
   * 获取sql语句
   *
   * @return string or bool
   */
  function getSQL(){
    return $this->sql;
  }

  function GetCurrentSchema(){
    $dbo =& $this->dbo;
    return $dbo->getDatabase();
  }

  //
  function setDatabase($schema){
    $dbo =& $this->dbo;    // 兼容php4的做法
    $l_rlt = $dbo->query("use ".$schema);
    $dbo->setDatabase($schema);
    $this->schema = $this->GetCurrentSchema();
    return $l_rlt;
  }

  function setCharset($charset = null){
    $dbo =& $this->dbo;
    if ("" == $charset){
      // 空的时候
      if ("utf8"==strtolower($GLOBALS['cfg']['db_character'])) $dbo->setCharset("utf8", $dbo->getConnection());//mysql_query("set names utf8;");// 数据库字符编码转换问题
      else if("gb2312"==strtolower($GLOBALS['cfg']['db_character'])) $dbo->setCharset("gbk", $dbo->getConnection());//mysql_query("set names gbk;");
    }else {
      // 指定的时候
      if (in_array($charset, array("utf8","gbk","latin1"))) $dbo->setCharset($charset, $dbo->getConnection());
      else $dbo->setCharset("latin1", $dbo->getConnection());
    }
  }

  function Disconnect(&$dbo){
    $dbo->disconnect();
  }
  function __destruct(){
    $this->Disconnect($this->dbo);
  }
}