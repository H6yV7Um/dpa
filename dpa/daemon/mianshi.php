<?php
// D:/php5320/php D:/www/dpa/daemon/mianshi.php

define("NEW_LINE_CHAR","\r\n");

/**
 * 写一个程序求两个数的最大公约数
 */
function max_divisor($a, $b) {
  // 两个数的公约数不会大于其中较小的数，所以用$n来控制公约数的取值范围
  $n = min($a, $b);
  //$i 表示公约数的值，其取值范围在$n至2之间，之所以从$n开始循环，是因为要求的是最大公约数
  for($i=$n; $i>1; $i--) {
    //当两个数除以$i 都为整数值时，$i 即为两数的公约数，因为$i是由大到小取值的，所以得出的是最大公约数
    if (is_int($a/$i) && is_int($b/$i))
      return $i; //返回该值
  }

  return 1; //如果不满足整除条件，则两数的最大公约数为1
}
echo max_divisor(12, 8) . NEW_LINE_CHAR;


/**
 * 写一个程序求两个数的最小公倍数
 *
 *  ????
 * TODO 此方法有问题 -- 以后修改
 *
 */
function min_multiple($a, $b) {
  if($b==0)     //一定要考虑除数不能为零
  {
    return $b;
  } else {
    $m = max($a, $b);
    $n = min($a, $b);
    for($i=2; ; $i++)
    {
      if (is_int($m*$i/$n))
      {
        return $i;
      }
    }
  }
  return $a*$b;
}
// echo min_multiple(12, 8) . NEW_LINE_CHAR;


// 1 1 2 3 5 8 13 是否需要考虑到PHP整型数字的范围
function digui($n){
  $sn = 0;
  if ($n<=0) {

  }else if ($n==1) {
    $sn = 1;
  }else if ($n==2) {
    $sn = 1;
  }else {
    $sn = digui($n-1)+digui($n-2);
  }
  return $sn;
}
echo digui(7) . NEW_LINE_CHAR;


/**
 * 1元钱一瓶汽水，喝完后两个空瓶换一瓶汽水，问：你有20元钱，最多可以喝到几瓶汽水。
 *
 * @param float $money_total
 * @param float $price_qi_ping  // 汽水单价
 * @param int $num_duihua_kong  // 兑换汽水的空瓶数量
 * @param int $num_duihuan_qi  // 能兑换出来的汽水数
 * @return array
 */
function bbbb($money_total=20,$price_qi_ping=1,$num_duihua_kong=2,$num_duihuan_qi=1){
  // 兑换的空瓶数必须大于兑换得来的汽水
  if ($num_duihua_kong<=$num_duihuan_qi) {
    return "";
  }

  // 初始可获得的汽水数以及空瓶数
  $num_total_he_qi = $num_yu = floor($money_total/$price_qi_ping);

  cccc($num_total_he_qi,$num_yu,$num_duihua_kong,$num_duihuan_qi);
  echo $num_total_he_qi ." : ".$num_yu;
}

function cccc(&$num_total_he_qi,&$num_yu,$num_duihua_kong=2,$num_duihuan_qi=1){
  echo "num_total_he_qi:".$num_total_he_qi." "."num_yu:".$num_yu.NEW_LINE_CHAR;
  // 只要空瓶总数大于0，就可以兑换所需空瓶数的时候
  //if ($num_yu>0) {
    $l_num_yu = $num_yu-$num_duihua_kong+$num_duihuan_qi;  // 逐一兑换剩余的空瓶
    if($l_num_yu>=0) {
      $num_yu = $l_num_yu;
      $num_total_he_qi += $num_duihuan_qi;  // 可以喝的汽水就加上能兑换的汽水数
      cccc($num_total_he_qi,$num_yu,$num_duihua_kong,$num_duihuan_qi);//继续循环
    }
  //}
}

// 既然是最多，则可以借空瓶还空瓶
//bbbb(20,1,7,1);



/**
 * 写一个程序打印1到100这些数字。
 * 但是遇到数字为3的倍数的时候，打印“A” 替代数字，5的倍数用“B”代替，
 * 既是3的倍数又是5的倍数打印“AB”
 *
 */
function aaa($num){
  for ($i=1;$i<=$num;$i++){
    $str = "";
    if (0==$i%3) {
      $str .= "A";
      if (0==$i%5) {
        $str .= "B";
      }
    }else if (0==$i%5) {
      $str .= "B";
    }else {
      $str .= $i;
    }

    echo $str.NEW_LINE_CHAR;
  }
}
// aaa(200);
