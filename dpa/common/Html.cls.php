<?php
/**
* Html.cls.php
*/
class Html {
  /**
   * 来自外部修改默认值的变量名称|数组键名称
   *
   * @var string
   */
    //var $default_waibu = "default_w";

    /**
    * 页面跳转
    * @static
    * @access public
    * @param string $url
    */
    function jump($url){
        header("Location: $url");
    }

    // 外部用于覆盖的数据索引名称。
    function getDefault_waibu_str(){
      // 因为在没有new一个对象的时候，不能使用对象中的全局属性，所以采用此方法
      // echo Html::default_waibu;此行将会报错????
      return "default_w";
    }
    /**
     * 依据字段数据生成input表单项
     *
     * @param array $f_arr 字段详细说明数组，来自字段定义表
     * @param array $a_init 外部数据，主要是修改默认值,也可能为空，修改数据的时候需要用到外部数据
     * @return string
     */
    function FormInputByField($f_arr, $a_init=array(), $a_no_need_field=array()){
      $l_str = "";  // 结果字符串
      $a_init = is_array($a_init) ? $a_init : array();  // 强制成数组

      if (empty($f_arr)) {
        return $l_str;
      }

      // 逐一字段解析并联成表单
      foreach ($f_arr as $l_info){
        $field = trim($l_info["name_eng"]);  // 字段都是英文的
        if (in_array($field,$a_no_need_field)) {
          continue;
        }
        if (array_key_exists($field, $a_init)) {
          $l_init[Html::getDefault_waibu_str()] = $a_init[$field];
        }else {
          $l_init = array();
        }
        if ("auto_increment"!=strtolower(trim($l_info["extra"])) && "timestamp" != strtolower(trim($l_info["type"]))) {
          $l_str .= '
          <tr>
            <td nowrap="nowrap" title="'.$l_info['description'].'">'.$l_info["name_cn"].'</td>
            <td>'.Html::getOneFieldInput($l_info, $l_init).
                ( ("NO"==strtoupper($l_info["is_null"])) ? '<span style="color:#FF0000">*</span>' : "" ).'</td>
          </tr>';
        }
        // 上传文件需要填充一下
      }

      return $l_str;
    }

    function getOneFieldInput($a_arr, $l_init=array()){
      $l_str = "";
      // 对于不同类型使用不同表单框
      $field = trim($a_arr["name_eng"]);  // 字段都是英文的
      $numeric = "";
      $type  = strtolower( trim($a_arr["type"]) );

      // 默认值以及其他属性，例如隐藏
      $l_hidden = "";
      if (array_key_exists(Html::getDefault_waibu_str(), $l_init)) {
        $l_default = $l_init[Html::getDefault_waibu_str()]["value"];
        if(isset($l_init[Html::getDefault_waibu_str()]["hidden"]) && $l_init[Html::getDefault_waibu_str()]["hidden"]) {
            $l_hidden = 'readonly="readonly" ';
        }
      }else {
        $l_default = ( ("NULL"==$a_arr["default"])?"":$a_arr["default"] );
      }
      $l_default = convCharacter(htmlspecialchars($l_default, ENT_QUOTES));

      if ("enum"==$type || false!==strpos($a_arr["f_type"],"Select")) {
        // 枚举型的采用select框
        $l_str .= '<select id="'.$field.'" name="'.$field.'">';
        $l_str .= Html::buildoptionsOrCheckBoxRadio($a_arr, $l_default, $l_init, "option");
        $l_str .= "</select>";
        // 增加select项的查找功能, 在select后面跟一个按钮，采用js实现
        /*$l_str .= '
        <input type="text" id="_QFT_'.$field.'" name="_QFT_'.$field.'" size="6" style="&#039;border:1px inset&#039;" onKeyPress="On_QuickFindPress(this, myform.elements(\''.$field.'\'))" />
             <input type="button" value="Find" onClick="On_QuickFindClick(myform.elements(\'_QFT_'.$field.'\'), myform.elements(\''.$field.'\'))" />
      ';*/

      }else {
        // 依据字段算法类型就能确定如何呈现表单。如果没有字段算法类型，则基于数据表结构的字段类型
        if (array_key_exists("f_type", $a_arr)) {
          $l_str .= Html::getFormHtmlByFieldType($a_arr,$l_default,$l_init);
        }else {
          if (false!==strpos($type,"text")) {
            // text框
            $l_str .= '<textarea cols="60" id="'.$field.'" name="'.$field.'" rows="8" wrap="physical">'.$l_default.'</textarea>';
          }else {
            $size = isset($a_arr["size"])?$a_arr["size"]:60;
            $l_str .= '<input id="'.$field.'" name="'.$field.'" size="' . $size .'" value="'.$l_default.'" '.$l_hidden.'/>';
          }
        }
      }

      /*$l_str .= '
        <input type="hidden" name="_VF__NOTNULL_'.$field.'" value="'.$a_arr["is_null"].'" />
            <input type="hidden" name="_VF__NUMERIC_'.$field.'" value="'.$numeric.'" />
            <input type="hidden" name="_VF__LENGTH_'.$field.'" value="'.$a_arr["length"].'" />
            <input type="hidden" name="_FCR_'.$field.'" value="'.$a_arr["name_cn"].'" />
            <input type="hidden" name="_FTR_'.$field.'" value="'.$a_arr["type"].'" />
        ';*/

      return $l_str;
    }

