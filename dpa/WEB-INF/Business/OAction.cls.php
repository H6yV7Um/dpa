<?php
/**
 * OAction.cls.php 开放网页所使用的：例如接口，投票留言等
 *
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once("lang/chinese.utf8.lang.php");
require_once("DataDriver/db/Nosql.cls.php");

class OAction extends Action {
  const LIMIT_PARAM = 'num'; // 以后可改成limit

  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){
    $l_content = new Response();
    $l_content->ret = 2; // 未知错误

    // http://uibi.wanda.cn/interface/index.php?do=o&action=GetTemplateListJS&cont_type=json&var_flag=json_project&p_id=2&_r=20271778
    if ( isset($request["action"])) {
      switch ($request["action"]) {
          case "zhanshang":
              $dbR = new DBR();
              $a_p_t_d_arr = array(
                  'p'=>array('name_cn'=>"用户中心库"),
                  't'=>array('name_cn'=>"展商"),
              );
              break;
        case "user":
          $dbR = new DBR();
          $a_p_t_d_arr = array(
            'p'=>array('name_cn'=>"用户中心库"),
            't'=>array('name_cn'=>"用户表"),
          );
          break;
        case "memory_data":
          break;
        case "fabu_zhanji":
            // 用户发布展记，app必须保证是登录态，TODO，可以先传uid，但是后续必须有登录之后的session和cookie数据验证身份
            // 通过cookie或参数中的uid，sid信息能确认用户的身份合法性，并获取用户信息；然后向表里面插入记录
            // 需留着

            break;
        case "ly":
          // 留言部分
          $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
          $dbR = new DBR($l_name0_r);
          $a_p_t_d_arr = array(
            'p'=>array('name_cn'=>"评论频道"),
          );
          $l_biao = "";
          if (isset($request['db_table'])) {
            $l_biao = $request['db_table'];
          }
          if ('ly_yule'==$l_biao) {
            $a_p_t_d_arr['t'] = array('name_cn'=>"娱乐频道评论");  // 娱乐频道的评论
            //$form['vote_type'] = $form['vote_type']+0;
          }
          break;
        case "vote":
          // 投票部分
          $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
          $dbR = new DBR($l_name0_r);
          $a_p_t_d_arr = array(
            'p'=>array('name_cn'=>"评论频道"),
            //'t'=>array('name_cn'=>"娱乐频道投票"),
          );
          $form = array_merge($form,$get);
          $form['clientip'] = getip();  // 投票的客户端ip

          // 针对哪张表
          $l_biao = "";
          if (isset($request['db_table'])) {
            $l_biao = $request['db_table'];
          }
          if ('vote_yule'==$l_biao) {
            $a_p_t_d_arr['t'] = array('name_cn'=>"娱乐频道投票");  // 娱乐频道的评论
            $request['vote_type'] = $request['vote_type']+0;
          }else if ('vote_it'==$l_biao) {
            $a_p_t_d_arr['t'] = array('name_cn'=>"IT频道投票"); // it频道的评论
            $request['vote_type'] = $request['vote_type']+0;
          }else {
            //
          }
          break;
        case "check_aicode":
          // 安全起见，前端只显示是否正确,而不能显示具体代码
          session_start();
          if ($_SESSION['AI-code'] == strtoupper($request['aicode'])) {
            $l_content->ret = 0;
          }else {
            $l_content->ret = 1;
          }
          break;
        case "check_login":
          $l_rlt_ = $this->check_login($actionMap,$actionError,$request,$response,$form,$get,$cookie, $l_content);
          $l_content->Assign($l_rlt_); //  将其他字段也注入到对象中去
          break;
        default:
            // 用url上的参数
            $l_name0_r = $GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R'];
            $dbR = new DBR($l_name0_r);
            $a_p_t_d_arr = array(
                'p'=>array('db_name'=>$request["action"]),
            );
            // 可以用数据库库id
            if (isset($request['db_id']) && $request['db_id'] > 0) {
                $a_p_t_d_arr = array(
                    'p'=>array('id'=>$request['db_id']),
                );  // 某张库
            }
            // 针对哪张表
            $l_biao = "";
            if (isset($request['db_table'])) {
                $l_biao = $request['db_table'];
            }
            if ('vote_yule'==$l_biao) {
                $a_p_t_d_arr['t'] = array('name_cn'=>"娱乐频道投票");  // 娱乐频道的评论
                $request['vote_type'] = $request['vote_type']+0;
            } else if ($l_biao){
                $a_p_t_d_arr['t'] = array('name_eng'=>$l_biao);  // 某张表
            }
            // TODO 兼容性处理 aups_t002 => news
            if ('aups_t002' == $l_biao){
                $a_p_t_d_arr['t']['name_eng'] ='news';  // 行业资讯表，改名，过几天删除
            }
            // 支持用表id
            if (isset($request['db_table_id']) && $request['db_table_id'] > 0) {
                $a_p_t_d_arr['t']['id'] = $request['db_table_id'];  // 某张表
            }

            // 文档id, TODO 这里的字段应该是可以自定义的
            if (array_key_exists('d_id', $request)) {
                if ($request["d_id"] > 0) {
                   $a_p_t_d_arr['d'] = array('id'=>$request["d_id"]);
                } else {
                    // 获取列表, TODO ,临时可用，但需要分页，需要用ListAction里面的方法
                    $a_p_t_d_arr['d'] = array();

                    // TODO, 支持条数限制 __LIMIT__ , __OFFSET__ 以后放到常量定义里面 ,类似魔术变量一样，
                    if (isset($request['offset']) && isset($request[self::LIMIT_PARAM])) {
                        $request['offset'] += 0;
                        $request[self::LIMIT_PARAM] += 0;

                        if ($request['offset'] < 0) $request['offset'] = 0;
                        if ($request[self::LIMIT_PARAM] <= 0) $request[self::LIMIT_PARAM] = 10; // 默认10条

                        $a_p_t_d_arr['d'][$GLOBALS['cfg']['__OFFSET__']] = $request['offset'];
                        $a_p_t_d_arr['d'][$GLOBALS['cfg']['__LIMIT__']]  = $request[self::LIMIT_PARAM];// TODO 改成limit
                    }

                    // 额外的搜索字段 TODO, 临时hardcode
                    if ('aups_t002' == $l_biao || 'news' == $l_biao || 2 == @$request['db_table_id']){
                        $allow_fields = array('s_shu_chengshi'); // 城市资讯
                        foreach ($allow_fields as $field_name) {
                            if (array_key_exists($field_name, $request))
                                $a_p_t_d_arr['d'][$field_name] = $request[$field_name];
                        }
                    }
                }
            }
          break;
      }
    }

    // 如果有其他动作需要处理的，统一在此处处理
    if (isset($a_p_t_d_arr) && !empty($a_p_t_d_arr)) {
      // 依据指定的项目、表、文档信息，从数据库中获取完整信息
      $l_ptd_info_arr = getProInfoTblInfoDocInfo($dbR, $a_p_t_d_arr);

      // type主要就是add,edit,list,del四种
      if ("list"==$request["type"]) {
        if ("user"==$request["action"]) {
          // 涉及到管理员表的管理员账号也不能
          $a_p_t_d_arr2 = array(
            'p'=>array('name_cn'=>"通用发布系统"),
            't'=>array('name_cn'=>"用户表"),
            'd'=>array('username'=>$request["username"]),
          );
          $l_ptd_info_arr2 = getProInfoTblInfoDocInfo($dbR, $a_p_t_d_arr2);
          $l_user2 = $l_ptd_info_arr2["d_info"];

          // 检查用户是否存在的语句
          $l_user = $l_ptd_info_arr["d_info"];
          if (PEAR::isError($l_user) || PEAR::isError($l_user2)) {

          } else {
            // 检查用户是否成功
            if (empty($l_user) && empty($l_user2)) {
              $l_content->ret = 0;  // 两张表中都不存在，可以注册
            }else {
              $l_content->ret = 1; // 该用户名存在，或其他错误
            }
            // 检查用户名和密码是否正确
            //
          }
        } else if ("ly"==$request["action"]) {
          if ('ly_yule'==$l_biao) {
            // 查询的限制条件, 返回的字符串类型
            $l_allow_ziduan = array('news_p_id','news_t_id','news_id','parent_id','ip','l');  //
            $a_p_t_d_arr2 = array(
              'p'=>array('name_cn'=>"评论频道"),
              't'=>array('name_cn'=>"娱乐频道评论"),
              'd'=>cArray::array__slice($request,$l_allow_ziduan),
            );
            // 获取具体的评论
            $l_ptd_info_arr2 = getProInfoTblInfoDocInfo($dbR, $a_p_t_d_arr2);

            $l_ly_s = $l_ptd_info_arr2["d_info"];  // 不为空的话是二维数组, 就是可能有多条记录
            //
          }

          // 检查用户是否存在的语句
          if (PEAR::isError($l_ptd_info_arr2)) {
              $l_content->ret = 2;
              $l_content->msg = var_export($l_ptd_info_arr2, true);
          } else {
            // 拼装成一段json串
            // print_r($l_ptd_info_arr2);
            $l_comment_json = $this->formatCommentArr($l_ly_s,$l_ptd_info_arr2["t_info"],$l_ptd_info_arr2["p_info"]);
            //print_r($l_comment_json); exit;
            $l_content->ret = 0;
            $l_content->newPosts = $l_comment_json;
          }
        } else {
            // 其他类型的
            if (array_key_exists('d_info', $l_ptd_info_arr) && $l_ptd_info_arr['d_info']) {
                // TODO 支持列表和单行数据
                if (count($l_ptd_info_arr['d_info']) == 1 && isset($l_ptd_info_arr['d_info'][0]) && !isset($request[self::LIMIT_PARAM])) {
                    $l_content->data = $l_ptd_info_arr['d_info'][0];
                } else $l_content->data = $l_ptd_info_arr['d_info'];
            }
        }
      } else if ("add"==$request["type"]) {
        //if ("user"==$request["action"]) {
        // 添加用户，需要将明文的密码进行md5一下, 在算法中已经实现
        //}
        // 添加文档的一种方法, doPath强制为文档添加, 项目id、表id都要同时赋值
        $l_doPath = "document_add";
        $if_is_open_page = 1;  // 跳过身份认证进入后续操作
        $l_ret = MoNiDO($l_doPath, $l_ptd_info_arr,$request,$response,$form,$get,$cookie, $if_is_open_page);

        // 依据返回的结果进行判断成功如否, 以及相关结果数组
        $l_content->Assign($l_ret['ret']);

        // 注册成功就为其登录，通常需要验证身份是否合法，暂时省略身份验证，直接为其登录
        // 由于涉及到主从同步时间的关系，此处直接使用现成的数据进行判断
        /*if (isset($l_content->id) && isset($l_content->username)) {
          $userR = new AdminUserR();
          // 强制种上sid的cookie
          $l_content->md5pass = 1;  // 需要说明是md5的密码
          $userR->SetSessionCookieByUserArr(cArray::ObjectToArray($l_content), array("remember"=>1));
        }*/

        if (isset($request['back_url']) && !empty($request['back_url'])) {
          $response['ret'] = array('ret'=>0);
          return $request['back_url'];
        }
        // 此处通常都是表单提交以后，应该重定向到相应的页面去；如果失败依然在注册的页面，并且显示错误信息
        // 也可能是接口, 因此返回需要依据给出的 cont_type 类型进行判定
        $response['ret'] = array('ret'=>0);
        if (!empty($request["cont_type"])){
          // 投票部分
          $response['html_content'] = $l_content;
          $response['ret'] = $response['html_content'];
          return null;
          // if (false !== strpos($_SERVER['HTTP_REFERER'], "admin.wanda.cn") || "admin.wanda.cn"==trim($_SERVER['HTTP_HOST']))
        }else {
          return "http://" . $GLOBALS['cfg']['WEB_DOMAIN']. "/";
        }
      } else if ("edit"==$request["type"]) {
        // 更新用户资料
        require_once('UpdateInfo.php');
        UpdateInfo($response, $l_ptd_info_arr, $request, $form,$get, $cookie, $if_is_open_page);
      }
    } else if ("memory_data"==$request["action"]){
      // 可能无需连数据库，而是其他数据库类型，例如key-value数据
      // 则只需要传递key即可返回相应value
      $l_list = array();
      if ("" != trim($request['list'])) $l_list = explode(",", $request['list']);

      $l_nosql = new Nosql("redis");

      // 逐项从内存中获取，并一数组形式存放结果
      if (!empty($l_list) && extension_loaded('redis')) {
        // 从redis或memcached中获取数据
        foreach ($l_list as $l_r_key){
          $l_content->$l_r_key = $l_nosql->get($l_r_key);
        }
      }
    }

    // 最终结果
    $response['html_content'] = $l_content;
    $response['ret'] = $response['html_content'];//array('ret'=>0);
    return null;
  }

  // 返回一个数值，用于json串
  function formatCommentArr($a_arr,$t_info,$p_info){
    $l_arr = array();

    // 从数据库中获取数据，虽然效率可能低下了点，以后优化????
    $dbR = new DBR($p_info);
    $dbR->SetCurrentSchema($p_info["db_name"]);
    $dbR->table_name = $t_info["name_eng"];


    if (is_array($a_arr) && !empty($a_arr)) {
      // 每条评论可能还有父级评论，需要同时获取其父级评论

      // 拼装数组, 每条数据, 参考http://my.ni9ni.com/tests/163_comment_json.php的newPosts
      // 最外面的数组是从0开始的数字键名, 每条评论中由所有上级评论组成的从1开始的一个数组对象
      foreach ($a_arr as $l_val){
        //  需要获取到 $l_val 的所有父级评论，从而组成新数组, 新数组应该是从1开始的索引数组
        $l_arr[] = $this->get_parent_comment_by_comment($dbR, $p_info, $t_info, $l_val);
      }
    }

    return $l_arr;
  }

  // 需要获取到 $l_val 的所有父级评论，从而组成新数组, 新数组应该是从1开始的索引数组
  function get_parent_comment_by_comment(&$dbR, $p_info, $t_info, $l_val){
    $l_arr = array();

    // 需要寻找该评论的父级评论了
    if (is_array($l_val) && !empty($l_val)) {
      $l_arr_tmp = array();
      $l_arr_tmp[1] = $l_val;
      // 逐级获取上级元素
      get_parents($l_arr_tmp, $dbR, $p_info, $t_info, $l_val);
      // 需要重新倒叙一下, 并且数字从1开始, 1楼，2楼...
      $l_arr = array_reverse($l_arr_tmp);  // 索引是从0开始的
      array_unshift($l_arr, "");  // 在开头插入空,使得有用部分的索引从1开始
      unset($l_arr[0]);  // 相当于将数组的索引加1
      $this->onlyFormat($l_arr, $p_info, $t_info);  // 拼装需要的字段，去掉多余的字段
      /*if ($l_val["id"]>13) {
        print_r($l_arr);  // 调试信息
      }*/
    }
    return $l_arr;
  }

  function onlyFormat(&$a_arr, $p_info, $t_info){
    if (is_array($a_arr) && !empty($a_arr)) {
      // 只需要的几个字段,
      $l_tmp = array(
        "id",  // 该评论的id
        "ip",  // ip地址
        "b",  // 评论内容
        "f",  // 平台类型，手机还是其他
        "a_cn",  // 城市中文名
        "l",  // 楼层
        "k",  // 外网链接
        "news_p_id",
        "news_t_id",
        "news_id",
        "parent_id",  // 父级id
      );

      // 需用用到nosql数据库获取顶的数目
      $l_nosql = new Nosql("redis");

      // 对数组处理只保留上述需要的字段
      foreach ($a_arr as $l_key => $l_tmp_arr){
        $l_new_arr = cArray::array__slice($l_tmp_arr,$l_tmp);
        $l_ip = $l_tmp_arr["ip"];
        $l_p_t_id = $p_info["id"] . "_". $t_info["id"]. "_". $l_tmp_arr["id"];
        $l_new_arr["t"]     = $l_tmp_arr["createdate"] . " " . $l_tmp_arr["createtime"];
        $l_new_arr["p_t_id"]   = $l_p_t_id;
        $l_new_arr["ding"]     = $l_nosql->get("vote_".$l_p_t_id."_up")+0;  // 顶贴的数目，需要从redis中获取????
        $l_new_arr["ip"]     = substr($l_ip, 0, strpos($l_ip, '.', strpos($l_ip,".")+1)) . ".*.*";  // ip需要特殊处理一下,只保留前两位
        $a_arr[$l_key] = $l_new_arr;
      }
    }

    return $a_arr;
  }

  // 登录验证的一些接口
  function check_login(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $l_content){
    // 具体接口的登录功能
    session_start(); // 必须要，不然 $_SESSION 将是空的
    $username = trim(urldecode($request["username"]));
    $md5_pass = trim(urldecode($request["password"]));
    $logout = isset($request["logout"])?$request["logout"]:0;
    if ($logout) {
      // 可能是退出登录, 退出登录主要清除sid的cookie
      $l_doPath = "logout";
      $if_is_open_page = 1;  // 跳过身份认证进入后续操作
      $l_response = MoNiDO($l_doPath, array(),$request,$response,$form,$get,$cookie, $if_is_open_page);

      // 依据返回的结果进行判断成功如否, 以及相关结果数组
      $l_content = $l_response['ret'];  // 退出成功
    }else if ($username&&$md5_pass) {
      // 此处的密码是md5以后的密码, 具备接口的登录功能
      $userR= new AdminUserR;
      $l_rlt = $userR->getLocUserexistByuser(array_merge($request,array("md5pass"=>1)));

      if (0==$l_rlt['ret']) {  // 成功
        $l_userinfo = $l_rlt['user_info'];
        $userR->SetSessionCookieByUserArr($l_userinfo, $request);

        // 为了安全起见, 只保留username,email,nickname等几项数据
        $l_content = array('ret'=>0,'id'=>$l_userinfo['id'],'username'=>$l_userinfo['username'],'email'=>$l_userinfo['email']);
      } else if (isset($_SESSION['user']['username']) && $_SESSION['user']['username']) {
        $l_content = array('ret'=>0,'id'=>$_SESSION['user']['id'],'username'=>$_SESSION['user']['username'],'email'=>trim(@$_SESSION['user']['email']));
      } else {
        $l_content = array('ret'=>1);
      }
    } else {
      // sid 方式
      $sid = $cookie["sid"];
      if (!empty($sid)) {
        $userR= new AdminUserR;
        $l_rlt = $userR->IsSid($sid);

        // 只有返回的是0的时候才是正确的
        if (0==$l_rlt['ret']) {
          $l_userinfo = $l_rlt['user_info'];
          // 为了安全起见, 只保留username,email,nickname等几项数据
          $l_content = array('ret'=>0,'id'=>$l_userinfo['id'],'username'=>$l_userinfo['username'],'email'=>$l_userinfo['email']);
        }else{
          $l_content = array('ret'=>1);
        }
      }else{
        $l_content = array('ret'=>1);
      }
    }

    return $l_content;
  }
}

/**
 * 获取节点的所有父节点
 *
 * @param int $a_child  需要寻找父节点的那个子节点
 * @return array     二维数组, 并且第一维的索引必须是从1开始的数字键名
 */
function get_parents(&$l_path, &$dbR, $p_info, $t_info, $l_val){
  $dbR->SetCurrentSchema($p_info["db_name"]);
  $dbR->table_name = $t_info["name_eng"];

  // 寻找父节点
  $l_row = $dbR->getOne("where id=".$l_val["parent_id"]);//父子节，最多就一条，未找到则为false
  //echo $dbR->getSQL();
  //var_dump($l_row);
  if (!empty($l_row)) {
    $l_path[] = $l_row;
    get_parents($l_path, $dbR, $p_info, $t_info, $l_row);
  }

  return $l_path;
}
