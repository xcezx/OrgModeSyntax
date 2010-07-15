<?php
$cwd = dirname(__FILE__);
include_once $cwd . '/../../t.php';

$t = new lime_test;

$p = new OrgModeSyntax_Paragraph(PEG::anything());
$c = PEG::context(array(
    'abc',
    'def'
  ));

$t->is(
  $p->parse($c),
  str_split('abc')
);

$t->is(
  $p->parse($c),
  str_split('def')
);