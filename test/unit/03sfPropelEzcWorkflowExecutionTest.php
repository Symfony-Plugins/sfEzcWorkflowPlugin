<?php

include dirname(__FILE__).'/../bootstrap/unit.php';
include_once dirname(__FILE__).'/test_stub.php';
$t = new lime_test(14, new lime_output_color());

//WORKFLOW EXECUTION
include_once dirname(__FILE__).'/../../lib/sfPropelEzcWorkflowExecution.class.php';
try {
  $branches = array( 'message: TRUE' => true, 'message: FALSE' => false );
  foreach( $branches as  $message => $choice ){

    //Load a workflow from database
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
    
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionPeer::WORKFLOW_ID, $workflow->id );
    $c->add( sfEzcWorkflowExecutionPeer::ID, $id );
    $wf_execution_count = sfEzcWorkflowExecutionPeer::doCount($c);
    $t->is($wf_execution_count, 1, 'Workflow execution stored on database');
    
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $id );
    $wf_execution_state_count = sfEzcWorkflowExecutionPeer::doCount($c);
    $t->cmp_ok( $wf_execution_state_count,'>',0, 'Workflow execution state stored on database' );
    
    // Load workflow execution from db.
    $execution = new sfPropelEzcWorkflowExecution($id);
    $t->cmp_ok(sizeof($execution->getActivatedNodes()),'>',0,'Activated nodes loaded from database');
    //$t->is($execution->isSuspended(),true,'Execution loaded is suspended');
    //Resume execution
    $execution->resume( array( 'choice' => $choice ) );
    $t->is($sf_ezc_testing_message,$message,'Correct branch executed');
  
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionPeer::WORKFLOW_ID, $workflow->id );
    $c->add( sfEzcWorkflowExecutionPeer::ID, $id );
    $wf_execution_count = sfEzcWorkflowExecutionPeer::doCount($c);
    $t->is($wf_execution_count, 0, 'Workflow execution cleaned on database');
  
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $id );
    $wf_execution_state_count = sfEzcWorkflowExecutionPeer::doCount($c);
    $t->is( $wf_execution_state_count, 0, 'Workflow execution state cleaned on database' );
  }

}catch( Exception $e ){
  $t->fail('Workflow wasn\'t executed. Exception raised: '.get_class($e).'. Message: '.$e->getMessage() );
}