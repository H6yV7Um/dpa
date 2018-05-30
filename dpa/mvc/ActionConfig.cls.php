<?php
/**
* ActionMap.cls.php
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
require_once('mvc/ActionMap.cls.php');

class ActionConfig {

    /**
    * action 配置元素
    * @access private
    */
    var $actionConfigs = null;


    /**
    * @access string $currentPath
    */
    var $currentPath = null;

    /**
    * ActionMap Constructor
    * @access public
    * @param array $actionConfigs
    */
    function ActionConfig( &$actionConfigs ){
        $this->actionConfigs =& $actionConfigs;
    }
    /**
    * 设置当前的 path
    * 表明没有设置相应的处理过程
    * @access public
    * @param string $path
    * @return boolean
    */
    function setCurrentPath($path){
        if (array_key_exists($path,$this->actionConfigs)) {
          $this->currentPath = $path;
          return true;
        } else {
            return false;
        }
    }
    /**
    * 取一个 ActionMap 对象
    * @access public
    */
    function getActionMap(){
        return new ActionMap($this->actionConfigs[$this->currentPath]);
    }
}