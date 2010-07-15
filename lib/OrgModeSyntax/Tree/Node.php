<?php
include_once(dirname(__FILE__) . '/INode.php');

class OrgModeSyntax_Tree_Node implements OrgModeSyntax_Tree_INode
{
  protected $value, $children;

  public function __construct(Array $children, $value = null)
  {
    list($this->children, $this->value) = array($children, $value);
  }

  public function hasValue()
  {
    return isset($this->value);
  }

  public function getValue()
  {
    return $this->value;
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
    return 'node';
  }
}