<?php
/**
* ActionServer.cls.php
*
* @author chengfeng<biosocial@gmail.com>
* @version 1.0
*/
require_once('lang/Assert.cls.php');
require_once("common/Html.cls.php");
require_once("mvc/ActionError.cls.php");
require_once("mod/AdminUserR.cls.php");
require_once("mod/UserPrivilege.cls.php");

class Response {
    public function Assign($items) {
        if (!$items) return;
        foreach ($items as $property => $value) {
            if (null !== $value)
                $this->$property = $value; // 增加或替换值
        }
    }
    public $status = 0;
    public $msg = '';
    //public $data = array();
}

class ActionServer {

  /**
    * 请求的数据
    * @access public
    */
  var $request = null;
  /**
    * 存放表单提交的数据
    * @access public
    */
  var $form = null;
  /**
    * 存放get数据
    * @access public
    */
  var $get = null;
  /**
    * 存放cookie数据
    * @access public
    */
  var $cookie = null;
  /**
    * 返回给用户，用于页面显示的数据
    * @access public
    */
  var $response = null;
  /**
    * ActionError 对象
    * @access public
    */
  var $actionError = null;
  /**
    * ActionMap 对象
    * @access private
    */
  var $actionMap = null;
  /**
    * forward config 数组
    * @access private
    */
  var $forwardConfig = null;

  var $files = null;

