<?php

/**
 * Base actions for the sfEzcWorkflowPlugin sfEzcWorkflowDemo module.
 * 
 * @package     sfEzcWorkflowPlugin
 * @subpackage  sfEzcWorkflowDemo
 * @author      Your name here
 * @version     SVN: $Id: BaseActions.class.php 12628 2008-11-04 14:43:36Z Kris.Wallsmith $
 */
abstract class BasesfEzcWorkflowDemoActions extends sfActions
{
 public function executeIndex(sfWebRequest $request)
  {
    $execution = sfEzcWorkflowManager::createExecutionByWorkflowByName('Test_sf');
    $id = $execution->start();
    sfEzcWorkflowManager::doProcessRemainingNodes($execution,$this);
  }
  
  public function executeChoice(sfWebRequest $request)
  {
    $this->sf_ezc_wf_execution_id = $request->getParameter('sf_ezc_wf_execution_id');
    if ($request->isMethod('post'))
    {
      $choice = $request->getParameter('choice')==='true'?true:false;
      $execution = sfEzcWorkflowManager::doWorkflowResume($this->sf_ezc_wf_execution_id, array('choice'=>$choice), $this);
      $this->variables = $execution->getVariables();
      $this->setTemplate('finished');
    }
  }
  
  public function executeMessage(sfWebRequest $request)
  {
    $this->sf_ezc_wf_execution_id = $request->getParameter('sf_ezc_wf_execution_id');
    if ($request->isMethod('post'))
    {
        $message = $request->getParameter('message');
        $execution = sfEzcWorkflowManager::doWorkflowResume($this->sf_ezc_wf_execution_id, array('message'=>$message),$this);
        $this->variables = $execution->getVariables()?$execution->getVariables():'nada';
        $this->setTemplate('finished');
    }
  }
}
