<?php
/**
 * 兼容PHP4的代码
 *
 */
class cConstans
{
  /**
   * 添加或修改字段的时候，不允许使用的字段名，只允许在字段定义表和表定义表中使用
   *
   * @return array
   */
  function getBaoLiuZiDuan(){
    return array("p_id","t_id");
  }
}