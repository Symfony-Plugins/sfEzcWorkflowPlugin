<?php

include(dirname(__FILE__).'/../bootstrap/functional.php');

$browser = new sfTestFunctional(new sfBrowser());

$c = new Criteria();
$wf_count = sfEzcWorkflowExecutionPeer::doCount($c);
echo $wf_count;

$browser->
  get('/sfEzcWorkflowExecutionAdmin/index')->

  with('request')->begin()->
    isParameter('module', 'sfEzcWorkflowExecutionAdmin')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body h1', '/sfEzcWorkflow Instances Admin/')->
  end()->
  
  get('/sfEzcWorkflowExecutionAdmin/new')->

  with('request')->begin()->
    isParameter('module', 'sfEzcWorkflowExecutionAdmin')->
    isParameter('action', 'new')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body h1','/Create a workflow instance/')->
  end()->
  
  click('Save')->
    isRedirected()->
    followRedirect()->
  with('response')->begin()->
    checkElement('body','/Workflow instance created sucessfully/')->
    checkElement('tr[class~="sf_admin_row"]',true, array('count'=>$wf_count+1))->
  end()->

  get('/sfEzcWorkflowExecutionAdmin/new')->

  with('request')->begin()->
    isParameter('module', 'sfEzcWorkflowExecutionAdmin')->
    isParameter('action', 'new')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body h1','/Create a workflow instance/')->
  end()->
  
  click('Save')->
    isRedirected()->
    followRedirect()->
  with('response')->begin()->
    checkElement('body','/Workflow instance created sucessfully/')->
    checkElement('tr[class~="sf_admin_row"]',true, array('count'=>$wf_count+2))->
  end()->

  click('Resume')->
  with('response')->begin()->
    isRedirected()->
  end()->
  back()->
  click('Cancel')->
    isRedirected()->
    followRedirect()->
  with('response')->begin()->
    checkElement('body','/Workflow instance cancelled sucessfully/')->
    checkElement('tr[class~="sf_admin_row"]',true, array('count'=>$wf_count+1))->
  end()
;