<?php
class OrgModeSyntax_List implements PEG_IParser
{
  protected $parser, $li;

  public function __construct(PEG_IParser $lineelt)
  {
    $item = PEG::callbackAction(
      array($this, 'mapLine'),
      PEG::anything());

    $this->parser = PEG::callbackAction(
      array('OrgModeSyntax_Tree', 'make'),
      PEG::many1($item)
    );

    $this->li = PEG::callbackAction(
      array('OrgModeSyntax_Util', 'processListItem'),
      PEG::seq(
        PEG::many(PEG::char('+')),
        PEG::many($lineelt)
      )
    );
  }

  public function parse(PEG_IContext $context)
  {
    return $this->parser->parse($context);
  }

  public function mapLine($line)
  {
    if (in_array(substr($line, 0, 1), array('+'), true))
    {
      return $this->li->parse(PEG::context($line));
    }

    return PEG::failure();
  }
}