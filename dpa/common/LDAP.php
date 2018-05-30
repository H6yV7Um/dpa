<?php
/**
 * 采用LDAP(轻量级目录存储协议)方式认证
 * @param string $uid
 * @param string $pwd
 * @abstract  登录成功返回1，失败返回0
 * @return int
*/
function LDAPAuth($uid, $pwd, $host='10.210.96.10', $port=389) {
  $bind_rdn = "uid=" . $uid . ",ou=people,o=staff.sina.com.cn,o=usergroup";
    $ds = ldap_connect($host, $port);
    if ($ds) {
        if ( @ldap_bind($ds, $bind_rdn, $pwd) ) {
            return 1;
        } else {
            return 0;
        }
        ldap_close($ds);
    } else {
        return 0;
    }
    return 0;
}




