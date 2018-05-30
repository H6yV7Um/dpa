<?php
/**
 * Document_addValidate.cls.phps
 */
require_once('mvc/ActionRequest.cls.php');
require_once('mvc/ActionError.cls.php');

class Document_addValidate extends ActionRequest {
  /**
     * gpc信息检查
     */
  function validate( $req, $form,$get,$cookie,$REQUEST_METHOD=NULL ){
    $error = new ActionError();
    if ("POST"==$REQUEST_METHOD) {
      // 添加用户的时候，需要进行一些认证
      // 以后放到数据库中去，便于前后端的统一，以后完善之 ????
      if ("user"==$form["action"] && "add"==$form["type"]) {
        // 用户名验证
        $l_name = "username";
        $l_reg = "/^[\w]{5,30}$/";
        if ( ! preg_match($l_reg, $form[$l_name]) ){
          // 没有匹配上，或者匹配出错
          $error->add('action_error_'.$l_name,$l_name.' not match');
        }

        // 密码验证
        $l_name = "password";
        $l_reg = "/^[\x20-\x7F]{5,30}$/";
        if ( ! preg_match($l_reg, $form[$l_name]) ){
          // 没有匹配上，或者匹配出错
          $error->add('action_error_'.$l_name,$l_name.' not match');
        }

        // 两次密码不同的验证,
        $l_name = "password2";
        if ( $form[$l_name] != $form["password"] ){
          $error->add('action_error_'.$l_name,'两次密码不一致');
        }

        // 邮箱如果填写了，其格式正确与否的验证
        $l_name = "email";
        if (!empty($form[$l_name])) {
          $l_reg = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
          if ( ! preg_match($l_reg, $form[$l_name]) ){
            // 没有匹配上，或者匹配出错
            $error->add('action_error_'.$l_name, $l_name.' not match');
          }
        }

        // 验证码的验证
        $l_name = "aicode";
          session_start();
        if ( $_SESSION['AI-code'] != $form[$l_name] ){
          $error->add('action_error_'.$l_name, '验证码不正确');
        }

        // 服务条款的验证
        $l_name = "fuwutiaokuan";
        // 如果没有选中，此项在$_POST数组中不会出现
        if ( !isset($form[$l_name]) || 1 != $form[$l_name] ){
          $error->add('action_error_'.$l_name, '服务条款必须同意');
        }
      }
    }
    return $error;
  }
}
