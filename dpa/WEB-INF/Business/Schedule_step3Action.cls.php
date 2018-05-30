<?php
/**
 * Schedule_step3Action.cls.php
 */
require_once("configs/css_js_img.conf.php");
require_once("common/functions.php");
require_once('mvc/Action.cls.php');
require_once('mod/DBR.cls.php');

class Schedule_step3Action extends Action {
  /**
     *
     * @access public
     * @param array &$request
     * @param array &$files
     */
  function execute(&$actionMap,&$actionError,$request,&$response,$form,$get,$cookie, $files=array()){

    $id = (int)$request["id"];

    $per_line    = 10;
    $select_len = 10;

    $_arr = array();
    // 点击 上一步
    if (1==$request["rewind"]) {
      $_arr["minute"] = $this->gencheckboxInput(60,$per_line,"minute",0,"formatnum" ,'',$select_len,$request["minute"]);
      $_arr["hour"]   = $this->gencheckboxInput(24,$per_line,"hour",  0,"formatnum" ,'',$select_len,$request["hour"]);
      $_arr["day"]   = $this->gencheckboxInput(31,$per_line,"day",   1,"formatnum" ,'',0,$request["day"]);
      $_arr["month"]   = $this->gencheckboxInput(12,$per_line,"month", 1,"formatnum" ,'',0,$request["month"]);
      $_arr["week"]   = $this->gencheckboxInput(7, $per_line,"week",  1,"formatweek",'',0,$request["week"]);
      $_arr["forbidden_date"] = $request["forbidden_date"];
      $_arr["forbidden_timezone"] = $request["forbidden_timezone"];
      $_arr["forbidden_timezone_options"] = buildOptions(getTimezone(),$_arr["forbidden_timezone"]);  // 默认东八区
    }else {
      if ("update"==$request["_action"]) {
        $dbR = new DBR();
        $dbR->table_name = TABLENAME_PREF."schedule";
        $_arr = $dbR->getOne("where id=".$id);

        $_arr["minute"] = $this->gencheckboxInput(60,$per_line,"minute",0,"formatnum", '',$select_len,$_arr["minute"]);
        $_arr["hour"]   = $this->gencheckboxInput(24,$per_line,"hour",  0,"formatnum" ,'',$select_len,$_arr["hour"]);
        $_arr["day"]   = $this->gencheckboxInput(31,$per_line,"day",   1,"formatnum" ,'',0,$_arr["day"]);
        $_arr["month"]   = $this->gencheckboxInput(12,$per_line,"month", 1,"formatnum" ,'',0,$_arr["month"]);
        $_arr["week"]   = $this->gencheckboxInput(7, $per_line,"week",  1,"formatweek",'',0,$_arr["week"]);

        $_arr["forbidden_timezone_options"] = buildOptions(getTimezone(),$_arr["forbidden_timezone"]);  // 默认东八区
      }else {
        // 生成静态html页面
        $_arr["minute"] = $this->gencheckboxInput(60,$per_line,"minute",0,"formatnum","",$select_len);
        $_arr["hour"]   = $this->gencheckboxInput(24,$per_line,"hour",  0,"formatnum","",$select_len);
        $_arr["day"]   = $this->gencheckboxInput(31,$per_line,"day",   1,"formatnum" ,'checked="checked"');
        $_arr["month"]   = $this->gencheckboxInput(12,$per_line,"month", 1,"formatnum" ,'checked="checked"');
        $_arr["week"]   = $this->gencheckboxInput(7, $per_line,"week",  1,"formatweek",'checked="checked"');
        $_arr["forbidden_date"] = "";  // 默认值为空，即没有默认值，但也需要写，不然替换不了
        $_arr["forbidden_timezone"] = 8;  // 默认东八区
        $_arr["forbidden_timezone_options"] = buildOptions(getTimezone(),$_arr["forbidden_timezone"]);  // 默认东八区
      }
    }

    // 先获取模板
    $content = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/".$actionMap->getProp("path").".html");
    // 加入头尾
    $header = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."header.html");  // 标准头
    $footer = file_get_contents($GLOBALS['cfg']['PATH_ROOT']."/".$GLOBALS['cfg']['Template_Path']."/"."footer.html");  // 标准尾
    $data_arr = array(
      "id"=>$id,
      "_action"=>$request["_action"],
      "name"=>$request["name"],
      "host"=>$request["host"],
      "server_timezone"=>$request["server_timezone"],
      "belong_user"=>$request["belong_user"],

      "RES_WEBPATH_PREF"=>$GLOBALS['cfg']['RES_WEBPATH_PREF'],
      "header"=>$header,
      "footer"=>$footer
    );
    $content = replace_template_para($data_arr,$content);

