<?php
$cwd = dirname(__FILE__);
include_once $cwd . '/../../t.php';

$t = new lime_test;

$p = OrgModeSyntax_Locator::it()->bracket;

$c = PEG::context('[[http://google.com/][Google]]');
$result = $p->parse($c);
$t->is($result->at('href'), 'http://google.com/');
$t->is($result->at('title'), 'Google');

$c = PEG::context('[[http://example.com/]]');
$result = $p->parse($c);
$t->is($result->at('href'), 'http://example.com/');
$t->is($result->at('title'), false);

$c = PEG::context('[[foo.png]]');
$result = $p->parse($c);
$t->is($result->at('src'), 'foo.png');
$t->is($result->at('alt'), false);

$c = PEG::context('[[http://example.com/sample.jpg][sample image]]');
$result = $p->parse($c);
$t->is($result->at('src'), 'http://example.com/sample.jpg');
$t->is($result->at('alt'), 'sample image');