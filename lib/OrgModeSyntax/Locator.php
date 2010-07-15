<?php
class OrgModeSyntax_Locator
{
  protected $blockRef = null;
  protected $lineElementRef = null;
  protected $shared = array();
  protected $facad;

  private function __construct()
  {
    $this->setUp();
  }

  protected function setUp()
  {
    $this->lineElement;
    $this->lineElementRef = new OrgModeSyntax_LineElement($this->bracket, $this->inlineTag);
    $this->block;
    $this->blockRef = PEG::memo(new OrgModeSyntax_Block($this));
  }

  public static function it()
  {
    static $obj = null;
    return $obj ? $obj : $obj = new self;
  }

  public function __get($name)
  {
    return isset($this->shared[$name])
      ? $this->shared[$name]
      : $this->shared[$name] = $this->{'create' . $name}();
  }

  /**
   * @author	xcezx
   * @param	string
   * @param	PEG_IParser
   * @param	Array
   * @return	OrgModeSyntax_NodeCreater
   */
  protected function nodeCreater($type, PEG_IParser $parser, Array $keys = array())
  {
    return new OrgModeSyntax_NodeCreater($type, $parser, $keys);
  }

  protected function createLineChar()
  {
    return PEG::anything();
  }

  protected function createInlineTag()
  {
    $parser = new OrgModeSyntax_InlineTag($this->lineElement);
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_INLINE_TAG, $parser, array('name', 'body'));
  }

  protected function createLineElement()
  {
    return PEG::ref($this->lineElementRef);
  }

  protected function createLineSegment()
  {
    return PEG::many($this->lineElement);
  }

  protected function createHttpLink()
  {
    $url_char = PEG::pack(
      '[',
      PEG::seq(
        PEG::choice('http://', 'https://'),
        PEG::many1(PEG::subtract($this->lineChar, ']'))),
      ']');
    $url = PEG::join($url_char);

    $title_char = PEG::pack(
      '[',
      PEG::many1(PEG::subtract($this->lineChar, ']')),
      ']');
    $title = PEG::join($title_char);

    $parser = PEG::seq($url, PEG::optional($title));
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_HTTPLINK, $parser, array('href', 'title'));
  }

  protected function createImageLink()
  {
    $ext = PEG::choice('.jpg', '.png', '.gif');
    $src_char = PEG::pack(
      '[',
      PEG::seq(
        PEG::many1(PEG::subtract($this->lineChar, ']', $ext)),
        $ext),
      ']');
    $src = PEG::join($src_char);

    $alt_char = PEG::pack(
      '[',
      PEG::many1(PEG::subtract($this->lineChar, ']')),
      ']');
    $alt = PEG::join($alt_char);

    $parser = PEG::seq($src, PEG::optional($alt));
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_IMAGELINK, $parser, array('src', 'alt'));
  }

  protected function createBracket()
  {
    return PEG::pack('[', PEG::choice(
        $this->imageLink,
        $this->httpLink
      ), ']');
  }

  protected function createHeader()
  {
    $parser = new OrgModeSyntax_Header($this->lineElement);

    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_HEADER, $parser, array('level', 'body'));
  }

  protected function createNoParagraph()
  {
  }

  protected function createList()
  {
    $parser = new OrgModeSyntax_List($this->lineElement);
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_LIST, $parser);
  }

  protected function createDefinitionList()
  {
    $parser = new OrgModeSyntax_DefinitionList($this->lineElement);
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_DEFINITION_LIST, $parser);
  }

  protected function createTable()
  {
  }

  protected function createParagraph()
  {
    $parser = new OrgModeSyntax_Paragraph($this->lineElement);

    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_PARAGRAPH, $parser);
  }

  protected function createEmptyParagraph()
  {
    $parser = PEG::count(PEG::many1(PEG::token('')));

    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_EMPTY_PARAGRAPH, $parser);
  }

  protected function createBlock()
  {
    $parser = PEG::ref($this->blockRef);

    return $parser;
  }

  protected function createParser()
  {
    return $this->nodeCreater(OrgModeSyntax_Node::TYPE_ROOT, PEG::many($this->block));
  }
}