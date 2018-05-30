<?php
/**
 * LoginAction.cls.php
 */

require_once("mod/AdminUserR.cls.php");
require_once('mvc/Action.cls.php');

class LoginAction extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    // 如果没有提交表单
    if ( empty($form)  ){
      $response['data']['back_url'] = $request['back_url'];
      return $actionMap->findForward('failure');
    }

    $response['form'] = $form;

    $username = $form['username'];
    $password = $form['password'];

    $userR = new AdminUserR();
    $result = $userR->getLocUserexistByuser($form);

    if ( $result['ret']>=100 ){
      $actionError->add('action_error_notice',$result['msg']);
      return $actionMap->findForward('failure');
    }else if ($result['ret']==1) {
      // 1:用户名密码不正确;
      $actionError->add('action_error_notice',"用户名或密码错误");
      return $actionMap->findForward('failure');
    }else if ($result['ret']==2) {
      //  2:用户不存在, 只提供了用户的情况下
      $actionError->add('action_error_notice',"用户不存在");
      return $actionMap->findForward('failure');
    } else if ($result['ret'] > 0 && isset($result['msg'])) {
      //  3:用户被删除了等情况
      $actionError->add('action_error_notice', $result['msg']);
      return $actionMap->findForward('failure');
    }

    if ( !empty($request['back_url']) ){
      // 似乎应该用session中注册的，以后验证????
      return $request['back_url'];
    }

    return $actionMap->findForward('success');
  }
}
