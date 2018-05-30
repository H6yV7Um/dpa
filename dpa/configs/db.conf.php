<?php
/**
 * 数据库配置信息，涉及到主从库、分表
 * 主从库信息需要在apache中配置  ,以后也可以不用包含此文件,但php命令行模式下运行的时候需要此文件
 * 为了适合cli模式，单纯在apache中设置没有用
 */
function __gener_conf($conf_path,$conf_file,$db_w="",$db_r=""){
    global $_SERVER;
    $configs = __fetch_config($conf_path,$conf_file);
    if (empty($configs)) {
        $_SERVER["SRV_DB_HOST_W"] = $_SERVER["SINASRV_DB6_HOST"];
        $_SERVER["SRV_DB_PORT_W"] = $_SERVER["SINASRV_DB6_PORT"];
        $_SERVER["SRV_DB_USER_W"] = $_SERVER["SINASRV_DB6_USER"];
        $_SERVER["SRV_DB_PASS_W"] = $_SERVER["SINASRV_DB6_PASS"];
        $_SERVER["SRV_DB_NAME_W"] = $_SERVER["SINASRV_DB6_NAME"];

        $_SERVER["SRV_DB_HOST_R"] = $_SERVER["SINASRV_DB6_HOST_R"];
        $_SERVER["SRV_DB_PORT_R"] = $_SERVER["SINASRV_DB6_PORT_R"];
        $_SERVER["SRV_DB_USER_R"] = $_SERVER["SINASRV_DB6_USER_R"];
        $_SERVER["SRV_DB_PASS_R"] = $_SERVER["SINASRV_DB6_PASS_R"];
        $_SERVER["SRV_DB_NAME_R"] = $_SERVER["SINASRV_DB6_NAME_R"];
    }else {
        if ((""!=$db_w && !array_key_exists($db_w,$configs)) || (""!=$db_r && !array_key_exists($db_r,$configs))) {
            // 需要输出信息，或者退出程序并报错。
            exit(__FILE__ . " LINE:" . __LINE__ . " " . $db_w . " ". $db_r.  " not in configs! ");
        }
        // write
        if(""!=$db_w) __server_db_host($_SERVER,$configs[$db_w],"W");
        // read
        if(""!=$db_r) __server_db_host($_SERVER,$configs[$db_r],"R");
    }
}
function __server_db_host(&$l_srv,$a_arr,$l_k="W"){
    // write
    if(array_key_exists("dsn",$a_arr)){
        $b = parse_url($a_arr["dsn"]);
    }else if(array_key_exists("mysql_dsn",$a_arr)){
        $b = parse_url($a_arr["mysql_dsn"]);
    }else {
        $b = array();
        $b["host"] = $a_arr["db_host"];
        $b["port"] = $a_arr["db_port"];
        $b["user"] = $a_arr["db_user"];
        $b["pass"] = $a_arr["db_pass"];
        $b["path"] = "/".$a_arr["db_name"];
    }
    $l_srv["SRV_DB_HOST_".$l_k] = $b["host"];
    $l_srv["SRV_DB_PORT_".$l_k] = $b["port"];
    $l_srv["SRV_DB_USER_".$l_k] = $b["user"];
    $l_srv["SRV_DB_PASS_".$l_k] = $b["pass"];
    $l_srv["SRV_DB_NAME_".$l_k] = substr($b["path"],1);
}
__gener_conf($GLOBALS['cfg']['INI_CONFIGS_PATH'],$GLOBALS['cfg']['INI_DB_DSN_CONFIGS_FILE'],$GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_W'],$GLOBALS['cfg']['SYSTEM_DB_DSN_NAME_R']);
