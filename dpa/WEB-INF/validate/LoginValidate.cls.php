<?php
/**
 * LoginValidate.cls.phps
 */
require_once('mvc/ActionRequest.cls.php');
require_once('mvc/ActionError.cls.php');

class LoginValidate extends ActionRequest {

  /**
     * 登录表单信息检查
     */
  function validate( $req, $form=null,$get=null,$cookie=null,$REQUEST_METHOD=NULL ){
    $error = new ActionError();
    if ("POST"==$REQUEST_METHOD) {
      if (is_array($form) && !empty($form) ) {
        if (empty($form['username'])){
          $error->add('action_error_username','* 用户名不能为空');
        }
        elseif (strlen($form['username']) > 20){
          $error->add('action_error_username','* 用户名的长度不能超过20个字符');
        }
        if (empty($form['password'])){
          $error->add('action_error_password','* 密码不能为空');
        }
        // 登录需要进行验证码的验证
        if ($_SESSION["ERROR_LOGIN"]["num"]>0) {
          if(strtolower($_SESSION["AI-code"]) != strtolower($form["aicode"])){
            $error->add('action_error_notice','* 验证码错误');
          }
        }
      }
    }

    return $error;
  }
}
