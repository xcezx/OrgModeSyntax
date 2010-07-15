<?php
interface OrgModeSyntax_Tree_INode
{
  public function hasValue();
  public function getValue();
  public function hasChildren();

  public function getChildren();

  public function getType();
}