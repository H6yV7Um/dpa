<?php
/**
 * LogoutAction.cls.php
 */
require_once('common/Html.cls.php');
require_once('mvc/Action.cls.php');
require_once('mod/AdminUserR.cls.php');

class LogoutAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $userR= new AdminUserR;
    $userR->logout();
    // cookie也相应的删除, 其他无关紧要的cookie应该保留一下
    /*if(is_array($cookie)){
    foreach ($cookie as $_name=>$val){
    //setcookie($_name, "", time()-3600, COOKIE_PORTFOLIO_PATH, COOKIE_PORTFOLIO_DOMAIN);
    setcookie($_name, "", time()-3600);
    }
    }*/

    $response['ret'] = array('ret'=>0,'msg'=>'Logout succ');
    // 返回字符串用于跳转
    return "main.php?do=".$GLOBALS['cfg']['DEFAULT_LOGIN_ACTION'];
  }
}