<?php
ini_set("session.name", "SESSIONID");
if (isset($_GET[SESSIONID]) && ! empty($_GET[SESSIONID])) {
    $session_id = $_GET[SESSIONID];
} elseif (isset($_COOKIE[SESSIONID]) && ! empty($_COOKIE[SESSIONID])) {
    $session_id = Cookie::Get(SESSIONID);
} else {
    // SESSION 生成因子尽可能丰富些，与当前机器性质绑定，确保多机器生成的不同
    $sessFactor = '' . session_id() . $_SERVER['SERVER_ADDR'] . microtime() . uniqid();
    if (file_exists("/etc/passwd")) {
        $sessFactor .= filectime("/etc/passwd") . md5_file("/etc/passwd");
    }
    $session_id = md5($sessFactor);
    Cookie::Set(SESSIONID, $session_id);
}
session_id($session_id);
