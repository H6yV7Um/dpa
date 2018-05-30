<?php
$GLOBALS["ACTION_CONFIGS"] = array(
    'default_map'=>array(
        'validate'      =>  true,
        'forwards'      =>  array('sysError'=> array('name'=>'sysError','path'=>'sysError.html','redirct'=>true))
    ),
    // http://vip.stock.finance.sina.com.cn/quotes_service/api/json_v2.php/Market_Center.getHQNodeData?page=1&num=40&sort=symbol&asc=1&node=new_jrhy&_s_r_a=init
    // 以后所有的开放网页都可以用这项，或者用open，主要参数则是p_id,t_id等参数获取,以及type通过获取json或者其他返回类型
    'o'=>array(
        'validate'      =>  false,
        'forwards'      =>  array(
            'sysError'=> array('name'=>'sysError','path'=>'sysError.html','redirct'=>true)
        )
    ),
    'user_add'=>array(
        'validate'      =>  false,
        'forwards'      =>  array(
            'sysError'=> array('name'=>'sysError','path'=>'sysError.html','redirct'=>true)
        )
    ),
    'login'=>array(
        'validate'    =>  true,
        'forwards'      =>  array(
            'success'=> array('name'=>'success','path'=>'main.php','redirct'=>true),
            //'auth_fail'=> array('name'=>'auth_fail','path'=>'msg_saveorder.php'), ajax
            'failure'=> array('name'=>'failure','path'=>'login.php'),
            'sysError'=> array('name'=>'sysError','path'=>'sysError.html','redirct'=>true)
        )
    )
);