    //
    function buildoptionsOrCheckBoxRadio($a_arr, $a_default, $l_init=array(), $a_tag="option"){
      $l_str = "";
      if ( "" !== $a_arr["length"] ) {
        $l_field = trim($a_arr["name_eng"]);

        // 当有外部算法的时候，length的值将被修改为array
        $l_array = false;
        if (is_array($a_arr["length"])) {
          $l_opts = $a_arr["length"];  // 用直接的数组
          $l_array = true;
        }else {
          $l_opts = explode(",", $a_arr["length"]);
          $l_tmps = array();
          if (false !== strpos( $a_arr["length"], "'" )) {
            foreach ($l_opts as $l_val){
              $l_tmps[] = trim($l_val, " '\"");
            }
            $l_opts = $l_tmps;
          }
        }

        if (in_array($a_default, $l_opts)) {
          // 可以直接修改默认值，在后续循环中，才能命中想要的数据项
          $a_arr["default"] = $a_default;
        }

        // 如果该字段可以为空的话, 需要强制增加空值。
        //if ( "NO"!=strtoupper($a_arr["is_null"]) ) $l_opts = array_merge(array(""=>""),$l_opts);

        foreach ($l_opts as $l_key => $l_opt){
          if ("option"==$a_tag) {
            // 每项需要去掉首尾的引号
            $l_opt = trim($l_opt, " '\"");
            $select_con = ($l_opt==$a_arr["default"])?' selected="selected"':"";
            $l_str .= '<option value="'.$l_opt.'"'.$select_con.'>'.($l_array?$l_key:$l_opt).'</option>';
          }else {
            $select_con = ($l_opt==$a_arr["default"])?' checked="checked"':"";
            $l_str .= '<input type="'.$a_tag.'" id="'.$l_field.'" name="'.$l_field.'" value="'.$l_opt.'"'.$select_con.' />'.($l_array?$l_key:$l_opt).'<br />';
          }
        }
      }

      return $l_str;
    }

    // 将数组的键值对填充到模板中去
  function AccordingTpl2Str($data_arr, $a_field=array("p_id"), $a_tpl='<input type="hidden" id="<!--{$ziduan}-->" name="<!--{$ziduan}-->" value="<!--{$value}-->" />'){
    $l_str = '';
    if (is_array($a_field) && is_array($data_arr)) {
      foreach ($a_field as $l_f){
        if (key_exists($l_f,$data_arr)) {
          $rep_arr = array("ziduan"=>$l_f,"value"=>$data_arr[$l_f]);
          $l_str .= replace_template_para($rep_arr,$a_tpl);
        }
      }
    }

    return $l_str;
  }

  // 转为多级相关链接而用
  function AtagTpl2Str($data_arr){
    $l_str = '';

    // 拼装链接地址
    if (!empty($data_arr)) {
      foreach ($data_arr as $l_f){
        $script_name = array_key_exists("script_name",$l_f) ? $l_f["script_name"] : "main.php";
        $do = $l_f["do"];
        $target = array_key_exists("target",$l_f) ? " target='".$l_f["target"]."'" : "";
        $l_str .= '/<a href="'.$script_name.'?do='.$do.$l_f["href"].'"'.$target.'>'.$l_f["name_cn"].'</a>';
      }
    }

    return $l_str;
  }

