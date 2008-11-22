<?php

include dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(2, new lime_output_color());

$version = sfEzcWorkflowPeer::getCurrentVersionNumber('Test');
$t->cmp_ok($version, '>' ,0, 'Workflow current version fetched from database');

$version = sfEzcWorkflowPeer::getCurrentVersionNumber('test_doesnt_exist_yet');
$t->is($version,0,'getting the version number of a workflow which doesn\'t exist must return 0');
