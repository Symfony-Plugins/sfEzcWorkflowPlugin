<?php

/**
 * sfEzcWorkflowManager contain basic methods that can be reused to process
 * ezcWorkflowExecution and ezcWorkflos from actions
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class sfEzcWorkflowManager
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
   * @exception sfEzcWorkflowManagerException
   * @return true if the request is valid, false otherwise
   */
  
  static public function validateWorkflowResumeRequest(ezcWorkflowExecution $execution, $variables,sfAction $action)
  {
    $active_nodes = $execution->getActivatedNodes();
    if (sizeof($active_nodes) <= 0 ){
      throw new sfEzcWorkflowManagerException('Workflow resume request is not valid. No active nodes to execute');
    }
    $sfNodefound = false;
    foreach( $active_nodes as $node )
    {
      $sfNodefound = self::isNodeExecutableByUser($node,$action->getUser());
      if ($sfNodefound)
      {
        return true;
      }
    }
    throw new sfEzcWorkflowManagerException('Workflow resume request is not valid');
  }
  
  /**
   * Check if there's ezcWorkflowNodeInputFromSf nodes active and redirect to
   * the corresponding action
   * @param ezcWorkflowExecution $execution
   * @param sfAction             $action     Action which call this method
   * @exception sfEzcWorkflowManagerException
   */
  static public function doProcessRemainingNodes(ezcWorkflowExecution $execution, sfAction $action)
  {
    $waiting_for = $execution->getWaitingFor();
    if ($execution->isSuspended() || sizeof($waiting_for) > 0)
    {
      $active_nodes = $execution->getActivatedNodes();
      if (!sizeof($active_nodes)){
        throw new sfEzcWorkflowManagerException('Suspended workflow has not active nodes');
      }
      foreach( $active_nodes as $node )
      {
        if (!self::isNodeExecutableByUser($node,$action->getUser()))
        {
          continue;
        }
        $action->redirect($node->getActionUri().'?sf_ezc_wf_execution_id='.$execution->getId());
      }
    }else{
      throw new sfEzcWorkflowManagerException('Workflow is running and doesn\'t require any input');
    }
  }
  
  /**
   * Check which executions instances are available for user execution
   * @param sfUser $user         User who wants to execute the workflow instance
   * @param Array  $executions   Array with execution instances to check
   */
  static public function getExecutionsWaitingForUser(sfUser $user,$executions)
  {
    if (!is_array($executions))
    {
      throw new sfEzcWorkflowManagerException('An array is required');
    }
    $result = array();
    foreach($executions as $execution)
    {
      if ($execution->isSuspended())
      {
        $activatedNodes = $execution->getActivatedNodes();
        foreach ($activatedNodes as $node)
        {
          if (self::isNodeExecutableByUser($node, $user))
          {
            $result[] = $execution;
            break;
          }
        }
      }
    }
    return $result;
  }
  
  /**
   * Validate if the user has rigths to execute the node
   * @param ezcWorkflowNodeInput $node Input node to check execution restrictions
   * @param sfUser               $user User who wants to execute the node
   * @return boolean true if execution is granted, false otherwise
   */
  static public function isNodeExecutableByUser(ezcWorkflowNodeInput $node, sfUser $user)
  {
    if ($node instanceof ezcWorkflowNodeInputFromSf)
    {
      if ($node->isSecure())
      {
        $credential = $node->getRequiredCredential();
        if (!$user->hasCredential($credential))
        {
          return false;
        }
      }
      return true;
    }
    return false;
  }

  /**
   * @param Integer $id ID of a workflow definition stored in a database
   * @return ezcWorkflowExecution
   */
  static public function createExecutionByWorkflowId($id)
  {
    //TODO: use a factory for sfPropel*
    $storage = new sfPropelEzcWorkflowDefinitionStorage();
    $workflow = $storage->loadById($id);
    $execution = new sfPropelEzcWorkflowExecution();
    $execution->workflow = $workflow;
    return $execution;
  }

  /**
   * @param String $name name of a workflow definition stored in a database
   * @return ezcWorkflowExecution
   */
  static public function createExecutionByWorkflowByName($name)
  {
    //TODO: use a factory for sfPropel*
    $storage = new sfPropelEzcWorkflowDefinitionStorage();
    $workflow = $storage->loadByName($name);
    $execution = new sfPropelEzcWorkflowExecution();
    $execution->workflow = $workflow;
    return $execution;
  }

  
  /**
   * @param Integer $id ID of a workflow execution stored in a database
   * @return ezcWorkflowExecution
   */
  static public function retrieveWorkflowExecutionById($id)
  {
    //TODO: use a factory for sfPropel*
    return new sfPropelEzcWorkflowExecution($id);
  }
  
  /**
   *
   */
  static public function saveWorkflowOnDatabase(ezcWorkflow $workflow)
  {
    //TODO: use a factory for sfPropel*
    $storage = new sfPropelEzcWorkflowDefinitionStorage();
    $storage->save( $workflow );
  }

  /**
   * @param Integer $workflow_id ID of a workflow definition stored in a database
   * @return ezcWorkflow
   */
  static public function retrieveWorkflowDefinitionById($workflow_id)
  {
    //TODO: use a factory for sfPropel*
    $storage = new sfPropelEzcWorkflowDefinitionStorage();
    $workflow = $storage->loadById($workflow_id);
    return $workflow;
  }
  
  /**
   * @param array $options an array of options for widget constructor
   * @return sfWidget which validates if a given id is valid to retrieve a workflow definition from a database
   */
  static public function getWorkflowDefinitionIdFormWidget($options = array())
  {
    //TODO: use a factory for sfPropel*
    $widget_options = array_merge(array('model' => 'sfEzcWorkflow', 'add_empty' => false), $options);
    return new sfWidgetFormPropelChoice($widget_options);
  }
  
  /**
   * @param array $options an array of options for widget constructor
   * @return sfWidget which validates if a given id is valid to retrieve a workflow definition from a database
   */
  static public function getWorkflowDefinitionIdValidator($options = array())
  {
    //TODO: use a factory for sfPropel*
    $validator_options = array_merge(array('model' => 'sfEzcWorkflow', 'column' => 'id'), $options);
    return new sfValidatorPropelChoice($validator_options);
  }
}