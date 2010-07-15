<?php
$cwd = dirname(__FILE__);
include_once $cwd . '/../../t.php';

$t = new lime_test;

$p = new OrgModeSyntax_InlineTag(PEG::anything());

$c = PEG::context('<strong>a</strong>');

$t->is($p->parse($c), array('strong', array('a')));