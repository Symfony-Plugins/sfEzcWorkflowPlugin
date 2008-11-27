<?php

include dirname(__FILE__).'/../bootstrap/unit.php';
include_once dirname(__FILE__).'/test_stub.php';
$t = new lime_test(9, new lime_output_color());
// GET A TEST WORKFLOW FROM A XML FILE
$definition = new ezcWorkflowDefinitionStorageXml( dirname(__FILE__).'/../fixtures/'  );
try{
  $workflow = $definition->loadByName( 'Test_sf' );  //definition contain an ezcWorkflowNodeInputFromSfTest
  $t->isa_ok($workflow,'ezcWorkflow','ezcWorkflow loaded from xml');
  $nodes_count = sizeof( $workflow->nodes );
  $variables_count = sizeof( $workflow->getVariableHandlers() );
  
  // Generate GraphViz/dot markup for workflow "Test".
  $visitor = new ezcWorkflowVisitorVisualization;
  $workflow->accept( $visitor );
  $str_workflow_orig = $visitor->__toString();
}catch (Exception $e){
  $t->fail('Workflow was not loaded from XML. Exception raised: '.get_class($e).'. Message: '.$e->getMessage() );
}

$storage = new sfPropelEzcWorkflowDefinitionStorage();

//TEST SAVING ON A DATABASE
try {
  $storage->save( $workflow );

  $c = new Criteria();
  $c->add( sfEzcWorkflowPeer::ID, $workflow->id );
  $wf_count = sfEzcWorkflowPeer::doCount($c);

  $t->is($wf_count, 1, 'Workflow stored on database');

  $c = new Criteria();
  $c->add( sfEzcWorkflowNodePeer::WORKFLOW_ID, $workflow->id );
  $saved_nodes_count = sfEzcWorkflowNodePeer::doCount($c);

  $t->is( $nodes_count, $saved_nodes_count, 'Right number of nodes saved' );

  $c = new Criteria();
  $c->add( sfEzcWorkflowVariableHandlerPeer::WORKFLOW_ID, $workflow->id );
  $saved_variables_count = sfEzcWorkflowVariableHandlerPeer::doCount($c);

  $t->is( $variables_count , $saved_variables_count, 'Right number of variable handlers saved' );

}catch (Exception $e){
  $t->fail('Workflow was not stored. Exception raised: '.get_class($e).'. Message: '.$e->getMessage() );
  $e->getTrace();
}

//TEST LOADING FROM A DATABASE
try {
  $workflow = $storage->loadByName('Test_sf');
  $t->pass('Wokflow loaded from database');
  $loaded_nodes_count = sizeof( $workflow->nodes );
  $t->is( $loaded_nodes_count, $nodes_count, 'Right number of nodes loaded');
  $loaded_variable_count = sizeof( $workflow->getVariableHandlers() );
  $t->is( $loaded_variable_count, $variables_count, 'Right number of variable handlers saved' );
  foreach ($workflow->nodes as $node )
  {
    if ($node instanceof ezcWorkflowNodeInputFromSf)
    {
      if (($node->getActionUri() === 'sfEzcWorkflowDemo/choice') || ($node->getActionUri() === 'sfEzcWorkflowDemo/message'))
      {
          $t->pass('action uri loaded correctly' );
      }
      else
      {
          $t->fail('incorrect action uri');
      }
    }
  }
  
  // Generate GraphViz/dot markup for workflow "Test".
  $visitor = new ezcWorkflowVisitorVisualization;
  $workflow->accept( $visitor );
  $str_workflow_loaded = $visitor->__toString();
}catch (Exception $e){
  $t->fail('Workflow could be loaded from database. Exception raised: '.get_class($e).'. Message: '.$e->getMessage() );
  $e->getTrace();
}

/*
In case you want to get a diagram to check visually whether both xml and db workflows are the same or not
echo $str_workflow_orig;
echo $str_workflow_loaded;
*/
