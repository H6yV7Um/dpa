<?php
/**
 * class: LDAPUserR.cls.php
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
require_once("mod/AdminUserR.cls.php");
require_once("common/LDAP.php");

class LDAPUserR extends AdminUserR
{
  function getLocUserexistByuser($username){
    $dbR = new DBR();
    $l_err = $dbR->errorInfo();
        if ($l_err[1]>0){
          // 数据库连接失败后
          return null;
        }
    $dbR->table_name = $this->table_name;
    $rlt = $dbR->getOne("where username='" . $username . "'");

    if ($rlt) {
      return $rlt;
    }else {
      return null;
    }
  }

  function validateLDAP($username, $password){
    if (1===LDAPAuth($username, $password)){
      return true;
    }else {
      return false;
    }
  }

  /**
  * @abstract 验证用户是否登录
  * @access public
  * @return bool
  */
  function Authorize($a_arr)
  {
    // 如果使用 session 和 cookie 验证
        if ( isset($_SESSION['user']) ){
            return true;
        } else {  // 没有session，则试图种
      // 用户提交的数据
          if ($a_arr["username"]&&$a_arr["password"]) {
            $username = $a_arr['username'];
            $password = $a_arr['password'];

            // 首先去LDAP验证
            if ($this->validateLDAP($username, $password)) {
              // 合法用户,再去本地数据库中查看是否存在，如果存在则记录登录日志；
              // 如果不存在则插入本地数据库，然后记录登录到登录日志中
              $l_uinfo = $this->getLocUserexistByuser($username);

              if ($l_uinfo){
                // 本地数据库中已经存在
                $this->_cusuid   = $l_uinfo["id"];
                $this->_username = $username;
                $this->_nickname = $l_uinfo["nickname"];

                // 可以在此种植需要用到的cookie，甚至是记住登录处sid，
                $this->setUserSession();

                // 记录下登录日志 begin
                $dbW = new DBW();
            $dbW->table_name = $this->loginlog_table_name;
            $data_arr = array(
              "username"=>$this->_username,
              "nickname"=>$this->_nickname,
              "clientip"=>getip(),
              "serverip"=>getServerIp(),
              "succ_or_not"=>"y"
            );
            $rlt = $dbW->insertOne($data_arr);
            // 记录日志完成 end

                      return true;
                  } else {
                    // 本地数据库里面如果不存在的话，则直接注册到数据库中，并设置session
                    // 注册到数据库 begin
                $dbW = new DBW();
            $dbW->table_name = $this->table_name;
            $data_arr = array(
              "username"=>$username,
              "pwd"=>"",
              "nickname"=>$username,  // 默认同username
              "mobile"=>"",
              "telephone"=>"",
              "email"=>$username."@staff.sina.com.cn",
              "badPwdStr"=>"",
              "lastPwdChange"=>time()
            );
            $rlt = $dbW->insertOne($data_arr);
            // 注册到数据库 end

            if ($rlt) { // 成功则注册session
              $this->_cusuid   = $dbW->LastID();
                  $this->_username = $username;
                  $this->_nickname = $username;  // 默认同username

                  // 可以在此种植需要用到的cookie，甚至是记住登录处sid，
                  $this->setUserSession();
            }else {
              echo "注册到数据库出错，也许数据表不存在或数据库选择出错";
              exit;
              return false;
            }

                    // 同时记录登录日志到数据库中
                    // 记录下登录日志 begin
                // $dbW = new DBW();
            $dbW->table_name = $this->loginlog_table_name;
            $data_arr = array(
              "username"=>$username,
              "nickname"=>$username,
              "clientip"=>getip(),
              "serverip"=>getServerIp(),
              "succ_or_not"=>"y"
            );
            $rlt = $dbW->insertOne($data_arr);
            // 记录日志完成 end

                      return true;
                  }
            }else {
              // 用户名或密码错误，记录一下日志
              $dbW = new DBW();
          $dbW->table_name = $this->loginlog_table_name;
              $data_arr = array(
                "username"=>$username,
                "clientip"=>getip(),
                "serverip"=>getServerIp(),
                "succ_or_not"=>"n"
              );
              $rlt = $dbW->insertOne($data_arr);

              return false;
            }
          } else {
            return false;
          }
        }
    return true;
  }
}
