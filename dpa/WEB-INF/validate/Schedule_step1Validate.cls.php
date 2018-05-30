<?php
/**
 * Schedule_step1Validate.cls.phps
 */
require_once('mvc/ActionRequest.cls.php');
require_once('mvc/ActionError.cls.php');

class Schedule_step1Validate extends ActionRequest {

  /**
     * gpc信息检查
     */
  function validate( $req, $form,$get,$cookie,$REQUEST_METHOD=NULL ){
    $error = new ActionError();
    if ("POST"==$REQUEST_METHOD) {
      if (is_array($form) && !empty($form) ) {
        if (empty($form['name'])){
          $error->add('action_error_name','name_cn code is empty');
        }
      }
    }
    return $error;
  }
}