    $content = replace_template_para($_arr,$content);

    // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
    $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);
    // 将外链的js替换为其相应js内容
    //$content = jssrc2content($content);
    // 替换其中的css地址和js地址 以后采用缓存文件，而不用每次都实时取
    $content = replace_cssAndjsAndimg($content,$GLOBALS['cfg']['SOURCE_CSS_PATH'],$GLOBALS['cfg']['SOURCE_JS_PATH'],$GLOBALS['cfg']['SOURCE_IMG_PATH']);// js中还有图片


    $response['html_content'] = replace_template_para($data_arr,$content);
    return null;  // 总是返回此结果
  }

  function gencheckboxInput($total,$perline,$a_name,$begin=1,$callfunc="formatnum",$checked="",$a_per=0,$a_val=""){
    $name = $a_name."[]";

    $str = "";
    // fill checkbox with given value
    $fill_arr = array();
    $a_val = trim($a_val);  //
    if (""!=$a_val) {
      if (false!==strpos($a_val,"*")) {
        $l_tmp_arr = explode('/',$a_val);
        // first checked decide if has *;
        $str  = $this->getFirstPart($total,$a_name,$name,$a_per,'checked="checked"',intval($l_tmp_arr[1]));  //first
        $str .= $this->getSecendPart($total,$perline,$name,$begin,$callfunc);
      }else {
        $fill_arr = explode(",",$a_val);
        // 第一部分的check没有，而per也一样没有，因为没有*
        $str = $this->getFirstPart($total,$a_name,$name,$a_per,"",0);
        // 主要集中在第二部分，哪些是checked的
        $str .= $this->getSecendPart($total,$perline,$name,$begin,$callfunc,$fill_arr);
      }
    }else {
      $str  = $this->getFirstPart($total,$a_name,$name,$a_per,$checked,0);  // first
      $str .= $this->getSecendPart($total,$perline,$name,$begin,$callfunc);
    }

    return $str;
  }

  function getFirstPart($total,$a_name,$name,$a_per,$checked,$selected_id=0){
    // first
    $str  = '<input type="checkbox" name="'.$name.'" value="*" onClick="return selChange(\''.$name.'\', 0, '.$total.');" '.$checked.'>所有 ';
    if($a_per>1){
      $str .= '/ <select name="per_'.$a_name.'">';

      for($i=1;$i<=$a_per;$i++){
        $t_arr[$i] = $i;
      }
      $str .= buildOptions($t_arr,(int)$selected_id,false);

      $str .='</select> ( 后面的下拉框中的整数表示每几分钟或每几小时执行一次 )';
    }
    $str .= NEW_LINE_CHAR."<br />".NEW_LINE_CHAR;

    return $str;
  }

  function getSecendPart($total,$perline,$name,$begin=1,$callfunc="formatnum",$fill_arr=array()){
    $str = "";

    if (!empty($fill_arr)) {
      for($i=0;$i<$total;$i++){
        if ($i>0 && $i%$perline==0) {
          $str .= "<br />".NEW_LINE_CHAR;
        }
        $value = $i+$begin;
        if ("formatweek"==$callfunc && 6==$i) {
          $value = 0;// 周日是第0天
        }
        // 逐个进行判断
        if (in_array($value,$fill_arr)) {
          $check_ed = 'checked="checked"';
        }else {
          $check_ed = "";
        }
        $str .= '<input type="checkbox" name="'.$name.'" value="'.$value.'" onClick="return selChange(\''.$name.'\', '.($i+1).', '.$total.');" '.$check_ed.'>'.$this->$callfunc($i+$begin).NEW_LINE_CHAR;
      }
    }else {
      for($i=0;$i<$total;$i++){
        if ($i>0 && $i%$perline==0) {
          $str .= "<br />".NEW_LINE_CHAR;
        }
        $value = $i+$begin;
        if ("formatweek"==$callfunc && 6==$i) {
          $value = 0;// 周日是第0天
        }
        $str .= '<input type="checkbox" name="'.$name.'" value="'.$value.'" onClick="return selChange(\''.$name.'\', '.($i+1).', '.$total.');" >'.$this->$callfunc($i+$begin).NEW_LINE_CHAR;
      }
    }

    $str .= "<br />".NEW_LINE_CHAR;

    return $str;
  }

  function formatnum($num){
    if ($num<10) {
      $num = "0".$num;
    }
    return $num;
  }

  function formatweek($num){
    $week_arr = array(
    "",
    "星期一","星期二","星期三","星期四","星期五","星期六","星期日"
    );
    return $week_arr[$num];
  }
}
