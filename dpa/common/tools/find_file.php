<?php
/**
  使用方法：

php D:/www/dpa/common/tools/find_file.php -p "D:/www/wanda_git/ffan/xadmin" -n ".svn"

php D:/www/dpa/common/tools/find_file.php -p "D:/www/wanda_git/ffan/xadmin" -r "/\.svn/i"

php D:/www/dpa/common/tools/find_file.php -p "D:/www/wanda_git/ffan/xadmin/scripts" -r "/st|ck/i"

php D:/www/dpa/common/tools/find_file.php -p "D:/www/wanda_git/ffan/xadmin/scripts" -r "/st|ck/i" -w "D:/www/wanda_git/ffan/xadmin/scripts/.svn"

 */
require_once(dirname(dirname(__DIR__)) . "/configs/system.conf.php");
require_once("common/functions.php");
require_once("common/Files.cls.php");

define("str_pad_num", 66);  // 44

// 获取参数列表
if (version_compare(PHP_VERSION, '4.3.0', '<'))
    exit('please use php4.3.0 or later!');
$_o = getopt('p:c:r:n:m:w:');

// 几个全局变量
$G_ALL_DATA   = array();
$G_biao_show   = 0;
$G_no_need_dir = array();  // need not find dir
$G_no_need_dir = !empty($_o['w']) ? array($_o['w']) : $G_no_need_dir; // need not find dir

// "/TB_OBJECT_[0-9]+|MT_COL_TEXT|TB_CLASS_PROPERTY|TB_COM_COLLECTION|TB_OBJECT_CLASS/i";
$G_regex = (array_key_exists('r', $_o)) ? $_o['r'] : '';
$G_findstr = (array_key_exists('n', $_o)) ? strtolower($_o['n']) : '';
if (!$G_regex && !$G_findstr) {
    exit(' find string is empty！ ');
}

$G_replace = (!empty($_o['m'])) ? $_o['m'] : '';

$G_COMMON_PATH = (!empty($_o['p'])) ? $_o['p'] : 'D:/www/wanda_git/ffan/xadmin';

$l_common = !empty($_o['c']) ? $_o['c'] : '';
$l_common = $l_common ? '/' . $l_common : '';
$l_source_path = $G_COMMON_PATH . $l_common;

if (!file_exists($l_source_path)) exit($l_source_path . ' path not exists!');
// 优先使用严格匹配
if ($G_findstr)
    findStrTransPath($l_source_path, true);
else
    transPath($l_source_path, true);

foreach ($GLOBALS['G_ALL_DATA'] as $l_key => $l_val) {
    $l_pref = (1 + $l_key) . ". " . $l_val[0];
    if ( 0==$l_key )
        echo str_pad('序号       文件路径', str_pad_num) . " " . NEW_LINE_CHAR;
    echo str_pad($l_pref, str_pad_num) . " " . NEW_LINE_CHAR;
}


echo $G_biao_show . " 个搜索字符" . NEW_LINE_CHAR;

function findStrTransPath($source_path, $son=false) {
    $d = @dir($source_path);
    if ($d) {
        while (false !== ($_file = $d->read())) {
            if ('.' != $_file && '..' != $_file) {//  过滤掉 . .. 这两项，但其他隐藏文件可以查看如：.svn
                $l_tmp_file = $source_path . "/" . $_file;

                // 过滤掉禁止扫描的目录
                if ($GLOBALS['G_no_need_dir'] && in_array($l_tmp_file, $GLOBALS['G_no_need_dir']))
                    continue;

                if ($GLOBALS['G_findstr'] == strtolower($_file)) {
                    $GLOBALS['G_biao_show']++;

                    $GLOBALS['G_ALL_DATA'][] = array(
                        str_replace($GLOBALS['G_COMMON_PATH'] . "/", "", str_replace('\\', "/", $source_path . "/" . $_file))
                    );
                }

                // 如果是目录，还得继续查找
                if($son && is_dir($l_tmp_file)) {
                    findStrTransPath($l_tmp_file, $son);
                }
            }
        }
        $d->close();
    }
}



function transPath($source_path, $son=false) {
    $d = @dir($source_path); // 如果没有权限，会返回false // NULL with wrong parameters, or FALSE in case of another error
    if ($d) {
        while (false !== ($_file = $d->read())) {
            if ('.' != $_file && '..' != $_file) {//  过滤掉 . .. 这两项，但其他隐藏文件可以查看如：.svn
                $l_tmp_file = $source_path . "/" . $_file;

                // 过滤掉禁止扫描的目录
                if ($GLOBALS['G_no_need_dir'] && in_array($l_tmp_file, $GLOBALS['G_no_need_dir']))
                    continue;

                // 采用正则表达式
                preg_match($GLOBALS['G_regex'], $_file, $matches);
                //print_r($matches);

                if (preg_match($GLOBALS['G_regex'], $_file, $matches)) {
                    $GLOBALS['G_biao_show']++;

                    $GLOBALS['G_ALL_DATA'][] = array(
                        str_replace($GLOBALS['G_COMMON_PATH'] . "/", "", str_replace('\\', "/", $source_path . "/" . $_file))
                    );
                }

                // 如果是目录，还得继续查找
                if($son && is_dir($l_tmp_file)) {
                    transPath($l_tmp_file, $son);
                }
            }
        }
        $d->close();
    }
}