  /**
    * 初始化, 主要是初始化请求的内容和映射关系
    * @access public
    * @param ActionMap &$actionMap ActionMap object
    * @param array &$request
    */
  function init( &$actionConfig, $request, $get=null, $post=null, $cookie=null, $files=null ){
    // 配置映射环境
    $this->actionMap = $actionConfig->getActionMap();
    $this->actionMap->prop["path"] = $actionConfig->currentPath;
    // 取请求内容
    $this->get     = $get;
    $this->cookie  = $cookie;

    // 这里的$_POST,$_REQUEST数据可能是二维的，也可能是三维的，因此采用递归方式。
    // request 包含 $_GET，$_POST 和 $_COOKIE 中的全部内容, 严格来说post数据不适合做urldecode；而cookie数据可以
    // 因此可以将request按照php.ini的配置顺序进行重新编排，建议保持原样，而不做任何处理
    $this->request   = $request;//digui_deep(digui_deep($request, "trim"), "urldecode");

    $this->form = $post;
    $this->files = $files;

    if ('cli' == php_sapi_name() && 'topublishdocs_edit' == $request['do']) return ; // 命令行下运行真可以不需要身份认证？攻击者可能前台提交数据，在后台运行

    // 不用担心后续的程序修改此数值，因为此步骤的执行非常靠前, 只有在main.php的处理过程之前赋值才能到此步骤. 命令行模式无需身份验证
    if ((isset($GLOBALS['cfg']['if_is_open_page']) && $GLOBALS['cfg']['if_is_open_page']))
      return ;

    // authentication
    if($actionConfig->getActionMap()->getProp('validate')) {
        $u = new AdminUserR();
        $auth = $u->ValidatePerm($request);

        // 登录不成功，需要根据返回数据，判断属于权限问题 or 未登录问题。
        if (false===$auth) {
          $auth_fail = $this->actionMap->getProp('forwards');
          if (key_exists("auth_fail",$auth_fail)) {
            require_once("WEB-INF/auth_fail/".$auth_fail["auth_fail"]["path"]);
            exit;
          }

          if ($GLOBALS['cfg']['DEFAULT_LOGIN_ACTION']==$request['do']) {
            // 此处通过，并显示login失败的页面

            // 1. 对于do为login的非提交数据认证如果失败（无session）, 为了避免循环, 需要继续显示表单页面, 此处需要先通过
            //    让 loginaction进行简单的处理，其实就是显示login表单页面
            // 2. 有提交数据，但是不成功，同1一样显示login表单页面，但是同时将显示错误提示信息
            // 3. 有提交数据，如果成功，程序的步骤是先设置session，然后在本程序将会通过，并且将do置为一个非login的默认方法（通常是mainpage）
            //    跳转到其他页面，由于已经有session了，因此认证通过，并且做相应的action处理
          }else{
            $u->logout();
            $back_url = GetCurrentUrl();
            if (false !== strpos($back_url, 'do=logout') || false !== strpos(urldecode($back_url), 'do=logout')) {
              Html::jump("?do=".$GLOBALS['cfg']['DEFAULT_LOGIN_ACTION']);
              exit;
            }
            Html::jump("?do=".$GLOBALS['cfg']['DEFAULT_LOGIN_ACTION'] . "&back_url=".urlencode($back_url));
            exit;  // 结束程序运行
          }
        }else {
          // 可能是错误信息， 也可能是登录成功
          if (is_string($auth)) {
            $u->destroy_cookie(); // 有可能是有sid但用户被删除，所以需要销毁一下也无妨
            exit($auth);
          }

          // 登录成功。则继续执行后续, 登录表单需要跳转到默认页面
          if ($GLOBALS['cfg']['DEFAULT_LOGIN_ACTION'] == @$request['do']) {
            // 从其他地方来的url，登录成功需要返回到那个页面
            if (isset($request['back_url']) && $request['back_url']) {
              Html::jump(urldecode($request['back_url']));
              exit;
            }
            Html::jump("?do=".$GLOBALS['cfg']['DEFAULT_ACTION']);
            exit;
          }
        }
    }
    return ;
    // 增加权限判断，直接使用session里面的user数据，结合提交的数据判断是否有此操作的权限
    // 只需要在user数组中加入权限数组，在具体的业务中再去判断其权限即可,
    // 可以从最外层，例如左侧导航开始做起，
    //print_r($_SESSION);
  }
  /**
    * 处理请求
    * @access public
    */
  function process(){

    // 如果需要验证提交的数据，则调用相应的ActionValidate::validate()验证数据.
    // 无论验证是否通过，validate()都将返回一个ActionError对象,在这里收到这个对象，
    // 可以调用ActionError->isEmpty()方法，判断是否有错误发生.
    // 这个 ActionError 对象将会传递给 Action子类对象.
    // Action::exec 进行事务操作，并取到相应的数据用户页面显示，根据结果交给指定的
    // tpl进行处理，并输出页面

    // -------- 进行第一阶段的缓存处理 ---------
    // 主要任务：
    // 判断是否属于自动更新的 path，如果否，则使用缓存，如果是则继续
    // 根据 path 确定 cacheTime
    // 判断缓存是否过期，否,则使用缓存,是，则继续

    $l_REQUEST_METHOD = isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : NULL;

    // validate form,get  主要针对 POST GET COOKIE  SESSion
    $para = $this->actionMap->prop["path"];
    $n_para = strtoupper((substr($para,0,1))).(substr($para,1));
    $validateclassName = $n_para."Validate";
    if (file_exists($GLOBALS['cfg']['Validate_Path']."/".$validateclassName.'.cls.php')) {
      require_once($GLOBALS['cfg']['Validate_Path']."/".$validateclassName.'.cls.php');
      $bean = new $validateclassName;
      $this->actionError = $bean->validate($this->request,$this->form,$this->get,$this->cookie,$l_REQUEST_METHOD);
    }
    if ($this->actionError == null){
      $this->actionError = new ActionError();
    }

    // 还需要进行权限认证, 通过session中的用户权限数组数据库和表的操作权限进行对照id，能快速定位是否有权限.
    // 外来数据同session数据进行对照
    $userPriv = new UserPrivilege();
    if (isset($_SESSION["user"]))
        $userPriv->validate($this->actionError, $_SESSION["user"],$this->request,$this->form,$this->get,$this->cookie,$l_REQUEST_METHOD);
    else $userPriv->validate($this->actionError, array(),$this->request,$this->form,$this->get,$this->cookie,$l_REQUEST_METHOD);

    // 对先前的错误进行统一的处理，先放到此处
    if ( !$this->actionError->isEmpty() ){
      // 是系统错误吗？
      if ($this->actionError->getProp('sysError') != false){
        $this->forwardConfig = $this->actionMap->findForward('sysError');
      }
      //print_r($this->actionError);
      $this->forwardConfig = $this->actionMap->findForward('failure');
    } else {
    // validate end ----

      // process action
      $className = $n_para."Action";
      Assert::condition($className != null,'className is empty at '.__CLASS__ . ' ' .__FILE__.' '.__LINE__);

      require_once($GLOBALS['cfg']['Business_Path']."/".$className.'.cls.php');
      $action = new $className;
      $this->forwardConfig = $action->execute($this->actionMap,$this->actionError,$this->request,$this->response,$this->form,$this->get,$this->cookie, $this->files);
    }
    $response =& $this->response;

    // 对结果的判断
    if ($this->forwardConfig == null){
      $response['action_error'] = $this->actionError == null ? null : $this->actionError->getAllProp();
      $input_file_name = $para.'.php';
      if( file_exists($GLOBALS['cfg']['Tpl_Path']."/".$input_file_name) ) {
        require_once($GLOBALS['cfg']['Tpl_Path']."/".$input_file_name);
      }else {
        require_once($GLOBALS['cfg']['Tpl_Path']."/add.php"); // 默认一个
      }
    } elseif ( is_string($this->forwardConfig) ){  // 如果是重定向到没有声明的页面
      // 在 init 和 execute 之间进行增加属性 $l_actionServer->actionMap->addParam2Forward("if_jump","page_no_jump","page_jump");  // 防止页面跳转所做的设置
      $l_page_jump = $this->actionMap->findForward('page_jump');  // 未设置的话将是 null
      if ( null!==$l_page_jump && "page_no_jump" == $l_page_jump['parameters']['if_jump'] ) {
        return null;  // 直接返回，中止后续执行，其返回值是无意义的，其他地方并未使用此返回值
      }

      Html::jump($this->forwardConfig);
      exit;
    } else if (isset($this->forwardConfig['redirct']) && $this->forwardConfig['redirct']===true) {   // 如果是重定向到其他已声明页面
      $jumpParam = '';
      if (array_key_exists('parameters', $this->forwardConfig) && is_array($this->forwardConfig['parameters']) && count($this->forwardConfig['parameters']>0)) {

        // 添加参数
        foreach ($this->forwardConfig['parameters'] as $k=>$v){
          $jumpParam .= '&'.$k.'='.$v;
        }
        if (strstr($this->forwardConfig['path'],'?') == false){
          $jumpParam = '?jump=1'.$jumpParam;
        }
      }
      Html::jump($this->forwardConfig['path'].$jumpParam);
      exit;
    } else if ( isset($this->forwardConfig['ret']) || isset($this->response['ret'])) {   // 如果是重定向到其他已声明页面
      //, 添加了接口方式,并且优先使用接口方式，同时兼容之前的方法
      //if (isset($this->forwardConfig['ret'])) $response =& $this->forwardConfig;
      //else $response =& $this->response;
      $response['action_error'] = $this->actionError == null ? null : $this->actionError->getAllProp();
      $input_file_name = $para.'.php';
      if( file_exists($GLOBALS['cfg']['Tpl_Path']."/".$input_file_name) ) {
        require_once($GLOBALS['cfg']['Tpl_Path']."/".$input_file_name);
      }else {
        require_once($GLOBALS['cfg']['Tpl_Path']."/add.php"); // 默认一个
      }
    } else {
      $response['action_error'] = $this->actionError == null ? null : $this->actionError->getAllProp();
      require_once($GLOBALS['cfg']['Tpl_Path']."/".$this->forwardConfig['path']);
    }
  }
}
