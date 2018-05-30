<?php
/**
 * Project_addValidate.cls.phps
 */
require_once('mvc/ActionRequest.cls.php');
require_once('mvc/ActionError.cls.php');

class Project_addValidate extends ActionRequest {
  /**
     * gpc信息检查
     */
  function validate( $req, $form,$get,$cookie,$REQUEST_METHOD=NULL ){
    $error = new ActionError();
    if ("POST"==$REQUEST_METHOD) {
      if (empty($form['name_cn'])){
        $error->add('action_error_name_cn','name_cn code is empty');
      }
    }
    return $error;
  }
}
