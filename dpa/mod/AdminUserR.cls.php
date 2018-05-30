<?php
/**
 * class: AdminUserR.cls.php
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
require_once("common/functions.php");
require_once("common/global_func.php");
require_once("common/lib/UIBI.cls.php");
require_once("mod/DBR.cls.php");
require_once("mod/DBW.cls.php");

class AdminUserR
{
  var $_sid = null;
  var $_fields = array("id","username","password","nickname","email","g_id","proj_priv","if_super","is_loc");
  var $_cookie_sid_domain = "";
  var $_cookie_sid_path   = "/";
  var $_cookie_sid_expire = 315360000;  // 十年时间 3650*24*3600
  var $_sid_str = "sid";    // cookie中sid的名称
  /**
   * 初始数据表
   * 会被覆盖
   */
  var $table_name     = '';
  var $loginlog_table_name= '';  // 登录日志表名

  function __construct($row = null, $type='', $sess_type='session'){
    // $type 可能是LDAP、或其他权限认证；$sess_type 可能是cookie认证
    $this->_cookie_sid_domain   = "." . $GLOBALS['cfg']['WEB_DOMAIN'];//初始化
    $this->table_name      = TABLENAME_PREF . $GLOBALS['cfg']['TABLENAME_USER'];
    $this->loginlog_table_name  = TABLENAME_PREF . $GLOBALS['cfg']['TABLENAME_LOGINLOG'];

    if(empty($row))
    {
      // cookie验证优先，session验证其次
      if (!empty($_COOKIE[$this->_sid_str])) {
        $this->_sid = $_COOKIE[$this->_sid_str];
      }

      session_start();
      $this->InitUserRow_();
    }
    $this->_cookie_sid_domain = $this->getCookieSidDomain();
  }

  // 兼容php4
  function AdminUserR($row = null, $type='', $sess_type='session')
  {
    $this->__construct($row, $type, $sess_type);
  }

  /**
   * 获取用户信息
   *
   * @param array $a_uinfo
   * @return array, 并且'ret'=>0|1表示成功还是失败，其他'user_info'=>rlt 表示用户信息
   *
   */
  function getLocUserexistByuser($a_uinfo){
    $rlt = array('ret'=>1);

    $l_pwd_ziduan = 'pwd';
    $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
    $dbR = new DBR($l_name0_r);
    $l_err = $dbR->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $rlt['ret'] = 100;
      $rlt['msg'] = "数据库无法连接:".$l_err[2];
      return $rlt;
    }

    // 如果强制为外部用户则只从用户中心找
    if (isset($GLOBALS['cfg']['if_neibu_login']) && "no"==$GLOBALS['cfg']['if_neibu_login']) {
      // 这样的设置表示只从用户中心获取数据
      $rlt = $this->getUserexistByuser($dbR, "", $a_uinfo);
    }else {
      // 优先找管理用户, 如果没有找到再去用户中心库中找，
      $rlt = $this->getUserexistByuser($dbR, $l_name0_r, $a_uinfo);
      if ( 2==$rlt['ret'] ) {
        // ret=2表示用户不存在，1表示密码错误, 不存在该用户才去用户表中寻找
        $rlt = $this->getUserexistByuser($dbR, "", $a_uinfo);
      }
    }
    return $rlt;
  }

  /**
   * 从不同库获取用户信息
   * 可能是uibi外部用户中心的用户数据，也可能是系统管理的用户数据
   *
   * ret代码说明，0表示成功，1~99一般性错误； 100~199是数据库方面的错误; 200~299是网络错误；300~399是其他
   *
   * @param obj $dbR
   * @param string $a_alias_db
   * @param array $a_uinfo
   * @return array, 必然有一项 ret=0|1分别表示成功、失败
   */
  function getUserexistByuser(&$dbR, $a_alias_db, $a_uinfo){
    $rlt = array('ret'=>1);  // 返回结果
    $l_pwd_ziduan = 'pwd';

    // 需要切换数据库连接信息以及表名
    if ($a_alias_db == $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']) {
      $l_is_loc = 1;  // 标记为系统管理员库
      $dbR->dbo = &DBO($a_alias_db);  // 每次切换一下
    } else {
      $l_is_loc = 0;  // 标记为用户中心库
      $a_p_t_d_arr = array(
        'p'=>array('name_cn'=>"用户中心库"),
        't'=>array('name_cn'=>"用户表")
      );
      $l_ptd_info_arr = getProInfoTblInfoDocInfo($dbR, $a_p_t_d_arr);
      if (empty($l_ptd_info_arr)) {
        $rlt['ret'] = 100;  // 在数据库方面的错误中未找到数据
        $rlt['msg'] = " some_error:".__FILE__ . " ".__LINE__;  // 出错了
        return $rlt;
      }else {
        // 此时的 dbR 已经是切换到相应的数据库了，不过再重复切换一下也无妨
        $dbR->dbo = &DBO($l_ptd_info_arr['p_info']['name_eng']."_r");
        $this->table_name = $l_ptd_info_arr['t_info']['name_eng'];
      }
      $l_pwd_ziduan = 'password';
    }
    $dbR->table_name = $this->table_name;

    $username   = $a_uinfo["username"];  // 也可能是Id
    $password   = $a_uinfo["password"];
    $md5pass    = array_key_exists("md5pass",$a_uinfo)?$a_uinfo["md5pass"]:false;

    $where_limit = " where username='" . $username . "'";

    // 先检查用户是否存在，用于前端显示不同信息
    $l_user = $dbR->getOne($where_limit);
    if (PEAR::isError($l_user)) {
      $rlt['ret'] = 101;  // 在数据库中未找到数据,或者数据库方面的错误
      $rlt['msg'] = " error message： " .$l_user->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
      return $rlt;
    }
    // 如果为空表示用户不存在, 将返回null空; 存在则返回数组
    if (null===$l_user) {
      // 表示不存在此用户
      $rlt['ret'] = 2;
      $rlt['msg'] = $username. " user  not exist!";
      return $rlt;
    }

    // 如果用户提交了密码，则需要密码验证
    if (''!=$password) {
      $md5_pass = $md5pass?$password:md5($password);
      $where_limit .= " and $l_pwd_ziduan ='".$md5_pass."'";
      $l_user = $dbR->getOne($where_limit);  // 再查询一遍
      if (PEAR::isError($l_user)) {
        $rlt['ret'] = 101;  // 在数据库中未找到数据,或者数据库方面的错误
        $rlt['msg'] = " error message： " .$l_user->userinfo .  NEW_LINE_CHAR;//作为错误信息显示出来
        return $rlt;
      }
    }

    if (is_array($l_user)) {
      if ('use' != $l_user['status_']) {
        // 用户被删除的情况
        $rlt['ret'] = 3;
        $rlt['msg'] = $username . " 该用户已被删除!";
        return $rlt;
      }
      // 同时需要将该用户的权限数据获取到，获取用户的项目和相应的表权限。并直接赋值到结果数组中
      $this->getProjTBLPriv( $dbR, $l_user );
      $l_user["md5pass"] = 1;  // 数据库中的密码是md5加密过的
      $l_user["is_loc"]  = $l_is_loc;  // 数据库中的密码是md5加密过的
      $rlt['ret'] = 0;  // 成功
      $rlt['user_info'] = $l_user;
      return $rlt;
    } else {
      // 有密码的情况下
      $rlt['ret'] = 1;
      $rlt['msg'] = "username and password not match!";
      return $rlt;
    }
  }

  // 从两张表中获取项目和表的权限
  function getProjTBLPriv( &$dbR, &$a_uinfo ){
    $u_id = $a_uinfo["id"];

    if ($u_id<=0) {
      return ;
    }
    // 通过u_id获取到其项目id数组和表权限
    $dbR->table_name = TABLENAME_PREF . "user_proj_privileges";
    $proj_priv = $dbR->getAlls(" where u_id=".$u_id . " and status_ = 'use' ");

    if (PEAR::isError($proj_priv)) {
      $proj_priv = array();
    }else {

      if (is_array($proj_priv) && !empty($proj_priv) && array_key_exists("suoshuxiangmu_id",$proj_priv[0])) {
        // 变成按照字段排列的
        $proj_priv = cArray::Index2KeyArr($proj_priv, $a_val=array("key"=>"suoshuxiangmu_id", "value"=>array()));

        // 再获取表权限
        $dbR->table_name = TABLENAME_PREF . "user_tempdef_privileges";
        $tbl_priv = $dbR->getAlls(" where u_id=".$u_id . " and status_ = 'use' ");
        if (PEAR::isError($tbl_priv)) {
          $tbl_priv = array();
        }else {
          if (is_array($tbl_priv)) {
            // 循环一下，并填充到$proj_priv中去, 作为子数组
            foreach ($tbl_priv as $l_tbl){
              // 按照t_id重新组织一下, 但必须保证已经有项目权限
              if (array_key_exists($l_tbl["suoshuxiangmu_id"], $proj_priv)) {
                $proj_priv[$l_tbl["suoshuxiangmu_id"]]["tbl_priv"][$l_tbl["suoshubiao_id"]] = $l_tbl;
              }
            }
          }
        }
      }
    }
    $a_uinfo["proj_priv"] = $proj_priv;

    return null;
  }

  function logout(){
    // 销毁session
    session_destroy();

    // 销毁cookie
    $this->destroy_cookie();

  }

  //
  function destroy_cookie($name=null){
    if (empty($name)) $name = $this->_sid_str;
    setcookie($name, "", time()-3600, $this->_cookie_sid_path, $this->_cookie_sid_domain);
  }

  /**
   * 通过给定的sid字符串，判断是否正确的sid，成功返回用户信息，失败返回错误信息
   *
   * @param string $sid
   * @return array, ret=0|1成功还是失败
   *
   */
  function IsSid($sid){
    $uid   = substr($sid,32);
    $u_arr  = array("username"=>$uid);
    $l_rlt   = $this->getLocUserexistByuser($u_arr);

    // 通过id获取到用户名和密码等信息以后进行加密验证, 如果跟提供的sid吻合则表示正确, 否则错误
    if (0==$l_rlt['ret']) {
      $l_sid = UIBI::getSidFromUIBIByUserPass($l_rlt['user_info']);
      if ($l_sid==$sid) {
        $l_rlt['ret'] = 0;  // 此项可以不必要，因为返回的一定有此项且为0
        return $l_rlt;
      }else {
        // 如果两次的sid不一致，认为是伪造的，因此设置为错误
        $l_rlt['ret'] = 9;
        $l_rlt['msg'] = "_sid_ is wrong!";
      }
    } else {
      //$l_rlt['ret'] = 1;  // 100~200表示数据库错误; 1 表示用户不存在
    }

    return $l_rlt;
  }

  /**
  * @abstract 验证用户是否登录
  * @access public
  * @return bool
  */
  function Authorize($a_arr)
  {
    // 返回地址注册
    if ( !isset($_SESSION['back_url']) && isset($a_arr["back_url"]) ){
      $_SESSION["back_url"] = $a_arr["back_url"];
    }

    // 如果使用 session 和 cookie 验证
    if ( isset($_SESSION['user']) ){
      // session存在可以认为已经登录成功，可不用种cookie。
      return true;
    }

    if (isset($_COOKIE[$this->_sid_str]) && $_COOKIE[$this->_sid_str]) {
      // kaixin001 _kx    42ebe5b40da8b6ce25e1ae1a47c4dea5_105421
      // 发送 cookie 的sid C33E51F0C725BA9B9BC368B9B7B15A25ifeng_test002 到指定服务器进行验证
      $l_rlt = $this->IsSid($_COOKIE[$this->_sid_str]);  //
      if (0==$l_rlt['ret']) {
        $this->_sid = $_COOKIE[$this->_sid_str];
        $this->InitUserRowByInfo($l_rlt['user_info']);
        $this->InitUserSession();
        return true;
      } else if (1==$l_rlt['ret']) {
        return $l_rlt['msg'];  // 1表示用户不存在, 返回字符串
      } else {
        // 注销错误的cookie, 此处ret为9表示sid是伪造的或者过期的
        $this->destroy_cookie();
        // return false; 先不返回，还要进行session判断
      }
    }

    // 用户提交的数据 // 没有session和cookie，则试图种
    if (isset($a_arr["username"]) && $a_arr["username"] && $a_arr["password"]) {
      $username = $a_arr['username'];
      $password = $a_arr['password'];

      if (isset($_SESSION["ERROR_LOGIN"]["num"]) && $_SESSION["ERROR_LOGIN"]["num"]>0) {
        if(strtolower($_SESSION["AI-code"]) != strtolower($a_arr["aicode"])){
          // 验证码错误
          $_SESSION["AI_code_error"] = 1;
          return false;
        }
      }
      // username 也可以是用户id
      $l_rlt = $this->getLocUserexistByuser($a_arr);

      if (0==$l_rlt['ret']){
        $this->SetSessionCookieByUserArr($l_rlt['user_info'], $a_arr);

        // 记录下登录日志 begin
        $data_arr = array(
          "username"=>$this->username,
          "nickname"=>$this->nickname,
          "succ_or_not"=>"y"
        );
        $rlt = $this->LogerLoginlog($data_arr);
        // 记录日志完成 end

        return true;
      } else {
        if ( !isset($_SESSION['ERROR_LOGIN']) ){
          $_SESSION['ERROR_LOGIN'] = array();
        }
        if (!isset($_SESSION["ERROR_LOGIN"]["num"])) {
          $_SESSION["ERROR_LOGIN"]["num"] = 0;
        }
        $_SESSION["ERROR_LOGIN"]["num"] += 1;

        // 用户名或密码不正确, 记录登录日志
        $data_arr = array(
          "username"=>$username,
          "description"=>$password,
          "succ_or_not"=>"n"
        );
        $rlt = $this->LogerLoginlog($data_arr);

        return false;
      }
    } else {
      return false;
    }

    return true;
  }

  function SetSessionCookieByUserArr($l_uinfo, $a_arr=array()){
    $this->InitUserRowByInfo($l_uinfo);
    $this->_sid = UIBI::getSidFromUIBIByUserPass($l_uinfo);

    // 可以在此种植需要用到的cookie，甚至是记住登录处sid
    $this->setUserSession();
    $this->setUserCookie();
    // 种cookie
    if (isset($a_arr["remember"]) && $a_arr["remember"]) {
      // 用户设置记录帐号, 记住登录处sid的cookie, 用于唯一识别
      if (!empty($this->_sid)) setcookie($this->_sid_str, $this->_sid, time()+$this->_cookie_sid_expire, $this->_cookie_sid_path, $this->_cookie_sid_domain);
    }
  }

  /**
  * @method ValidatePerm
  * @access public
  * @param array $request
  * return boolean
  */
  function ValidatePerm ($a_request){
    $l_auth = $this->Authorize($a_request);
    if ( is_string($l_auth) ){
      return $l_auth;  // 返回错误信息
    }
    if ($l_auth){
      return $this->Authorize($a_request); // 在一次请求的目的是上一次请求已经做了一些处理
    }
    // 还有其他一些权限，都在此处理
    return false;
  }

  function setUserCookie(){
    if ($this->id) {
      // 常用cookie种下
      setcookie("uid", $this->id, time()+$this->_cookie_sid_expire, $this->_cookie_sid_path, $this->_cookie_sid_domain);
      setcookie("user", $this->username, time()+$this->_cookie_sid_expire, $this->_cookie_sid_path, $this->_cookie_sid_domain);
      if (""!=$this->email) setcookie("email", $this->email, time()+$this->_cookie_sid_expire, $this->_cookie_sid_path, $this->_cookie_sid_domain);
    }
  }

  function setUserSession(){
    if ($this->id) {
      // 设置 session
      $this->InitUserSession();
      return true;
    } else {
      $this->logout();
      return false;
    }
  }

  function LogerLoginlog($data_arr){
    $l_name0_w = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'];
    $dbW = new DBW($l_name0_w);
    $l_err = $dbW->errorInfo();
    if ($l_err[1]>0){
      // 数据库连接失败后
      $response['html_content'] = date("Y-m-d H:i:s") . " 出错了， 错误信息： " . $l_err[2]. ".";
      return null;
    }
    $dbW->table_name = $this->loginlog_table_name;
    $data_arr["clientip"] = getip();
    $data_arr["serverip"] = getServerIp();
    return $dbW->insertOne($data_arr);
  }

  function getCookieSidDomain(){
    // 命令行下无HTTP_HOST；
    if (isset($_SERVER["HTTP_HOST"]))
        $l_domain = getSimpleDomain("http://".$_SERVER["HTTP_HOST"]);
    else return '';
    if (false===strpos($this->_cookie_sid_domain, $l_domain)) {
      if ("localhost" == $l_domain) {
        return $l_domain;
      }else {
        return "." . $l_domain;
      }
    }
    return $this->_cookie_sid_domain;
  }

  function InitUserSession(){
    if ( !isset($_SESSION['user']) ){
      $_SESSION['user'] = array();
    }
    // $_SESSION['user']['username'] = $this->username;
    foreach ($this->_fields as $l_field){
      $_SESSION['user'][$l_field] = $this->$l_field;
    }
  }

  function InitUserRow_(){
    // $this->id = $_SESSION['user']['id'];
    if (isset($_SESSION['user'])) {
        foreach ($this->_fields as $l_field){
          $this->$l_field = $_SESSION['user'][$l_field];
        }
    }
  }

  function InitUserRowByInfo($a_uinfo)
  {
    // $this->id = $a_uinfo["id"];
    foreach ($this->_fields as $l_field){
      $this->$l_field = $a_uinfo[$l_field];
    }
  }

  function __destruct(){}
}
