<?php
include_once(dirname(__FILE__) . '/INode.php');

class OrgModeSyntax_Tree_Leaf implements OrgModeSyntax_Tree_INode
{
  protected $value;

  public function __construct($value)
  {
    $this->value = $value;
  }

  public function hasValue()
  {
    return true;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function hasChildren()
  {
    return false;
  }

  public function getChildren()
  {
    return array();
  }

  public function getType()
  {
    return 'leaf';
  }
}