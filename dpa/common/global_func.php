<?php
function getServerIp($url = 'http://hq.ni9ni.com/getip.php'){
  require_once "HTTP/Request.php";
  //$url = "http://202.108.37.119/company/distrsync_request.php";
  //$url = "http://hq.ni9ni.com/getip.php";
  if (!$url) return '';

  $req = new HTTP_Request($url);
  // 设置代理
  if (isset($GLOBALS['cfg']['WEB_PROXY_FILE']) && file_exists($GLOBALS['cfg']['WEB_PROXY_FILE'])) {
    $proxy_dsn = file_get_contents($GLOBALS['cfg']['WEB_PROXY_FILE']);
    $proxy_arr = parse_url($proxy_dsn);
    if (isset($proxy_arr['host'])) {
      if (!isset($proxy_arr['port']))
        $proxy_arr['port'] = 80;
      if (isset($proxy_arr['user']))
        $req->setProxy($proxy_arr['host'], $proxy_arr['port']);
      else
        $req->setProxy($proxy_arr['host'], $proxy_arr['port'], $proxy_arr['user'], $proxy_arr['pass']);
    }
  }
  $req->_timeout = 20;
  $req->setMethod("GET");
  $req->addHeader("User-Agent","Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1 (.NET CLR 3.5.30729)");
  $rc = $req->sendRequest();
  //if(PEAR::isError($rc)) echo "HTTP_Request Error : ".$rc->getMessage(); // 通常有网络不通的情况

  $ip = $req->getResponseBody();

  if (strlen($ip)>15) {  // 出错
    $ip = "";
  }
  return $ip;
}
function STDERR($str){
  echo $str;
}
function getOSArr(){
  return array("SunOS"=>"SunOS","FreeBSD"=>"FreeBSD","Linux"=>"Linux","WinXp"=>"WinXp");
}

function option_method_arr(){
    return array(
        '='   => '等于',
        '!='   => '不等于',
        'like'   => '匹配',
        '<'     => '小于',
        '<='  => '小于或等于',
        '>'     => '大于',
        '>='  => '大于或等于',
    );
}

function get_method_option($a_default='='){
    $l_arr = option_method_arr();
    $method_option = '';
    foreach ($l_arr as $l_k=>$l_v) {
        $l_select = '';
        if ($l_k == $a_default) $l_select = ' selected';
        $method_option .= '<option value="'.$l_k.'"'.$l_select.'>'.$l_v.'</option>';
    }
    return $method_option;
}

//
function getZiduan($a_str){
  $rlt_arr = array();
  $rlt2 = array();
  $rlt3 = array();
  // 分解 $a_str url:文档URL;sp_f33:文档标题;creator:创建者;__create_datetime__:创建日期和时间;mender:修改者;mendtime:修改时间;sp_f42:所属栏目;sp_f43:所属子栏目;sp_f35:来源
  $l_arr = explode(";",$a_str);

  foreach ($l_arr as $l_v){
    $l_tm = explode(":",$l_v);
    $l_k = trim($l_tm[0]);
    if (""!=trim($l_k)) {
      if ("url"==strtolower(trim($l_k))) {
        $rlt_arr[] = "url_1";
      }else if ("__create_datetime__"==strtolower(trim($l_k))){
        // 两个字段
        $rlt_arr[] = "createdate";
        $rlt_arr[] = "createtime";
      }else if ("__mend_datetime__"==strtolower(trim($l_k))){
        // 两个字段
        $rlt_arr[] = "menddate";
        $rlt_arr[] = "mendtime";
      }else if ("__minute_hour_day_month_week__"==strtolower(trim($l_k))){
        // 多个字段
        $tmp = explode("_",strtolower(trim($l_k)));
        foreach ($tmp as $_vl){
          if (!empty($_vl)) {
            $rlt_arr[] = $_vl;
          }
        }
      }else {
        $rlt_arr[] = $l_k;
      }
      $rlt2[trim($l_k)] = trim($l_tm[1]);

      // 用于设置enum相应值的某些自定义属性值以 | 分隔enum值
      if (isset($l_tm[2])) {
        $l_enum = explode("|",$l_tm[2]);
        foreach ($l_enum as $l_enum_v_a){
          $l_enum_v = explode(",",$l_enum_v_a);
          $rlt3[trim($l_k)][trim($l_enum_v[0])] = trim($l_enum_v[1]);
        }
      }
    }
  }

  return array($rlt_arr,$rlt2,$rlt3);
}

