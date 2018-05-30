<?php
/**
* DAO.cls.php
* @abstract  操作MySQL数据库,希望扩展出两个子类，分别是读写mysql的
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
class DAO{
  // ----------------- public variable -------------- //
  /**
     * 结果常量 : 操作成功
     * @access public
     * @staticvar final
     * @var integer
     */
  //var $MYSQL_SUCCESS = 100;
  //var $MYSQL_ERR_SELECT_DB = 101;
  //var $SELDB_ERR = "选择数据库失败！";
  //var $DISCONN_ERR = "数据库连接已断开！";
  //var $NOREC_ERR = "查询失败！";
  //var $QUERY_ERR = "没有选中记录集！";
  //var $NOMOREREC_ERR = "记录集中没有更多的记录了！";
  // ----------------- private variable ---------------- //
  /**
     * 连接句柄
     * @access private
     * @var resource
     */
  var $connection = null;

  var $isconnection = false;
  /**
     * 当前的schema
     * @access private
     */
  var $currentSchema = null;

  var $assoc = false;

  /**
     * 连接数据库
     * @access public
     * @param string $dbname
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbhost
     * @return object
     */
  function Connect( $dbschema = '', $dbuser = '', $dbpass = '', $dbhost = 'localhost', $dbport = '3306' ){
    // 添加 ":/path/to/socket" 的兼容
    if ("/"==substr(ltrim($dbhost),0,1)||":"==substr(ltrim($dbhost),0,1)||"localhost:"==substr(ltrim($dbhost),0,10)) {
      $this->connection = @mysql_connect($dbhost, $dbuser, $dbpass);
    }else {
      $this->connection = @mysql_connect($dbhost . ':' . $dbport, $dbuser, $dbpass);
    }
    if (  $this->isConnection() ){
      $this->setCharset();  // 立即设置字符编码
      $this->isconnection = true;
      if(""!=$dbschema)$this->setCurrentSchema($dbschema);
    }
    return $this->errorInfo();
  }
  // 如何判断是否连接上的方法, 参数如：$dbo->getConnection()
  function isConnection(){
    $l_info = $this->errorInfo();
    if($l_info[1]==0){
      return true;
    }else {
      return false;
    }
  }
  function errorInfo($error = null){
    if ($this->connection) {
            $native_code = @mysql_errno($this->connection);
            $native_msg  = @mysql_error($this->connection);
        } else {
            $native_code = @mysql_errno();
            $native_msg  = @mysql_error();
        }
    return array($error,$native_code,$native_msg);
  }
  /**
     * 设置当前的 schema
     * @access public
     * @param string  $schema
     * @return boolean
     */
  function SetCurrentSchema( $schema ){
    $this->schema = $schema;
    return @mysql_select_db($this->schema, $this->connection);
  }

  function GetCurrentSchema(){
    return $this->schema;
  }
  // --------------- 很少使用的 public method ----------------- //

  /**
     * 断开连接,在使用mysql_connect函数时使用,
     * 如果用mysql_pconnect连接则无需使用
     * @access public
     * @return boolean
     */
  function Disconnect(){
    return @mysql_close($this ->connection);
  }

  // ------------- assistant method ----------------- //
  /**
    * 将值转换成SQL可读格式
    * @access public
    * @staitc
    * @return string|integer
    */
  function FormatValue( $theValue, $theType=null ,$slashes='gpc' ) {

    if (empty($theType)) $theType = gettype($theValue);

    switch ( $theType ) {
      case "integer":
        $theValue = ($theValue === '') ? "NULL"
        : intval($theValue) ;
        break;
      case "double":
        $theValue = ($theValue != '') ? "'".doubleval($theValue)."'"
        : "NULL";
        break;
      case "string":
        if ($theValue != "NOW()") {
          // 字符串先全部复原
          $theValue = (get_magic_quotes_gpc() || get_magic_quotes_runtime()) ? stripslashes($theValue) : $theValue;

          if (false!==strpos($theValue, "'")) {
            $theValue = str_replace("'","\'",$theValue);  // 只将单引号转义
          }
          $theValue = "'" . $theValue . "'";
        }
        break;
      default :
        $theValue = "NULL";
        break;
    }
    return $theValue;
  }
  /**
    * 格式化SQL成员字段名
    * @access public
    * @static
    * @param string $theField
    * @return string
    */
  function FormatField( $theField ){
    return '`'.$theField.'`';
  }

  /**
   * 设置字符编码
   * @param string $charset
   */
  function setCharset($charset = null){
    if ("" == $charset){
      // 空的时候 包括null
      if ("utf8"==strtolower($GLOBALS['cfg']['db_character'])) mysql_query("set names utf8;");// 数据库字符编码转换问题
      else if("gb2312"==strtolower($GLOBALS['cfg']['db_character'])) mysql_query("set names gbk;");
    }else {
      // 指定的时候
      if (in_array($charset, array("utf8","gbk","latin1"))) mysql_query("set names $charset;");
      else mysql_query("set names latin1;");
    }
  }
}