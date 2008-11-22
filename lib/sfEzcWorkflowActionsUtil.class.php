<?php

/**
 * sfEzcWorkflowActionUtil contain basic methods that can be reused to process
 * sfEzcWorkflowExecution from actions
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class sfEzcWorkflowActionsUtil
{
  /**
   * This action resume a sfPropelEzcWorkflowExecution suspended when a
   * ezcWorkflowNodeInputFromSf is reached.
   * If after the workflow is resumed another ezcWorkflowNodeInputFromSf is reached
   * the action redirects to the module/action set in that node configuration.
   * @param Integer  $execution_id
   * @param Array    $variables an associative array with the variable to pass for
   *                            resuming the workflow instance
   * @param sfAction $action Action which call this method
   * @return ezcWorkflowExecution
   */
  static public function doWorkflowResume($execution_id, $variables, sfAction $action)
  {
    $execution = new sfPropelEzcWorkflowExecution($execution_id);
    self::validateWorkflowResumeRequest($execution, $variables, $action);
    $execution->resume($variables);
    if ($execution->isSuspended())
    {
      self::doProcessRemainingNodes($execution,$action);
    }
    return $execution;
  }
  
  /**
   * This method validate if a request for resuming a workflow can be executed for
   * the current symfony action user.
   * @todo: validate variables as well. TODO
   * @param ezcWorkflowExecution $execution
   * @param array                $variables an associative array with the variable to pass for
   *                                        resuming the workflow instance
   * @param sfAction             $action Action which call this method
   * @exception sfEzcWorkflowActionException
   * @return true if the request is valid, false otherwise
   */
  
  static public function validateWorkflowResumeRequest(ezcWorkflowExecution $execution, $variables,sfAction $action)
  {
    $active_nodes = $execution->getActivatedNodes();
    if (sizeof($active_nodes) <= 0 ){
      throw new sfEzcWorkflowActionException('Workflow resume request is not valid. No active nodes to execute');
    }
    $sfNodefound = false;
    foreach( $active_nodes as $node )
    {
      if ($node instanceof ezcWorkflowNodeInputFromSf)
      {
        if ($node->isSecure())
        {
          $credential = $node->getRequiredCredential();
          if (!$action->getUser()->hasCredential($credential))
          {
            $sfNodefound = false;
          }
        }
        else
        {
          $sfNodefound = true;
        }
      }
      if ($sfNodefound)
      {
        return true;
      }
    }
    throw new sfEzcWorkflowActionException('Workflow resume request is not valid');
  }
  
  /**
   * Check if there's ezcWorkflowNodeInputFromSf nodes active and redirect to
   * the corresponding action
   * @param ezcWorkflowExecution $execution
   * @param sfAction             $action     Action which call this method
   * @exception sfEzcWorkflowActionException
   */
  static public function doProcessRemainingNodes(ezcWorkflowExecution $execution, sfAction $action)
  {
    $waiting_for = $execution->getWaitingFor();
    if ($execution->isSuspended() || sizeof($waiting_for) > 0)
    {
      $active_nodes = $execution->getActivatedNodes();
      if (!sizeof($active_nodes)){
        throw new sfEzcWorkflowActionException('Suspended workflow has not active nodes');
      }
      foreach( $active_nodes as $node )
      {
        if ($node instanceof ezcWorkflowNodeInputFromSf)
        {
          if ($node->isSecure())
          {
            $credential = $node->getRequiredCredential();
            if (!$action->getUser()->hasCredential($credential))
            {
              continue;
            }
          }
          $action->redirect($node->getActionUri().'?sf_ezc_wf_execution_id='.$execution->getId());
        }
      }
    }else{
      throw new sfEzcWorkflowActionException('Workflow is running and doesn\'t require any input');
    }
  }
}