<?php
include_once(dirname(__FILE__) . '/INode.php');

class OrgModeSyntax_Tree_Root implements OrgModeSyntax_Tree_INode
{
  protected $children;

  public function __construct(Array $children)
  {
    $this->children = $children;
  }

  public function hasValue()
  {
    return false;
  }

  public function getValue()
  {
    return null;
  }

  public function hasChildren()
  {
    return true;
  }

  public function getChildren()
  {
    return $this->children;
  }

  public function getType()
  {
    return 'root';
  }
}