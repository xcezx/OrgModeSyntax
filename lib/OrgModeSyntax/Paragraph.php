<?php
class OrgModeSyntax_Paragraph implements PEG_IParser
{
  protected $parser, $line;

  public function __construct(PEG_IParser $lineelt)
  {
    $this->parser = PEG::callbackAction(
      array($this, 'map'),
      PEG::anything()
    );
    $this->line = PEG::many($lineelt);
  }

  public function parse(PEG_IContext $context)
  {
    return $this->parser->parse($context);
  }

  public function map($line)
  {
    return $this->line->parse(PEG::context($line));
  }
}