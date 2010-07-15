<?php
include_once(dirname(__FILE__) . '/../lib/OrgModeSyntax.php');

$str = '* Header 1
foo [[http://example.com][bar]] baz

** Header 2

[[http://google.com/]]

[[/sample.png][サンプル画像]]

+ foo
+ bar
++ baz

- aaa : bbb

';

echo OrgModeSyntax::render($str);