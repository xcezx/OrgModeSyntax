<?php
$cwd = dirname(__FILE__);
include_once $cwd . '/../../t.php';

$t = new lime_test;

$p = OrgModeSyntax_Locator::it()->lineElement;

$c = PEG::context('a');

$t->is($p->parse($c), 'a');