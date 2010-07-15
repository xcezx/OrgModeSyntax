<?php
class OrgModeSyntax_Node
{
  const TYPE_ROOT            = 'root';
  const TYPE_HEADER          = 'header';
  const TYPE_PARAGRAPH       = 'paragraph';
  const TYPE_EMPTY_PARAGRAPH = 'emptyparagraph';
  const TYPE_LIST            = 'list';
  const TYPE_DEFINITION_LIST = 'definitionlist';
  const TYPE_INLINE_TAG      = 'inlinetag';
  const TYPE_HTTPLINK        = 'httplink';
  const TYPE_IMAGELINK       = 'imagelink';

  protected $type, $offset, $data, $contextHash;

  function __construct($type, $data = array(), $offset = null, $contextHash = null)
  {
    $this->type        = $type;
    $this->data        = $data;
    $this->offset      = $offset;
    $this->contextHash = $contextHash;
  }

  public function getContextHash()
  {
    return $this->contextHash;
  }

  public function getOffset()
  {
    return $this->offset;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getData()
  {
    return $this->data;
  }

  public function at($name, $defaultVal = null)
  {
    return array_key_exists($name, $this->data)
      ? $this->data[$name]
      : $defaultVal;
  }

  public function isTopHeader()
  {
    return $this->type === self::TYPE_HEADER && $this->at('level') === 0;
  }
}