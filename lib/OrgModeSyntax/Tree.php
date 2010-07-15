<?php
class OrgModeSyntax_Tree
{
  public static function make(Array $arr)
  {
    return new OrgModeSyntax_Tree_Root(self::makeNodeArray($arr));
  }

  protected static function makeNodeArray(Array $arr)
  {
    $i = 0;
    $len = count($arr);
    $tree_arr = array();
    $min_level = self::fetchMinLevel($arr);
    while ($i < $len)
    {
      list($tree_arr[], $i) = self::makeNode($arr, $i, $min_level);
    }

    return $tree_arr;
  }

  protected static function makeNode(Array $arr, $i, $min_level)
  {
    $children = array();
    $len = count($arr);
    if ($min_level < $arr[$i]['level'])
    {
      for (; $i < $len && $min_level < $arr[$i]['level']; $i++)
      {
        $children[] = $arr[$i];
      }
      return array(new OrgModeSyntax_Tree_Node(self::makeNodeArray($children)), $i);
    }
    else
    {
      $value = $arr[$i]['value'];
      $i++;
      for (; $i < $len && $min_level < $arr[$i]['level']; $i++)
      {
        $children[] = $arr[$i];
      }
      $node = $children
        ? new OrgModeSyntax_Tree_Node(self::makeNodeArray($children), $value)
        : new OrgModeSyntax_Tree_Leaf($value);

      return array($node, $i);
    }
  }

  protected static function fetchMinLevel(Array $arr)
  {
    foreach ($arr as $elt)
    {
      if (!isset($level) || $level > $elt['level'])
      {
        $level = $elt['level'];
      }
    }
    return $level;
  }
}