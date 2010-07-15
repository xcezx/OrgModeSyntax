<?php
include_once(dirname(__FILE__) . '/../../t.php');

$t = new lime_test;

$p = new OrgModeSyntax_DefinitionList(PEG::anything());
$c = PEG::context(array(
    '- foo : bar',
    '- baz : qux',
  ));

$t->is(
  $p->parse($c),
  array(
    array(str_split(' foo '), str_split(' bar')),
    array(str_split(' baz '), str_split(' qux')),
  ));