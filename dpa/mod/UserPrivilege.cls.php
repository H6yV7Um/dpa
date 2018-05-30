<?php
/**
 * 用户各种权限: 项目的主要属性权限（文档或数据库）、表查增删改权限、文档查增删改权限
 *     涉及到三张表，从中获取数据并注册到session数组中去。
 *
 * @author chengfeng@
 * @since  2011-7-9
 */

class UserPrivilege
{
  //var $_privs = array("proj_priv","tempdef_priv","doc_priv");

  /**
   * 初始数据表
   */
  //var $table_name_ = "user_proj_privileges";

  function __construct($row = null, $type='', $sess_type='session')
  {

  }

  // 兼容php4
  function UserPrivilege($row = null, $type='', $sess_type='session')
  {
    $this->__construct($row, $type, $sess_type);
  }

  // 有任何权限不符合的都将返回到ActionError中去
  // 同时也可以输出错误信息，并退出
  function validate( &$actionError, $privs, $req, $form,$get,$cookie,$l_REQUEST_METHOD=NULL ){
        //
        if(!is_object($actionError)) $actionError = new ActionError();

        // 如果资源是开放的，也就是任何人都可以看的话，则可以立即返回，不注册任何错误
        // 类似于linux的文件权限一样，自己有读写权限，组内读写以及其他用户对文件的读写权限
        // 此处就简单地对别一个读权限，以后需要再完善之????
        // 超级用户也能跳过权限检查
        if ( is_array($privs) && !array_key_exists("if_super",$privs) ) $privs["if_super"] = 0;
        if ((isset($req["obj_open"]) && "open" == $req["obj_open"]) || $privs["if_super"] ) {
          return null;
        }

        // $privs 来自session数组的user proj_priv,
        // 先判断是从大到小，从外到内，即必须先具备proj权限，才能谈tbl权限。
        if (isset($privs["proj_priv"])) {
          // 依据 p_id t_id 进行搜索(项目id和表id)
          // 如何识别p_id,t_id呢, 结合参数do， 如何自动识别p_id和t_id能, 通常这个系统中p_id就是项目id，t_id表id
          $p_id = $this->getProjectId($privs, $req, $form,$get,$cookie);
          $t_id = $this->getTableId($privs, $req, $form,$get,$cookie);

          if ($p_id>0) {
            // 当有p_id的时候，
            if (array_key_exists($p_id, $privs["proj_priv"])) {
              // 先判断具体项目的权限，也有select、insert、update等
              $l_action = "list_all_temp";
              if ("F"==$privs["proj_priv"][$l_action ."_priv"]) {
                // 并没有对此表的操作权限，
                $actionError->add('action_error_msg','* you do not have the privileges to option!');
                return ;
              }

              // 两种情况，有t_id和没有t_id
              if ($t_id>0) {
                $l_action = "list";
                if ("F"==$privs["proj_priv"][$p_id]["tbl_priv"][$l_action ."_priv"]) {
                  // 并没有对此表的操作权限，
                  $actionError->add('action_error_msg','* you do not have the privileges to option!');
                  return ;
                }

                // 当有表权限的时候
                if (array_key_exists($t_id, $privs["proj_priv"][$p_id]["tbl_priv"])) {
                  // 具体的某项权限，例如：insert，select，update等。

                }else {
                  // 并没有对此表的操作权限，
                  $actionError->add('action_error_msg','* you do not have the privileges to option!');
                  return ;
                }
              }
            }else {
              // 数据库中没有设置该用户对此项目的权限，则注册错误
              $actionError->add('action_error_msg','* you do not have the privileges to option!');
              return ;
            }
          }
        }

        // return $error;
    }

    function getSqlInProjectByPriv(){
      $l_ps = "";
      if (array_key_exists("proj_priv",$_SESSION["user"]) && !empty($_SESSION["user"]["proj_priv"])) {
        // 通过p_id来限制选取的项目范围
        $l_ps = array_keys($_SESSION["user"]["proj_priv"]);
        $l_ps = implode(",", $l_ps); // 当$l_ps没有的时候是"";当有一个的时候是"1";两个以上"1,3,6"
      }
      return $l_ps;
    }

    function getProjectId($privs, $req, $form,$get,$cookie){
      // 其实需要依据动作进行判断，p_id有可能是上一级id而非项目id，暂时先用p_id替代之.
      if ("project"==$req["type_name"]) $l_f = "id";
      else $l_f = "p_id";

      return isset($req[$l_f])?$req[$l_f]:null;
    }

    function getTableId($privs, $req, $form,$get,$cookie){
      return isset($req["t_id"])?$req["t_id"]:null;
    }

    // 给出单个的权限进行验证。例如：某个用户alter某个table的权限是否具备，
    function yanzhendange(){

    }

    function getSqlInTableByPid($p_id){
      $l_ts = "";
      if (isset($_SESSION["user"]["proj_priv"][$p_id]['tbl_priv'])) {
        // 通过p_id来限制选取的项目范围
        $l_ts = array_keys($_SESSION["user"]["proj_priv"][$p_id]['tbl_priv']);
        $l_ts = implode(",", $l_ts); // 当$l_ps没有的时候是"";当有一个的时候是"1";两个以上"1,3,6"
      }
      return $l_ts;
    }

  function __destruct(){}
}

