<?php
class OrgModeSyntax_Util
{
  public static function normalizeList(Array $data)
  {
    return OrgModeSyntax_Tree::make($data);
  }

  public static function processListItem(Array $li)
  {
    $ret = array();
    $ret['level'] = count($li[0]) - 1;
    $ret['value'] = array(end($li[0]), $li[1]);

    return $ret;
  }
}