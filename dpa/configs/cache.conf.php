<?php
/**
 * 直接用 $_SERVER 配置中的 memcache 配置即可，不需要考虑分配那台机器的问题
 * 以下这些需要配置到apache中，程序直接调用，不用包含此文件。但php命令行模式下运行的时候需要此文件
 */
$_SERVER["SRV_MEMCACHED_KEY_PREFIX"] = "__";
$_SERVER["SRV_MEMCACHED_HOST"] = "localhost";
$_SERVER["SRV_MEMCACHED_PORT"] = "11211";
$_SERVER["SRV_MEMCACHED_EXPI"] = 600;