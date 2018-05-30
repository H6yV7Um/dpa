<?PHP
/**
 * Files.cls.php
 * Create file, Create dir
 * file_put_contents 可能会更加方便
 *  但是只能在 PHP 5 中使用
 */

class Files {
  var $dir;        //文件存放目录，目录名(不带/)
  var $rootdir;    //html文件存放根目录，目录名(不带/)
  var $name;       //html文件存放路径
  var $dirname;    //指定的文件夹名称
  var $url;        //获取html文件信息的来源网页地址
  var $time;       //html文件信息填加时的时间
  var $dirtype;    //目录存放方式
  var $nametype;   //html文件命名方式

  //建立目地文件夹
  function createdir($dir='')
  {
    if (!is_dir($dir)){
      mkdir($dir,0775,true);
    }
  }

  /**
  * append content to file
  * @access public
  * @param string $content content will be writed inot file
  * @param string $filePath file
  * @return string return '' when success, return description string when fail
  */
  function overwriteContent( $content, $filePath ){
    return $this->writeContent($content, $filePath, true);
  }

  /**
  * append content to file
  * @access public
  * @param string $content content will be writed inot file
  * @param string $filePath file
  * @param string $mode opearate mode
  * @return string return '' when success, return description string when fail
  */
  function writeContent( $content, $filePath, $overwrite=false, $mode='w' ){

    if ( !file_exists($filePath) || $overwrite ) { // || is_writable($filePath)
      $this->createdir(dirname($filePath)); // 创建目录
      if ($overwrite) {
        if (is_link($filePath)) unlink($filePath);  // 符号连接必须先删除，不然无法覆盖之
        file_put_contents($filePath, $content);
      }else {
        file_put_contents($filePath, $content, FILE_APPEND);
      }
      return '';
    } else {
      return "file $filePath isn't writable";
    }
  }

  /**
  * 取得文件的扩展名
  * @access public
  * @param $filename file name
  * @return string file extense name
  */
  function getExt($filename){
    $ext = substr( strrchr($filename,'.'), 1 );
    return $ext;
  }

    /**
     * @access public
     * @param string $rsFile 文件路径
     * @param string $rsOption
     * @return string|boolean
     */
    function readContent( $rsFile, $rsOption = 'wt' ) {
        return @file_get_contents($rsFile,$rsOption);
    }

    /**
   * 此处的策略是将php代码包含到程序中来，策略类似于include, 但由于这些phpcode不是文件而是内存中的
   *
   * asp_tags  short_open_tag
   *
  1.  <?php echo 'if you want to serve XHTML or XML documents, do like this'; ?>

  2.  <script language="php">
          echo 'some editors (like FrontPage) don\'t
                like processing instructions';
      </script>

  3.  <? echo 'this is the simplest, an SGML processing instruction'; ?>
      <?= expression ?> This is a shortcut for "<? echo expression ?>"

  4.  <% echo 'You may optionally use ASP-style tags'; %>
      <%= $variable; # This is a shortcut for "<% echo . . ." %>
   *
   * @param string $php_code_cont
   * @param string $path
   */
  function phpcontent2phpfile($php_code_cont,$a_filename,$a_path="./",$overwrite=true,$tar_char="utf8"){
    // 先将内容写入文件中，注意添加<?php 标签，然后include进来即可完成任务
    // 涉及到字符编码的问题。需要进行必要的转码
    $l_cont = ltrim($php_code_cont);
    if ('<?php' !== strtolower(substr($l_cont, 0, 5)) &&
      '<?' !== strtolower(substr($l_cont, 0, 2))) {
      // 先只判断这两种，见上面的4种方式
      $l_sep =  ( 'WIN' === strtoupper(substr(PHP_OS, 0, 3)) )? "\r\n":"\n";
      $l_cont = '<?php' . $l_sep . convStrOrArrCharacter2other($l_cont,"gb2312",$tar_char);   // 加上前缀
    }

    //if (!is_dir($a_path)) mkdir($a_path,0777,true);  // 先建立目录
    $l_path = rtrim($a_path," /\\") . DIRECTORY_SEPARATOR . $a_filename;
    //file_put_contents( $l_path, $l_cont);
    $this->writeContent($l_cont, $l_path, $overwrite);

    return $l_path;
  }

    // 删除多级目录, 功能同下，只是多了一些判断，尽量用这个。
    function removeDir($dirName) {
      $result = false;
      if( !is_dir($dirName) ) {
        trigger_error("目录名称错误", E_USER_ERROR);
      }
      $handle = @opendir($dirName);
      while(($file = readdir($handle)) !== false) {
        if($file != '.' && $file != '..') {
          $dir = $dirName . DIRECTORY_SEPARATOR . $file;
          is_dir($dir) ? $this->removeDir($dir) : unlink($dir);
        }
      }
      closedir($handle);
      $result = rmdir($dirName) ? true : false;
      return $result;
    }
    // 删除多级目录
    function rmdirs($dir) {
      $d = @dir($dir);
      if ($d) {
          while (false !== ($child = $d->read())){
            if($child != '.' && $child != '..'){
              if(is_dir($dir.'/'.$child))
              $this->rmdirs($dir.'/'.$child);
              else unlink($dir.'/'.$child);
            }
          }
          $d->close();
      }
      rmdir($dir);
    }
}