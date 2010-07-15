<?php
include_once(dirname(__FILE__) . '/../../t.php');

$t = new lime_test;

$p = new OrgModeSyntax_List(PEG::anything());
$c = PEG::context(array(
    '- List item 1',
    '-- List item 1-1',
  ));

$root = $p->parse($c);
$t->is($root->getType(), 'root');

$nodes = $root->getChildren();
$t->is($nodes[0]->getType(), 'node');
$t->is($nodes[0]->getValue(), array('-', str_split(' List item 1')));

$leaf = $nodes[0]->getChildren();
$t->is($leaf[0]->getType(), 'leaf');
$t->is($leaf[0]->getValue(), array('-', str_split(' List item 1-1')));
