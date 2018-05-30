<?php
/**
 * Tempdef_addValidate.cls.phps
 */
require_once('mvc/ActionRequest.cls.php');
require_once('mvc/ActionError.cls.php');

class Tempdef_addValidate extends ActionRequest {

  /**
     * gpc信息检查
     */
  function validate( $req, $form,$get,$cookie,$REQUEST_METHOD=NULL ){
    $error = new ActionError();
    if ("POST"==$REQUEST_METHOD) {
      if (is_array($form) && !empty($form) ) {
        require_once("common/lib/cConstans.cls.php");
        $l_baoliu_ziduans = cConstans::getBaoLiuZiDuan();
        if ( in_array($form['name_eng'],$l_baoliu_ziduans) ){
          $error->add('action_error_username',"* " . $form['name_eng'] . '是保留字段名, 请换一个字段名');
        }
      }
    }

    return $error;
  }
}