// list
function buildH($arr,$ziduan,$val_replace=array(),$a_no_need_field=array("id","last_modify"), $domain=''){
  $str = "";
  $str_title = "";
  if (is_array($arr) && count($arr)>0) {
    $key = 0;
    foreach ($arr as $val){
      // 不必显示的行
      if (isset($val["name_eng"]) && in_array($val["name_eng"], $a_no_need_field)) {
        continue;
      }
      // 每行采用不同颜色
      if (0==$key%2) {
        $str .= "<tr height='30' bgcolor='#F6FBE9' align='center'>";
      }else {
        $str .= "<tr height='30' bgcolor='#D9E2D0' align='center'>";
      }
      // 对应的标题
      if (0==$key) $str_title .= "";

      foreach ($ziduan[1] as $filed=>$l_f_name_cn){
          if (array_key_exists($filed, $val)) {
              $l_val = $val[$filed];
          } else {
              $l_val = '';
          }

        if (array_key_exists($filed,$ziduan[2])) {
          $__atri = " ".getAttri($ziduan[2],$filed);
        }else {
          $__atri = "";
        }

        if ("id"==$filed) {
          if ('del'==$val['status_']) $display_v = '<strike>'.$l_val.'</strike>';
          else $display_v = $l_val;
          $str .= "<td nowrap><input type='radio' name='id' value='".$l_val."'>".$display_v."</td>";
        }else if ("status_"==$filed) {
          $display_v = htmlspecialchars($l_val, ENT_NOQUOTES);
          if (!empty($val_replace)) {
            if(key_exists($filed,$val_replace)) $display_v = getFieldDesc($l_val,$val_replace[$filed]);
          }

          if ('del'==$val['status_']) $display_v = '<strike>'.$display_v.'</strike>';

          if (key_exists($filed,$ziduan[2])) {
            $str .= "<td nowrap style='color:".$ziduan[2][$filed][$l_val]."'>".$display_v."</td>";
          } else $str .= "<td nowrap>".$display_v."</td>";
        }else if ("__create_datetime__"==$filed){
          // 两个字段
          $str .= "<td valign=middle align=left>".$val["createdate"]." ".$val["createtime"]."</td>";
        }else if ("host_entry"==$filed){
          // 执行回调函数
          $callback = key($ziduan[2][$filed]);
          $str .= "<td align='left'>".$callback($val)."</td>";
        }else if ("__minute_hour_day_month_week__"==$filed){
          // 多个字段
          $_title = "";
          $tmp = explode("_",$filed);
          foreach ($tmp as $_vl){
            if (!empty($_vl)) {
              $_title .= $val[$_vl]." ";
            }
          }
          $str .= "<td valign='middle' align='left'><div title='$_title'". $__atri. ">".$_title."</div></td>";
        } else if ($filed == "url_1") {
          $display_v = $l_val;
          // URL需要加链接
          if (isset($val["url_1"]) && $val["url_1"] && 'del'!=$val['status_'] && $val["url_1"] != $domain) {
            $l_url = (false === strpos($val["url_1"], '://')) ? rtrim($domain, '/') . "/" . ltrim($val["url_1"], '/') : $val["url_1"];
            $display_v = "<a href='".$l_url."' target='_blank'>".$l_val."</a>";
          }
          if ('del'==$val['status_']) $display_v = '<strike>'.$l_val.'</strike>';

          $str .= "<td nowrap>".$display_v."</td>";
        }else {
          $display_v = htmlspecialchars($l_val, ENT_NOQUOTES);
          if (!empty($val_replace)) {
            if(key_exists($filed,$val_replace)) $display_v = getFieldDesc($l_val,$val_replace[$filed]);
          }
          if ('del'==$val['status_']) $display_v = '<strike>'.$display_v.'</strike>';

          $str .= "<td nowrap". $__atri .">".convCharacter($display_v)."</td>";
        }
        if (0==$key){
          $str_title .= "<td nowrap align='center'". $__atri .">".$l_f_name_cn."</td>";
        }
      }

      $str .= "</tr>";

      $key++;
    }
  }

  return array($str,$str_title);
}

function getAttri($a_atr_arr,$a_field){
  $__atri = "";
  if (key_exists($a_field,$a_atr_arr)) {
    foreach($a_atr_arr[$a_field] as $__atri_key=>$__atri_val){
      if (""==$__atri) {
        $__atri = 'style="';
      }
      $__atri .= $__atri_key.":$__atri_val;";
    }

    if (""!=$__atri) {
      $__atri .= '"';
    }
  }

  return $__atri;
}

function call_back_host_entry($vals){
  return "http://".$vals["host_domain"].":".$vals["host_port"]."/".convCharacter($vals["host_root"])."";
}