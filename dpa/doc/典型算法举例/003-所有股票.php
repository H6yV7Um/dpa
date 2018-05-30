[sql]
select {股票代码},{股票代码全称},{股票代码中文名} from {股票表}

[code]<?php
$dbR = $a_arr['dbR'];

$l_code = "{股票代码}";
$dbR->table_name = "{股票表}";
$l_rlt = $dbR->getAlls('where status_="use" order by '.$l_code.' asc ', '{股票代码},{股票代码全称},{股票代码中文名}');

$l_total = count($l_rlt);
$html = "所有股票: (".$l_total.")";
// 逐项进行处理, 特别是要进行截字
if (!empty($l_rlt)) {
  $html .= '<br />';

  $l_tmp = array_chunk($l_rlt, 8);
  foreach ($l_tmp as $l_kuai){
    foreach ($l_kuai as $l_v){
      $l_code   = $l_v['{股票代码}'];
      //$l_symbol   = $l_v['{股票代码全称}'];
      $l_name_cn  = $l_v['{股票代码中文名}'];

      $html .= '<span><a href="/cn/stock/'.$l_code.'/" target="_blank">'.$l_code.'</a> '.$l_name_cn.'</span>
          ';
    }
    $html .= '<br />';
  }
}

return $html;