<?php
class OrgModeSyntax_DefinitionList implements PEG_IParser
{
  protected $parser, $definitionList;

  public function __construct(PEG_IParser $elt)
  {
    $dt = PEG::many(PEG::subtract($elt, ':'));
    $dd = PEG::many($elt);

    $this->parser = PEG::many1(PEG::callbackAction(
        array($this, 'map'),
        PEG::anything()
      ));

    $this->definitionList = PEG::seq(
      PEG::drop('-'),
      $dt,
      PEG::drop(':'),
      $dd
    );
  }

  public function parse(PEG_IContext $context)
  {
    return $this->parser->parse($context);
  }

  public function map($line)
  {
    return $this->definitionList->parse(PEG::context($line));
  }
}