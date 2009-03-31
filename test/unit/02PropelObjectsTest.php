<?php

include dirname(__FILE__).'/../bootstrap/unit.php';
include_once dirname(__FILE__).'/test_stub.php';
$t = new lime_test(2, new lime_output_color());

$storage = new sfPropelEzcWorkflowDefinitionStorage();
$workflow = $storage->loadByName('Test');
// Set up database-based workflow executer.
$execution = new sfPropelEzcWorkflowExecution();
// Pass workflow object to workflow executer.
$execution->workflow = $workflow;
$sf_ezc_testing_message = null;
// Start workflow execution.
$id = $execution->start();
$t->is($execution->isSuspended(),true,'Execution is suspended');

$propel_obj = sfEzcWorkflowExecutionPeer::retrieveByPK($id);
$variables = array('test' => 'value', 'test_2' => 'value 2');
$propel_obj->setVariables($variables);
$propel_obj->save();

$propel_obj = sfEzcWorkflowExecutionPeer::retrieveByPK($id);
$variables_retrieved = $propel_obj->getVariables();

$t->is($variables_retrieved, $variables);