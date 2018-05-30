#/usr/bin/env php
<?php
/**
 * 比较两个目录的下的文件个数，文件名的差异
 *
 * -p 路径1，默认为当前目录 ./
 * -t 路径2，

php P:/develope/www/dpa/eswine/common/tools/two2diff.php -p common/tools

 */

require_once 'Console/Getopt.php';
$_options = Console_Getopt::getopt($argv, 'p:t:m:n:', array());
$_o = array();
if (!PEAR::isError($_options)) {
  foreach ($_options[0] as $l_v){
    $_o[$l_v[0]] = $l_v[1];
  }
}

main($_o);

function main($_o){
  //$path1 = (!empty($_o["t"])) ? $_o["t"] : "./";

  $path = (!empty($_o["p"])) ? $_o["p"] : "";
  $path2 = "P:/develope/www/dpa/__20120605/" . $path;
  $path1 = "P:/develope/www/dpa/eswine/" . $path;

  $l_arr1 = transPath($path1);
  $l_arr2 = transPath($path2);
  print_r($l_arr1);
  print_r($l_arr2);

  //
  $l_diff1 = array_diff($l_arr1,$l_arr2);
  sort($l_diff1);
  $l_diff2 = array_diff($l_arr2,$l_arr1);
  sort($l_diff2);
  print_r($l_diff1);
  print_r($l_diff2);
}

//
function transPath($source_path,$name=".svn"){
  $l_arr = array();

  // 如果是需要将某个目录下的所有文件转化一下，则需要遍历目录下文件
  $d = dir($source_path);
  if ($d) {
  while (false !== ($_file = $d->read())) {
    if ($_file != "." && $_file != "..") {//  只删除指定名称的文件或目录
      // 检查是目录还是文件
      if(!is_dir($source_path.DIRECTORY_SEPARATOR.$_file)){
        $l_arr[] = $_file;
      }
    }
  }
  $d->close();
  }
  return $l_arr;
}

// 模板目录下所有的文件进行内容对照, 分别找出跟指定的文件内容相同和不同的
function main2($_o){
  $path = (!empty($_o["p"])) ? $_o["p"] : "D:/www/dpa/WEB-INF/tpl";
  $tar = (!empty($_o["t"])) ? $_o["t"] : "add.php";

  $l_arr1 = transPath($path);

  // 逐个文件内容进行比较
  if (!empty($l_arr1)) {
    $l_org_cont = file_get_contents($path . "/" . $tar);

    $l_same = $l_diff = array();
    foreach ($l_arr1 as $l_file){
      $l_content = file_get_contents($path."/".$l_file);
      if (trim($l_org_cont) != trim($l_content)) {
        $l_diff[] = $l_file;
      }else $l_same[] = $l_file;
    }

    echo "same: \n"; print_r($l_same);
    echo "diff: \n"; print_r($l_diff);
  }
}
