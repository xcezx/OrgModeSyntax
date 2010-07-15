<?php
$cwd = dirname(__FILE__);
include_once $cwd . '/../../t.php';

$t = new lime_test;

$p = new OrgModeSyntax_Header(PEG::anything());

$c = PEG::context(array('* Top level headline'));
$t->is($p->parse($c), array(0, str_split(' Top level headline')));

$c = PEG::context(array('** Second level'));
$t->is($p->parse($c), array(1, str_split(' Second level')));

$c = PEG::context(array('*** 3rd level', 'some text'));
$t->is($p->parse($c), array(2, str_split(' 3rd level')));
