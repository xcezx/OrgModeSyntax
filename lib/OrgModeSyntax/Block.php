<?php
class OrgModeSyntax_Block implements PEG_IParser
{
  protected
    $lineTable,
    $paragraphCheckTable,
    $firstCharTable,
    $paragraph;

  /**
   * @param	OrgModeSyntax_Locator
   */
  public function __construct(OrgModeSyntax_Locator $locator)
  {
    $this->lineTable = array(
      '' => $locator->emptyParagraph,
    );

    $this->paragraphCheckTable = array(
      '+' => true,
      '-' => true,
      '*' => true,
    );

    $this->firstCharTable = array(
      '*' => $locator->header,
      '+' => $locator->list,
      '-' => $locator->definitionList,
    );

    $this->paragraph = $locator->paragraph;
  }

  /**
   * @param	PEG_IContext
   */
  public function parse(PEG_IContext $context)
  {
    if ($context->eos())
    {
      return PEG::failure();
    }

    $line = $context->readElement();
    $context->seek($context->tell() - 1);

    // 行ディスパッチ
    if (isset($this->lineTable[$line]))
    {
      return $this->lineTable[$line]->parse($context);
    }

    $char = substr($line, 0, 1);

    if (!isset($this->paragraphCheckTable[$char]))
    {
      return $this->paragraph->parse($context);
    }

    if (isset($this->firstCharTable[$char]))
    {
      return $this->firstCharTable[$char]->parse($context);
    }

    return $this->paragraph->parse($context);
  }
}