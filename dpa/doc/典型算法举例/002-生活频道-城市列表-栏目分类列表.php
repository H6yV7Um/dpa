[sql]
select {保存路径},{所属栏目},{栏目名称},{级别} from {栏目配置} limit 1

[code]<?php
$dbR = $a_arr['dbR'];

// 用于生成分类信息的城市首页的各个栏目区块
$l_options = '';  // 结果

// 先从栏目配置中获取到所有的一级栏目，除去新闻栏目
$dbR->table_name = "{栏目配置}";
$l_lanmu_all = $dbR->getAlls("where `{级别}`=1 and status_='use' and {栏目名称} not in ('新闻') ", "id,{保存路径},{所属栏目},{栏目名称},{级别}");

// 循环一下，重新处理
$l_length2 = 6;

if (!empty($l_lanmu_all)) {
  foreach ($l_lanmu_all as $vals){
    $l_options .= '
    <div class="left">
        <h3><a href="/${英文名称}/'.trim($vals['{保存路径}']," /").'/" title="'.$vals['{栏目名称}'].' - ${中文名称} " target="_blank">'.$vals['{栏目名称}'].'</a> </h3>
        <ul>';

    // 找出其下二级栏目, 然后进行逐条列出
    $l_lanmu_2 = $dbR->getAlls("where {所属栏目}='".$vals['{栏目名称}']."' and `{级别}`=2 and status_='use'  ", "id,{保存路径},{所属栏目},{栏目名称},{级别}");
    $l_num = count($l_lanmu_2);
    if (!empty($l_lanmu_2)) {
      foreach ($l_lanmu_2 as $l_v2){
        // 如果二级栏目中包含了一级栏目的路径，则无需组装
        if (false!==strpos($l_v2['{保存路径}'], ltrim($vals['{保存路径}']) )) {
          $l_lujin = trim($l_v2['{保存路径}']," /");
        }else {
          $l_lujin = trim($vals['{保存路径}']," /")."/".trim($l_v2['{保存路径}']," /");
        }
        $l_options .= '
            <li><a href="/${英文名称}/'.$l_lujin.'" target="_blank">'.$l_v2['{栏目名称}'].'</a></li>';
      }
    }
    if ($l_num<$l_length2) {
      for ($i=1; $i<=$l_length2-$l_num; $i++){
        $l_options .= '
            <li>&nbsp;</li>';
      }
    }

    $l_options .= '
        <br />
        </ul>
      </div>
  ';

  }
}

return $l_options;
