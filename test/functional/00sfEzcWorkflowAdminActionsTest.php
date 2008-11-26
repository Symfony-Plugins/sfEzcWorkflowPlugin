<?php

include(dirname(__FILE__).'/../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$definition = new ezcWorkflowDefinitionStorageXml( dirname(__FILE__).'/../fixtures/'  );
$workflow = $definition->loadByName( 'Test_sf' );  //definition contain an ezcWorkflowNodeInputFromSfTest
$storage = new sfPropelEzcWorkflowDefinitionStorage();
$storage->save( $workflow );


$c = new Criteria();
$wf_count = sfEzcWorkflowPeer::doCount($c);
echo $wf_count;

$browser->
  get('/sfEzcWorkflowAdmin/index')->

  with('request')->begin()->
    isParameter('module', 'sfEzcWorkflowAdmin')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body h1', '/sfEzcWorkflow Admin/')->
    checkElement('tr[class~="sf_admin_row"]',true, array('count'=>$wf_count))->
  end()->
  
/*  setField('sfEzcWorkflow[name]','Test_sf')->
  setField('sfEzcWorkflow[xml_file]',dirname(__FILE__).'/../fixtures/Test_sf_1.xml')->
  click('Create workflow from xml definition')->
  with('response')->begin()->
    checkElement('body h1', '/Workflow saved on database successfully/')->
    checkElement('tr[class~="sf_admin_row"]',true, array('count'=>$wf_count+1))->
  end()->
*/  

  
  get('/sfEzcWorkflowAdmin/1/downloadXml')->
    isResponseHeader('content-type', 'text/xml; charset=utf-8')->
    isStatusCode(200)->

  get('/sfEzcWorkflowAdmin/100000000/downloadXml')->
    isStatusCode(302)->
    followRedirect()->
    
  with('response')->
    checkElement('body','/Invalid request. sfEzcWorkflowStorageException:/')->

  get('/sfEzcWorkflowAdmin/1/downloadDot')->
    isResponseHeader('content-type', 'text/plain; charset=utf-8')->
    isStatusCode(200)->
    
  get('/sfEzcWorkflowAdmin/100000000/downloadDot')->
    isStatusCode(302)->
    followRedirect()->
    
  with('response')->
    checkElement('body','/Invalid request. sfEzcWorkflowStorageException:/')
;