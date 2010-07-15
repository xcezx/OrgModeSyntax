<?php
$cwd = dirname(__FILE__);
require_once $cwd . '/t.php';

$lime = new lime_harness(null);
$lime->register_glob(dirname(__FILE__) . '/lib/*.php');
$lime->register_glob(dirname(__FILE__) . '/lib/OrgModeSyntax/*.php');
$lime->run();
