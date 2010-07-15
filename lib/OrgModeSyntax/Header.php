<?php
class OrgModeSyntax_Header implements PEG_IParser
{
  protected $child, $paraser;

  public function __construct(PEG_IParser $elt)
  {
    $this->child  = PEG::many($elt);
    $this->parser = PEG::callbackAction(array($this, 'map'), PEG::anything());
  }

  public function parse(PEG_IContext $context)
  {
    return $this->parser->parse($context);
  }

  public function map($line)
  {
    if (strpos($line, '*') === 0)
    {
      list($level, $rest) = $this->toLevelAndRest((string)substr($line, 1));

      $body = $this->child->parse(PEG::context($rest));

      return array($level, $body);
    }

    return PEG::failure();
  }

  private function toLevelAndRest($line)
  {
    $level = 0;

    for ($i = 0, $len = strlen($line); $i < $len; $i++)
    {
      if ($line[$i] === '*')
      {
        $level++;
      }
      else
      {
        break;
      }
    }

    return array($level, (string)substr($line, $level));
  }
}
