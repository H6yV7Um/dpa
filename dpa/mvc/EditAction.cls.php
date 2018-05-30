<?php
/**
 * EditAction.cls.php
 *
 * @abstract edit
 *
 * @author chengfeng<biosocial@gmail.com>
 * @version 1.0
 */
class EditAction {

  /**
   * 构造函数，可进行一些初始化操作，例如修改分页默认值
   *
   * @param array|null $_arr
   */
  function __construct($_arr=null){
    //
  }
  // for php4
  function EditAction($_arr=null){
    return $this->__construct($_arr);
  }

  /**
   * actiong execute
   *
   * @param array $arr
   * @param Object $actionMap
   * @param Object $actionError
   * @param array $request
   * @param array $response
   * @param array $form
   */
  function execute(&$arr,&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){
    //return replace_template_para($data_arr,$content);
  }
}
