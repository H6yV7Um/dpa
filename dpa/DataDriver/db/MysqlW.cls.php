<?php
/**
* DAO.cls.php
* @abstract  操作MySQL数据库,希望扩展出两个子类，分别是读写mysql的；不需要格式化外来数据，因为语句中已经有检查,除del操作没检查
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
require_once("configs/db.conf.php");
require_once("DataDriver/db/DAO.cls.php");

class MysqlW extends DAO {
  var $sql = null;
  /**
   * 程序运行的整个生命周期只用一个mysqlW连接
   *
   * @var unknown_type
   */
  var $connectionW = null;
  /**
   * 主库连接
   *
   * @return resource
   */
  function ConnectW($dbname = '', $dbuser = '', $dbpass = '', $dbhost = 'localhost', $dbport = '3306'){
    if (""!=$dbname) {
      $temp_con = parent::Connect($dbname, $dbuser, $dbpass, $dbhost, $dbport);
    }else{
      $temp_con = parent::Connect($_SERVER["SRV_DB_NAME_W"],
      $_SERVER["SRV_DB_USER_W"],$_SERVER["SRV_DB_PASS_W"],
      $_SERVER["SRV_DB_HOST_W"],$_SERVER["SRV_DB_PORT_W"]);
    }
    $this->connectionW = $this->connection;
    return $temp_con;
  }
  /**
     * 插入
     * @param string $sql 插入命令
     * @return boolean
     */
  function InsertIntoTbl($tablename, $ar){
    if (!is_array($ar)||empty($ar)) {  // 确保$ar为非空数组
      return false;
    }
    $num = count($ar);
    $i=0;
    $ziduan = "";
    $vals = "";
    foreach ($ar as $key => $val){
      if ($i > 0){
        $ziduan .= ",";
        $vals .= ","; // 至少第一项以后才能有逗号
      }
      $ziduan .= $this->FormatField(trim($key));
      $vals   .= $this->FormatValue(trim($val));
      $i++;
    }
    $sql = "insert into {$tablename} ( $ziduan ) values ( $vals )";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"201")) echo $sql.NEW_LINE_CHAR;
    return $this->Query($sql);
  }
  /**
     * 取得上一步 INSERT 操作产生的 ID
     * @access public
     * @return integer
     */
  function LastID(){
    return @mysql_insert_id($this ->connectionW);
  }
  /**
   * 彻底删除记录，谨慎操作
   *
   * @param string $tablename
   * @param string $condition
   * @param string $limit
   * @return resource|boolean
   */
  function DeleteData($tablename, $condition, $limit = ""){
    $sql = "delete from {$tablename} $condition $limit ";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"202")) echo $sql.NEW_LINE_CHAR;
    return $this->Query($sql);
  }
  /**
   * 更新命令
   *
   * @param string $tablename
   * @param string $condition
   * @param array $ar
   * @param boolean $bAdd
   * @return boolean
   */
  function UpdateTableArray($tablename, $ar, $condition, $if_addcount = false){
    if (!is_array($ar)||empty($ar)) {  // 确保$ar为非空数组
      return false;
    }
    $sql = "update {$tablename} set ";
    $sql .= cString_SQL::FmtFieldValArr2Str($ar, ",", $if_addcount);
    /*
    $i=0;
    foreach ($ar as $key => $val){
      if ($i > 0) $sql .= ","; // 至少第一项以后才能有逗号
      $sql .= ($this->FormatField(trim($key)) . "=" . $this->FormatValue(trim($val)));
      $i++;
    }*/
    $sql .= " where {$condition}";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"203")) echo $sql.NEW_LINE_CHAR;
    return $this->Query($sql);
  }
  /**
    * 执行一条查询命令,只能用于update操作
    * @access private
    * @param string $sql sql
    * @return resource|boolean
    */
  function Query($sql){
    // 事先判断 查询语句是否是 update ，不是就返回false
    $sql = ltrim($sql);$this->sql = $sql;
    //$prex = strtolower(substr($sql,0,5));
    //if ($prex==="updat"||$prex==="inser"||$prex==="delet"||$prex==="alter"){
      return @mysql_query($sql, $this->connectionW);
    //}
    //else return false; // 非更新操作的不能
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
    $rs = @mysql_query($sql, $this->connectionW);
    if(!$this->assoc){
      $row = @mysql_fetch_array($rs);
    }else{
      $row = @mysql_fetch_assoc($rs);
    }
    @mysql_free_Result($rs);
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

  function setDatabase($db_name){
    $sql = "use $db_name ";$this->sql = $sql;
    global $SHOW_SQL;
    if ("all"==$SHOW_SQL||false!==strpos($SHOW_SQL,"202")) echo $sql.NEW_LINE_CHAR;
    return $this->Query($sql);
  }
}