  //
  /**
   * 依据字段算法类型，返回相应的html表单代码
   *
   * @param  array  $a_vals  字段定义表中的某字段信息数组
   * @param  array  $a_default 外部提供的数据,可能来自编辑的时候原来数据库中的数据
   * @param  array  $a_content_arr 外部提供的数据, 在修改数据的时候用于填充额外的属性,例如隐藏属性等。暂时不做实现，用不到。
   * @return string 相应的html代码
   */
  function getFormHtmlByFieldType($a_vals,$a_default,$a_content_arr=array()){
    $l_html = "";  // 存放结果

    $l_field = trim($a_vals["name_eng"]);
    $l_f_type= trim($a_vals["f_type"]);

    $l_content = $a_default;  // 以及进行过转码和特殊字符转换

    // 其他属性
    $l_hidden = "";
      if (array_key_exists(Html::getDefault_waibu_str(), $a_content_arr)) {
        if(isset($a_content_arr[Html::getDefault_waibu_str()]["hidden"]) && $a_content_arr[Html::getDefault_waibu_str()]["hidden"]) {
            $l_hidden = 'readonly="readonly" ';
        }
      }

    switch ($l_f_type){
      case "Form::CodeResult":
        $l_html = '<input type="text" id="'.$l_field.'" name="'.$l_field.'" size="80" value="'.$l_content.'" '.$l_hidden.'/>';
        break;
      case "Form::TextField":
        $l_ziduan = convCharacter($a_vals["name_cn"]);
        $style = "";
        if ("文档标题"==$l_ziduan||"副标题"==$l_ziduan) {
          $style = 'style="background-image:url('.$GLOBALS['cfg']['RES_WEBPATH_PREF'].'dpa_html/images/ruler.gif)"'; // 文档标题有样式
        }
        $l_html = '<input type="text" '.$style.' id="'.$l_field.'" name="'.$l_field.'" size="80" value="'.$l_content.'" '.$l_hidden.'/>';
        break;
      case "Form::Password":
        $l_html = '<input type="password" id="'.$l_field.'" name="'.$l_field.'" value="'.$l_content.'" size="20" />';
        break;
      case "Form::Date":
        $l_html = '<input class="Wdate" type="text" onClick="WdatePicker()" id="'.$l_field.'" name="'.$l_field.'" value="'.$l_content.'" />';
        break;
      case "Form::DateTime":
        $l_html = '<input class="sang_Calender" type="text" id="'.$l_field.'" name="'.$l_field.'" value="'.$l_content.'" />';
        break;
      case "Form::TextArea":
        $l_html = '<textarea id="'.$l_field.'" name="'.$l_field.'" cols="80" rows="16">'.$l_content.'</textarea>';
        break;
      case "Form::HTMLEditor":
        $l_html = Html::getHTMLEditor($l_field,$l_content);
        break;
      //   下拉列表框
      case "Form::Select":
      case "Form::DB_Select":
      case "Form::URL_Select":
      case "Form::DB_DSN_Select":
        // 前面以及已有处理，此处不做处理，包括上面的各种select都一样
        break;
      // 检查框
      case "Form::CheckBoxGroup":
      case "Form::DB_CheckBoxGroup":
      case "Form::URL_CheckBoxGroup":
      case "Form::DB_DSN_CheckBoxGroup":
        $l_html = Html::buildoptionsOrCheckBoxRadio($a_vals,$l_content,$a_content_arr,"checkbox");
        break;
      // 选项框
      case "Form::RadioGroup":
      case "Form::DB_RadioGroup":
      case "Form::URL_RadioGroup":
      case "Form::DB_DSN_RadioGroup":
        $l_html = Html::buildoptionsOrCheckBoxRadio($a_vals,$l_content,$a_content_arr,"radio");
        break;
      // 文件
      case "Form::ImageFile":
        $l_html = Html::getImageFileHtml($l_field, $l_content, $a_content_arr);
        break;
      case "Form::AudioFile":
      case "Form::VedioFile":
      case "Form::File":
        //$l_html = '<input type="file" id="'.$l_field.'" name="'.$l_field.'" size="40" onChange="return CheckImageSize(document.myform, this);" />';
        $l_html = Html::getFileHtml($l_field, $l_content, $a_content_arr);
        break;
      //
      case "Form::CGICall":
        break;
      case "Form::CrossPublish":
        break;
      default:
        break;
    }

    return $l_html;
  }

