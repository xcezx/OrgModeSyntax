<?php
class OrgModeSyntax_LineElement implements PEG_IParser
{
  protected $table;

  public function __construct(PEG_IParser $bracket, PEG_IParser $inlineTag)
  {
    $this->table = array(
      '[' => PEG::choice($bracket   , PEG::anything()),
      '<' => PEG::choice($inlineTag , PEG::anything()),
    );
  }

  public function parse(PEG_IContext $context)
  {
    if ($context->eos())
    {
      return PEG::failure();
    }

    $char = $context->readElement();

    if (isset($this->table[$char]))
    {
      $offset = $context->tell() - 1;
      $context->seek($offset);

      return $this->table[$char]->parse($context);
    }

    return $char;
  }
}