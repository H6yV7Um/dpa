<?php
/**
* MysqlR.cls.php
* @abstract  只负责从从库中读取数据，没有任何写，更新，删除操作,每一外来参数都需要额外的格式化一下，防止sql注入漏洞。只需调用一下 FormatValue 即可
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
require_once("configs/db.conf.php");
require_once("DataDriver/db/DAO.cls.php");

class MysqlR extends DAO {
  var $sql = null;
  /**
   * 程序运行的整个生命周期只用一个mysqlR连接
   *
   * @var unknown_type
   */
  var $connectionR = null;
  /**
   * 从库连接
   *
   * @return resource
   */
  function ConnectR($dbname = '', $dbuser = '', $dbpass = '', $dbhost = 'localhost', $dbport = '3306'){
    if (""!=$dbname) {
      $temp_con = parent::Connect($dbname, $dbuser, $dbpass, $dbhost, $dbport);
    }else{
      $temp_con = parent::Connect($_SERVER["SRV_DB_NAME_R"],
      $_SERVER["SRV_DB_USER_R"],$_SERVER["SRV_DB_PASS_R"],
      $_SERVER["SRV_DB_HOST_R"],$_SERVER["SRV_DB_PORT_R"]);
    }
    $this->connectionR = $this->connection;
    return $temp_con;
  }
  // 预留此方法，虽然可以继承
  function errorInfo($error = null){
    return parent::errorInfo($error);
  }
  /**
     * 取得当前的 schema
     * @access public
     * @return string
     */
  function GetCurrentSchema(){
    return $this->currentSchema;
  }
  /**
     * 取一个值
     * @access public
     * @param string $sql example : select field_a from table_a Limit 1
     * @return mixed
     */
  function GetOne($sql){
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"101")) echo $sql.NEW_LINE_CHAR;
    $this->sql = $sql;
    $row = $this->GetRow($sql);
    return $row[0];
  }
  /**
     * 取一行(一维数组)
     * @access public
     * @param string $sql example : select field_a from table_a Limit 1
     * @return array| false
     */
  function GetRow($sql){
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"102")) echo $sql.NEW_LINE_CHAR;
    $this->sql = $sql;
    $rs = $this->Query($sql,$this->connectionR);
    $row = $this->fa($rs);
    @mysql_free_Result($rs);
    return $row;
  }
  /**
     * 取一列(一维数组)
     * @access public
     * @param string $sql sql语句
     * @return array
     */
  function GetCol($sql){
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"103")) echo $sql.NEW_LINE_CHAR;
    $this->sql = $sql;
    $rs = $this->Query($sql);
    $data = array();
    while( ($row = $this->fa($rs)) != false ){
      $data[] = $row[0];
    }
    @mysql_free_Result($rs);
    return $data;
  }
  /**
     * 取多行(二维数组)
     * @access public
     * @param string $sql
     * @return array
     */
  function GetPlan($sql){
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"104")) echo $sql.NEW_LINE_CHAR;
    $this->sql = $sql;
    $rs = $this ->Query($sql);
    if ( $rs == false ){
      return false;
    }
    $data = array();
    while( ($row = $this ->fa($rs)) != false ){
      $data[] = $row;
    }
    @mysql_free_Result($rs);
    return $data;
  }
  // --------------- 很少使用的 public method ----------------- //
  /**
     * 取得结果集的行数
     * @access public
     * @param resource result set
     */
  function CountResultRows($rs){
    return @mysql_num_rows($rs);
  }
  /**
     * 取到最后一次操作所影响的行数
     * @access public
     * @return integer
     */
  function CountAffectedRows(){
    return @mysql_affected_rows($this->connectionR);
  }

  /**
  * 取一行
  * @access private
  * @param resource $rs 结果集
  * @return array
  */
  function fa($rs){
    if(!$this->assoc)
    {
      return @mysql_fetch_array($rs);
    }
    else
    {
      return @mysql_fetch_assoc($rs);
    }
  }
  /**
    * 执行一条读取查询命令
    * @access private
    * @param string $sql sql
    * @return resource|boolean
    */
  function Query($sql){
    // 事先判断 查询语句是否是 select ，不是就返回false
    $sql = ltrim($sql);$this->sql = $sql;
    $prex = strtolower(substr($sql,0,4));
    if ($prex==="sele" || $prex==="desc" || $prex==="show"){
      return @mysql_query($sql, $this->connectionR);
    }
    else return false; // 非select操作的被禁止
  }

  /**
   * 获取sql语句
   *
   * @return string or bool
   */
  function getSQL(){
    return $this->sql;
  }
}