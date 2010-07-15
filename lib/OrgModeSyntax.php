<?php
require_once 'PEG.php';
include_once(dirname(__FILE__) . '/OrgModeSyntax/Block.php');
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/CommentRemover.php'); */
include_once(dirname(__FILE__) . '/OrgModeSyntax/DefinitionList.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Header.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/InlineTag.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/LineElement.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/List.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Locator.php');
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/NoParagraph.php'); */
include_once(dirname(__FILE__) . '/OrgModeSyntax/Node.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/NodeCreater.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Paragraph.php');
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/Pre.php'); */
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/Quote.php'); */
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/Regex.php'); */
include_once(dirname(__FILE__) . '/OrgModeSyntax/Renderer.php');
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/SuperPre.php'); */
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/TOCRenderer.php'); */
/* include_once(dirname(__FILE__) . '/OrgModeSyntax/Table.php'); */
include_once(dirname(__FILE__) . '/OrgModeSyntax/Tree.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Tree/INode.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Tree/Leaf.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Tree/Node.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Tree/Root.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/TreeRenderer.php');
include_once(dirname(__FILE__) . '/OrgModeSyntax/Util.php');

class OrgModeSyntax
{
  /**
   * @param	string
   * @return	OrgModeSyntax_Node
   */
  public static function parse($str)
  {
    return OrgModeSyntax_Locator::it()->parser->parse(self::context($str));
  }

  /**
   * @param	string
   * @return	Array
   */
  public static function parseAsSections($str)
  {
    $blocks = self::parse($str)->getData();

    // 先頭にある空のパラグラフを削る
    foreach ($blocks as $i => $block)
    {
      if ($block->getType() === OrgModeSyntax_Node::TYPE_EMPTY_PARAGRAPH)
      {
        unset($blocks[$i]);
      }
      else
      {
        break;
      }
    }
    $blocks = array_values($blocks);

    // セクションごとにブロック要素をまとめる
    $sections = array();
    $len = count($blocks);
    $blocks = array_reverse($blocks);
    for ($i = 0; $i < $len; $i++)
    {
      $section = array();
      for (; $i < $len; $i++)
      {
        $section[] = $blocks[$i];
        if ($blocks[$i]->isTopHeader())
        {
          break;
        }
      }
      $sections[] = array_reverse($section);
    }
    $sections = array_reverse($sections);

    // ブロック要素の配列をノードにする
    foreach ($sections as $i => $section)
    {
      $sections[$i] = new OrgModeSyntax_Node(OrgModeSyntax_Node::TYPE_ROOT, $section);
    }

    return $sections;
  }

  /**
   * @param	string
   * @param	Array
   * @return	string
   */
  public static function render($str, Array $config = array())
  {
    $node = self::parse($str);
    $renderer = new OrgModeSyntax_Renderer($config);
    return $renderer->render($node);
  }

  /**
   * @param	OrgModeSyntax_Node
   * @param	Array
   * @return	string
   */
  public static function renderNode(OrgModeSyntax_Node $node, Array $config = array())
  {
    $renderer = new OrgModeSyntax_Renderer($config);
    return $renderer->render($node);
  }

  /**
   * @param	string
   * @return	PEG_IContext
   */
  protected static function context($str)
  {
    $str = str_replace(array("\r\n", "\r"), "\n", $str);

    return PEG::context(preg_split("{\n}", $str));
  }

  /**
   * @param	OrgModeSyntax_Node
   * @param	Array
   * @return	string
   */
  public static function getSectionTitle(OrgModeSyntax_Node $node, Array $config = array())
  {
    self::assertRootNode($node);

    $renderer = new OrgModeSyntax_Renderer($config);
    return $renderer->renderTitle($node);
  }

  /**
   * @param	OrgModeSyntax_Node
   * @return	bool
   */
  public static function hasTopHeader(OrgModeSyntax_Node $node)
  {
    self::assertRootNode($node);

    list($block) = $node->getData() + array(false);

    return $block && $block->isTopHeader();
  }

  /**
   * @param	OrgModeSyntax_Node
   */
  protected static function assertRootNode(OrgModeSyntax_Node $node)
  {
    if ($node->getType() !== OrgModeSyntax_Node::TYPE_ROOT)
    {
      throw new InvalidArgumentException('this node must be root node');
    }
  }
}