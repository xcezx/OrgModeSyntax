<?php
class OrgModeSyntax_InlineTag implements PEG_IParser
{
  protected $parser;

  public function __construct(PEG_IParser $element)
  {
    $open = PEG::second('<', PEG::choice('del', 'strong', 'ins', 'em'), '>');
    $close = PEG::second('</', PEG::choice('del', 'strong', 'ins', 'em'), '>');
    $this->parser = PEG::seq($open, PEG::many(PEG::subtract($element, $close)), $close);
  }

  public function parse(PEG_IContext $context)
  {
    $result = $this->parser->parse($context);

    if ($result instanceof PEG_Failure)
    {
      return $result;
    }

    list($open, $body, $close) = $result;

    return $open !== $close ? PEG::failure() : array($open, $body);
  }
}