  function getImageFileHtml($f_name, $a_content, $a_content_arr=array()){
    // 如果有内容则显示
    $a_content = trim($a_content);

    // 默认上传的选中，当有url的时候，默认后面选中
    $checked1 = 'checked';
    $checked2 = '';
    $img_html = '';
    if ($a_content) {
      $checked1 = '';
      $checked2 = 'checked';
      $img_html = '<a href="'.$a_content.'" target="_blank" ><img src="'.$a_content.'" width="20" height="20" /></a>';
    }

    $str = '
      <input type="radio" id="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" name="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" '.$checked1.' value="1" />
          上传本地图片
          <input type="file" id="'.$GLOBALS['cfg']['UPLOADIMG_PRE'].$f_name.'" name="'.$GLOBALS['cfg']['UPLOADIMG_PRE'].$f_name.'" size="40" onChange="return CheckImageSize(document.myform, this);" />
          <br />
      <input type="radio" id="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" name="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" '.$checked2.' value="2" />
          使用绝对链接
          <input type="text" id="'.$f_name.'" name="'.$f_name.'" size="60" value="'.$a_content.'" />
    '.$img_html;
    return $str;
  }

  // 除图片外文件
  function getFileHtml($f_name, $a_content, $a_content_arr=array()){
      // 如果有内容则显示
      $a_content = trim($a_content);

      // 默认上传的选中，当有url的时候，默认后面选中
      $checked1 = 'checked';
      $checked2 = '';
      if ($a_content) {
          $checked1 = '';
          $checked2 = 'checked';
      }

      $str = '
      <input type="radio" id="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" name="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" '.$checked1.' value="1" />
          上传本地文件
          <input type="file" id="'.$GLOBALS['cfg']['UPLOADIMG_PRE'].$f_name.'" name="'.$GLOBALS['cfg']['UPLOADIMG_PRE'].$f_name.'" size="40" />
          <br />
      <input type="radio" id="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" name="'.$GLOBALS['cfg']['RADIO_UPLOADIMG_CHANGE'].$f_name.'" '.$checked2.' value="2" />
          使用绝对链接
          <input type="text" id="'.$f_name.'" name="'.$f_name.'" size="60" value="'.$a_content.'" />
    ';
      return $str;
  }

  // 正文的区域不一样!
  function getHTMLEditor($f_name,$content){
    $host_pre = GetCurrentUrlPre();
    $str = '
    <TEXTAREA NAME="'.$f_name.'" COLS="80" ROWS="16">'.$content.'</TEXTAREA>
    <script type="text/javascript" language="javascript"> CKEDITOR.replace("'.$f_name.'",{
    filebrowserBrowseUrl : "' . $host_pre . '/dpa_html/ckeditor/ckfinder/ckfinder.html",
    filebrowserImageBrowseUrl : "' . $host_pre . '/dpa_html/ckeditor/ckfinder/ckfinder.html?Type=Images",
    filebrowserFlashBrowseUrl : "' . $host_pre . '/dpa_html/ckeditor/ckfinder/ckfinder.html?Type=Flash",
    filebrowserUploadUrl:"' . $host_pre . '/dpa_html/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files",
    filebrowserImageUploadUrl:"' . $host_pre . '/dpa_html/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images",
    filebrowserFlashUploadUrl :"' . $host_pre . '/dpa_html/ckeditor/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash"
    });</script>
                <INPUT TYPE="HIDDEN" NAME="_VF__NOTNULL_'.$f_name.'" VALUE="FALSE">
                <INPUT TYPE="HIDDEN" NAME="_VF__MIN_LENGTH_'.$f_name.'" VALUE="0">
                <INPUT TYPE="HIDDEN" NAME="_VF__MAX_LENGTH_'.$f_name.'" VALUE="0">
                <INPUT TYPE="HIDDEN" NAME="_VF__FTR_'.$f_name.'" VALUE="Text">
                <INPUT TYPE="HIDDEN" NAME="_FCR_'.$f_name.'" VALUE="正文">
                <INPUT TYPE="HIDDEN" NAME="_AP_'.$f_name.'" VALUE="Article.Content">
    ';
    return $str;
  }

}