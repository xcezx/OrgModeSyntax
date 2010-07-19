<?php
class OrgModeSyntax_Renderer
{
  protected
    $config,
    $fncount,
    $root,
    $treeRenderer,
    $headerCount;

  public function __construct(Array $config = array())
  {
    $this->config = $config + array(
      'headerlevel' => 1,
      'htmlescape' => true,
      'id' => uniqid('sec'),
      'sectionclass' => 'section',
      'linktitlehandler' => array($this, 'linkTitleHandler'),
      'trim' => true,
    );

    $this->treeRenderer = new OrgModeSyntax_TreeRenderer(
      array($this, 'listItemCallback'),
      array($this, 'isOrderedCallback'));
  }

  public static function linkTitleHandler($url)
  {
    return $url;
  }

  public function listItemCallback(Array $data)
  {
    list(, $lineSegment) = $data;
    return $this->renderLineSegment($lineSegment);
  }

  public function isOrderedCallback(OrgModeSyntax_Tree_INode $node)
  {
    $children = $node->getChildren();
    foreach ($children as $child)
    {
      if ($child->hasValue())
      {
        $buf = $child->getValue();
        return $buf[0] === '+';
      }
    }
    return false;
  }

  public function render(OrgModeSyntax_Node $rootNode)
  {
    if ($rootNode->getType() !== OrgModeSyntax_Node::TYPE_ROOT)
    {
      throw new InvalidArgumentException();
    }

    $this->root = $rootNode;
    $this->headerCount = 0;

    $ret = $this->renderNode($rootNode);
    $ret = '<div class="' . $this->config['sectionclass'] . '">'
      . PHP_EOL
      . $ret
      . PHP_EOL . '</div>' . PHP_EOL;

    return $ret;
  }

  public function renderTitle(OrgModeSyntax_Node $root)
  {
    if ($root->getType() !== OrgModeSyntax_Node::TYPE_ROOT)
    {
      throw new InvalidArgumentException();
    }

    $this->root = $root;
    $this->headerCount = 0;

    $nodes = $root->getData();

    if (isset($nodes[0]) && $nodes[0]->isTopHeader())
    {
      return strip_tags($this->renderLineSegment($nodes[0]->at('body')));
    }
    return '';
  }

  protected function renderNode(OrgModeSyntax_Node $node)
  {
    $ret = $this->{'render' . $node->getType()}($node->getData());
    return $ret;
  }

  protected function renderRoot(Array $arr)
  {
    foreach ($arr as &$elt)
    {
      $elt = $this->renderNode($elt);
    }
    return join(PHP_EOL, $arr);
  }

  protected function renderHeader(Array $data)
  {
    $level = $data['level'] + $this->config['headerlevel'];
    $name = 'hs_' . md5($this->config['id']) . '_header_' . $this->headerCount++;

    return "<h{$level} id=\"{$name}\">" . $this->renderLineSegment($data['body']) . "</h{$level}>";
  }

  protected function renderNoParagraph(Array $data)
  {
  }

  protected function renderParagraph(Array $data)
  {
    return '<p>' . $this->renderLineSegment($data) . '</p>';
  }

  protected function renderEmptyParagraph($data)
  {
    return str_repeat('<br />' . PHP_EOL, max($data - 1, 0));
  }

  protected function renderList(OrgModeSyntax_Tree_Root $root)
  {
    return $this->treeRenderer->render($root);
  }

  protected function renderDefinitionList(Array $data)
  {
    foreach ($data as &$elt)
    {
      $elt = $this->renderDefinition($elt);
    }
    return join(PHP_EOL, array('<dl>', join(PHP_EOL, $data), '</dl>'));
  }

  protected function renderDefinition(Array $data)
  {
    list($dt, $dd) = $data;
    $ret = array();
    if ($dt)
    {
      $ret[] = '<dt>' . $this->renderLineSegment($dt) . '</dt>';
    }
    $ret[] = '<dd>' . $this->renderLineSegment($dd) . '</dd>';
    return join(PHP_EOL, $ret);
  }

  protected function renderLineSegment(Array $data)
  {
    $data = self::normalize($data);
    foreach ($data as &$elt)
    {
      $elt = !$elt instanceof OrgModeSyntax_Node
        ? ($this->config['htmlescape'] ? $this->escape($elt) : $elt)
        : $this->renderNode($elt);
    }
    return $this->config['trim'] ? trim(join('', $data)) : join('', $data);
  }

  protected function renderHttpLink(Array $data)
  {
    list($href, $title) = array($data['href'], $data['title']);

    if ($title === '')
    {
      $title = call_user_func($this->config['linktitlehandler'], $href);
    }
    elseif ($title === false)
    {
      $title = $href;
    }

    return sprintf('<a href="%s">%s</a>', self::escape($href), self::escape($title));
  }

  protected function renderImageLink(Array $data)
  {
    list($src, $alt) = array($data['src'], $data['alt']);

    if ($alt === '')
    {
      $alt = call_user_func($this->config['imagealthandler'], $src);
    }
    elseif ($alt === false)
    {
      $alt = $src;
    }

    return sprintf('<img src="%s" alt="%s" />', $src, $alt);
  }

  protected static function escape($str)
  {
    if (!is_string($str))
    {
      debug_pring_backtrace();
      return;
    }
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }

  protected static function normalize(Array $arr)
  {
    $ret = array();

    while ($arr)
    {
      list($elt, $arr) = self::segment($arr);
      $ret[] = $elt;
    }

    return $ret;
  }

  protected static function segment(Array $arr)
  {
    $first = array_shift($arr);

    if (!is_string($first))
    {
      return array($first, $arr);
    }

    $str = $first;
    while ($arr)
    {
      if (is_string($arr[0]))
      {
        $str .= array_shift($arr);
      }
      else
      {
        return array($str, $arr);
      }
    }
    return array($str, array());
  }
}