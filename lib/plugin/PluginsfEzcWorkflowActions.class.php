<?php

/**
 * PluginsfEzcWorkflowAction contain basic methods that can be reused to process
 * requests to be processed by a sfEzcWorkflow
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class PluginsfEzcWorkflowActions extends sfActions
{
  /**
   * This action resume a sfPropelEzcWorkflowExecution suspended when a
   * ezcWorkflowNodeInputFromSf is reached.
   * If after the workflow is resumed another ezcWorkflowNodeInputFromSf is reached
   * the action redirects to the module/action set in that node configuration.
   * @return sfPropelEzcWorkflowExecution
   */
  public function doWorkflowResume($execution_id, $variables)
  {
    $execution = new sfPropelEzcWorkflowExecution($execution_id);
    $this->validateWorkflowResumeRequest($execution);
    $execution->resume($variables);
    if ($execution->isSuspended())
    {
      $this->doProcessRemainingNodes($execution);
    }
    return $execution;
  }
  
  /**
   * This method validate if a request for resuming a workflow can be executed.
   * It checks credentials and form values
   * TODO: Identificar apropiadamente el nodo, si hay dos nodos activos de sf, cual tomar?
   */
  
  protected function validateWorkflowResumeRequest(sfPropelEzcWorkflowExecution $execution)
  {
    $active_nodes = $execution->getActivatedNodes();
    if (sizeof($active_nodes) <= 0 ){
      throw new sfException('Workflow resume request is not valid. No active node to execute');
    }
    foreach( $active_nodes as $node )
    {
      if ($node instanceof ezcWorkflowNodeInputFromSf)
      {
        //TODO: Validate if the node is the one that fits to this request
        if ($node->isSecure())
        {
          $credential = $node->getRequiredCredential();
          if (!$this->getUser()->hasCredential($credential))
          {
            return false;
          }
        }
        return true;
      }
    }
    throw new sfException('Workflow resume request is not valid');
  }
  
  /**
   * Check if there's ezcWorkflowNodeInputFromSf nodes active to be executed
   */
  protected function doProcessRemainingNodes(sfPropelEzcWorkflowExecution $execution)
  {
    if ($execution->isSuspended())
    {
      $active_nodes = $execution->getActivatedNodes();
      if (!sizeof($active_nodes)){
        throw new sfException('Suspended workflow has not active nodes');
      }
      foreach( $active_nodes as $node )
      {
        if ($node instanceof ezcWorkflowNodeInputFromSf)
        {
          if ($node->isSecure())
          {
            $credential = $node->getRequiredCredential();
            if (!$this->getUser()->hasCredential($credential))
            {
              continue;
            }
          }
          $this->redirect($node->getActionUri().'?sf_ezc_wf_execution_id='.$execution->getId());
        }
      }
    }else{
      throw new sfException('Workflow is running, can\'t be resumed');
    }
    throw new sfException('TODO: esta vaina es reusable, refactorizar');
  }
}