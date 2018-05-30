<?php
require_once("mod/AdminUserR.cls.php");
require_once('mvc/Action.cls.php');

class Pasword_editAction extends Action {
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie,$files=array()){
    $dbR = new DBR();
    $dbR->table_name = "dpps_user";
    if (!empty($form)) {
      // 获取用户信息
      $_arr = $dbR->getOne(" where id = ". $_SESSION['user']['id']);
      if ($_arr) {
        $o_passwd = $_arr['pwd'];
        if($form['password']!=$form['password_c']){
          $response['html_content'] = '输入密码不一致';
          $response['ret'] = array('ret'=>0);
          return null;  // 总是返回此结果
        }
        if (md5($form['password_o']) != $o_passwd) {
          $response['html_content'] = '输入密码不正确';
          $response['ret'] = array('ret'=>0);
          return null;  // 总是返回此结果
        } else {
          // 则进行重置密码
          $dbW = new DBW();
          $dbW->table_name = "dpps_user";
          $dbW->updateOne(array('pwd'=>md5($form['password'])), 'id=' . $_arr['id']);

          $response['html_content'] = '修改成功';
          $response['ret'] = array('ret'=>0);
          return null;  // 总是返回此结果

          /*$u = new AdminUserR();
          $u->logout();
          return "?do=login"; // 返回登录*/
        }
      } else {
        echo('用户不存在！');
      }
    }

    $data_arr = array(
      "nickname"=>$nickname,
      "ip"=>getip(),
      "RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
    );
    // 获取模板
    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");

    $response['html_content'] = replace_template_para($data_arr,$content);
    $response['ret'] = array('ret'=>0);
    return null;  // 总是返回此结果
  }
}
