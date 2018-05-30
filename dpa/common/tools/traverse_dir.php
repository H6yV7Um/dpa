<?php
/**
  遍历目录下的所有文件，并按照格式输出，使用方法：
php D:/www/dpa/common/tools/traverse_dir.php -p "D:/www/wanda_git/ffan/xadmin" -s ".log,.txt"
php D:/www/dpa/common/tools/traverse_dir.php -p "D:/www/wanda_git/ffan/xadmin/scripts" -w ".svn,.git"
php /home/sre/test/traverse_dir.php -p /var/wd/wrs/logs/ -s .log

 */
define('NEW_LINE_CHAR', "\r\n");

// 获取参数列表
if (version_compare(PHP_VERSION, '4.3.0', '<'))
    exit('please use php4.3.0 or later!');
$_o = getopt('p:w:s:');

// 几个全局变量
$G_ALL_DATA   = array();
$GLOBALS['not_exist'] = array();
$GLOBALS['exist_file'] = array();
$G_no_need_dir = isset($_o['w']) ? explode(',', str_replace(' ', '', $_o['w'])) : array(); // 过滤的路径 .svn .git等
$G_suffix = isset($_o['s']) ? explode(',', str_replace(' ', '', $_o['s'])) : array(); // 文件后缀要求, 只统计.log文件

$G_COMMON_PATH = (isset($_o['p'])) ? $_o['p'] : __DIR__; // 路径，必须
$l_common = isset($_o['c']) ? $_o['c'] : '';
$l_common = $l_common ? '/' . $l_common : '';
$l_source_path = $G_COMMON_PATH . $l_common;

if (!file_exists($l_source_path))
    exit($l_source_path . ' path not exists!');
traversePath($l_source_path, true);

foreach ($GLOBALS['G_ALL_DATA'] as $l_key => $l_val) {
    // 如果 log_movie 目录中对应的文件不存在，则记录下来
    $log_movie_path = str_replace('/var/wd/wrs/logs', '/var/wd/log_movie/logs', $l_val[0]);
    if (!file_exists($log_movie_path)) {
        $GLOBALS['not_exist'][] = $log_movie_path;
    } else {
        // 按照日期分开
        if (false !== strpos($l_val[0], '2017-06-20')) {
            // 20号的需要追加到log_movie日志后面
            // cat /var/wd/wrs/logs/biz/biz-2017-06-20.log >> /var/wd/log_movie/logs/biz/biz-2017-06-20.log
            $GLOBALS['exist_file']['2017-06-20'][] = $l_val[0];
            echo 'cat ' . $l_val[0] . ' >> ' . $log_movie_path . NEW_LINE_CHAR;
        } else if (false !== strpos($l_val[0], '2017-06-22')) {
            // 22日的需要追加到log_movie日志前面
            // cat /var/wd/wrs/logs/biz/biz-2017-06-22.log /var/wd/wrs/logs/biz/biz-2017-06-22.log.log_movie > /var/wd/log_movie/logs/biz/biz-2017-06-22.log
            $GLOBALS['exist_file']['2017-06-22'][] = $l_val[0];
            echo 'cat ' . $l_val[0] . ' ' . $l_val[0] . '.log_movie' . ' > ' . $log_movie_path . NEW_LINE_CHAR;
        } else {
            // 其他
            $GLOBALS['exist_file']['other'][] = $l_val[0];
            echo 'cp ' . $log_movie_path . ' ' . $l_val[0] . '.log_movie' . NEW_LINE_CHAR;
        }
    }
}

if ($GLOBALS['not_exist']) {
    echo NEW_LINE_CHAR . 'path not exist:' . NEW_LINE_CHAR;
    foreach ($GLOBALS['not_exist'] as $l_key => $log_movie_path) {
        // mv /var/wd/wrs/logs/cineapi/chenxing-2017-06-22.log /var/wd/log_movie/logs/cineapi/chenxing-2017-06-22.log
        $l_val = str_replace('/var/wd/log_movie/logs', '/var/wd/wrs/logs', $log_movie_path);
        echo 'mv ' . $l_val . ' ' . $log_movie_path . NEW_LINE_CHAR;
    }
}
if ($GLOBALS['exist_file']['2017-06-20']) {
    // 20号的需要追加到log_movie日志后面
    // cat /var/wd/wrs/logs/biz/biz-2017-06-20.log >> /var/wd/log_movie/logs/biz/biz-2017-06-20.log
    echo NEW_LINE_CHAR . '2017-06-20:' . NEW_LINE_CHAR;
    foreach ($GLOBALS['exist_file']['2017-06-20'] as $l_key => $l_val) {
        $log_movie_path = str_replace('/var/wd/wrs/logs', '/var/wd/log_movie/logs', $l_val);
        echo 'cat ' . $l_val . ' >> ' . $log_movie_path . NEW_LINE_CHAR;
    }
}
if ($GLOBALS['exist_file']['2017-06-22']) {
    // 22日的需要追加到log_movie日志前面
    // cat /var/wd/wrs/logs/biz/biz-2017-06-22.log /var/wd/wrs/logs/biz/biz-2017-06-22.log.log_movie > /var/wd/log_movie/logs/biz/biz-2017-06-22.log
    echo NEW_LINE_CHAR . '2017-06-22:' . NEW_LINE_CHAR;
    foreach ($GLOBALS['exist_file']['2017-06-22'] as $l_key => $l_val) {
        $log_movie_path = str_replace('/var/wd/wrs/logs', '/var/wd/log_movie/logs', $l_val);
        echo 'cat ' . $l_val . ' ' . $l_val . '.log_movie' . ' > ' . $log_movie_path . NEW_LINE_CHAR;
    }
}

//print_r($GLOBALS['exist_file']);
echo 'total ' . count($GLOBALS['G_ALL_DATA']) . ' not_exist: ' . count($GLOBALS['not_exist']) . NEW_LINE_CHAR;

function traversePath($source_path) {
    $source_path = rtrim(str_replace('\\', '/', $source_path), '/'); // 路径分隔符号强制用/，
    $d = @dir($source_path); // 如果没有权限，会返回false // NULL with wrong parameters, or FALSE in case of another error
    if ($d) {
        while (false !== ($_file = $d->read())) {
            if ('.' != $_file && '..' != $_file) { //  过滤掉 . .. 这两项，但其他隐藏文件可以查看如：.svn
                $l_tmp_file = $source_path . '/' . $_file;

                // 过滤掉禁止扫描的目录
                if ($GLOBALS['G_no_need_dir'] && in_array($_file, $GLOBALS['G_no_need_dir']))
                    continue;

                // 如果是目录，还得继续查找
                if(is_dir($l_tmp_file)) {
                    traversePath($l_tmp_file);
                } else {
                    if ($GLOBALS['G_suffix'] && !in_array(getSuffix($_file), $GLOBALS['G_suffix']))
                        continue;
                    // 统计文件个数
                    $GLOBALS['G_ALL_DATA'][] = array(
                        $l_tmp_file,
                        str_replace($GLOBALS['G_COMMON_PATH'] . '/', '', $l_tmp_file),
                    );
                }
            }
        }
        $d->close();
    }
}

// 获取文件后缀 .log
function getSuffix($url) {
    return substr(basename($url), strrpos(basename($url), '.'));
}
