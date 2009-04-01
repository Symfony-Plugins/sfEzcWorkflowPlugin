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
  const ANY_SF_NODE = 1;
  const ANY_INPUT_NODE = 2;
  const ANY_NODE = 3;

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
    $execution = self::retrieveWorkflowExecutionById($execution_id);
    self::validateWorkflowResumeRequest($execution, $variables, $action,self::ANY_SF_NODE);
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
   * @param integer              $type_nodes   says what nodes can be resumed (sf input, any input, any node)
   * @exception sfEzcWorkflowManagerException
   * @return true if the request is valid, false otherwise
   */
  
  static public function validateWorkflowResumeRequest(ezcWorkflowExecution $execution, $variables,sfAction $action, $type_nodes = self::ANY_SF_NODE )
  {
    $active_nodes = $execution->getActivatedNodes();
    if (sizeof($active_nodes) <= 0 ){
      throw new sfEzcWorkflowManagerException('Workflow resume request is not valid. No active nodes to execute');
    }
    $sfNodefound = false;
    foreach( $active_nodes as $node )
    {
      $sfNodefound = self::isNodeExecutableByUser($node,$action->getUser(),$type_nodes);
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
   * @param sfAction             $action        Action which call this method
   * @param integer              $type_nodes   says what nodes can be resumed (sf input, any input, any node)
   * @exception sfEzcWorkflowManagerException
   */
  static public function doProcessRemainingNodes(ezcWorkflowExecution $execution, sfAction $action, $type_nodes = self::ANY_SF_NODE )
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
        if (!self::isNodeExecutableByUser($node,$action->getUser(),$type_nodes))
        {
          continue;
        }
        if ($node instanceof ezcWorkflowNodeInputFromSf)
        {
          $action->redirect($node->getActionUri().'?sf_ezc_wf_execution_id='.$execution->getId());
        }
        else
        {
          $execution->resume();
        }
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
   * Validate if the user has rigths to execute
   * @param ezcWorkflowNode $node Input node to check execution restrictions
   * @param sfUser               $user User who wants to execute the node
   * @param integer              $type_nodes   says what nodes can be resumed (sf input, any input, any node)
   * @return boolean true if execution is granted, false otherwise
   */
  static public function isNodeExecutableByUser(ezcWorkflowNode $node, sfUser $user, $type_nodes)
  {
    if (($type_nodes == self::ANY_NODE ) && $node instanceof ezcWorkflowNode)
    {
      return true;
    }
    
    if (($type_nodes == self::ANY_NODE || $type_nodes == self::ANY_INPUT_NODE) && $node instanceof ezcWorkflowNodeInput)
    {
      return true;
    }

    if (($type_nodes == self::ANY_NODE || $type_nodes == self::ANY_INPUT_NODE || $type_nodes == self::ANY_SF_NODE ) && $node instanceof ezcWorkflowNodeInputFromSf)
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
    return self::createWorkflowExecution($workflow);
  }

  /**
   * @param String $name name of a workflow definition stored in a database
   * @return ezcWorkflowExecution
   */
  static public function createExecutionWorkflowByName($name)
  {
    //TODO: use a factory for sfPropel*
    $storage = new sfPropelEzcWorkflowDefinitionStorage();
    $workflow = $storage->loadByName($name);
    return self::createWorkflowExecution($workflow);
  }
  
  /**
   * Creates and set-up a new workflow execution
   * @return sfPropelEzcWorkflowExecution
   */
  static public function createWorkflowExecution(ezcWorkflow $workflow)
  {
    
    //TODO: use a factory for sfPropel*
    $execution = new sfPropelEzcWorkflowExecution();
    $execution->workflow = $workflow;
    self::registerPlugin($execution);
    self::registerEventLogger($execution);
    return $execution;
  }

  /**
   * Registers a plugin in the workflow execution based on the app.yml configuration
   * more info on: http://www.ezcomponents.org/docs/tutorials/WorkflowEventLogTiein
   * <pre>
   * all:
   *  sf_ezc_workflow:
   *    register_event_logger: on
   *    event_logger_classname: mysfEzcWorkflowEventLogger
   * </pre>   
   * @return sfEzcWorkflowExecution
   */
  static protected function registerEventLogger(ezcWorkflowExecution $execution)
  {
    if (!sfConfig::get('app_sf_ezc_workflow_register_event_logger'))
    {
      return $execution;
    }
    // Connect signals to slots.
    $logger_classname = sfConfig::get('app_sf_ezc_workflow_event_logger_classname');
    $logger = new $logger_classname;
    $execution->addListener( $logger ); 
    return $execution;
  }

  /**
   * Registers a plugin in the workflow execution based on the app.yml configuration
   * more info on: http://www.ezcomponents.org/docs/tutorials/WorkflowSignalSlotTiein
   * <pre>
   * all:
   *  sf_ezc_workflow:
   *    register_signal_receiver: on
   *    signal_receiver_classname: mysfEzcWorkflowSignalReceiver
   * </pre>   
   * @return sfEzcWorkflowExecution
   */
  static protected function registerPlugin(ezcWorkflowExecution $execution)
  {
    if (!sfConfig::get('app_sf_ezc_workflow_register_signal_receiver'))
    {
      return $execution;
    }
    // Connect signals to slots.
    $receiver_classname = sfConfig::get('app_sf_ezc_workflow_signal_receiver_classname');
    $receiver = new $receiver_classname(sfContext::getInstance()->getEventDispatcher());
    $signals = new ezcSignalCollection;
    $signals->connect( 'afterExecutionStarted', array( $receiver, 'afterExecutionStarted' ) );
    $signals->connect( 'afterExecutionSuspended', array( $receiver, 'afterExecutionSuspended' ) );
    $signals->connect( 'afterExecutionResumed', array( $receiver, 'afterExecutionResumed' ) );
    $signals->connect( 'afterExecutionCancelled', array( $receiver, 'afterExecutionCancelled' ) );
    $signals->connect( 'afterExecutionEnded', array( $receiver, 'afterExecutionEnded' ) );
    $signals->connect( 'beforeNodeActivated', array( $receiver, 'beforeNodeActivated' ) );
    $signals->connect( 'afterNodeActivated', array( $receiver, 'afterNodeActivated' ) );
    $signals->connect( 'afterNodeExecuted', array( $receiver, 'afterNodeExecuted' ) );
    $signals->connect( 'afterRolledBackServiceObject', array( $receiver, 'afterRolledBackServiceObject' ) );
    $signals->connect( 'afterThreadStarted', array( $receiver, 'afterThreadStarted' ) );
    $signals->connect( 'afterThreadEnded', array( $receiver, 'afterThreadEnded' ) );
    $signals->connect( 'beforeVariableSet', array( $receiver, 'beforeVariableSet' ) );
    $signals->connect( 'afterVariableSet', array( $receiver, 'afterVariableSet' ) );
    $signals->connect( 'beforeVariableUnset', array( $receiver, 'beforeVariableUnset' ) );
    $signals->connect( 'afterVariableUnset', array( $receiver, 'afterVariableUnset' ) );

    // Register SignalSlot workflow engine plugin.
    $plugin = new ezcWorkflowSignalSlotPlugin();
    $plugin->signals = $signals;    
    $execution->addPlugin( $plugin );
    return $execution;
  }
  
  /**
   * @param Integer $id ID of a workflow execution stored in a database
   * @return ezcWorkflowExecution
   */
  static public function retrieveWorkflowExecutionById($id)
  {
    //TODO: use a factory for sfPropel*
    $execution = new sfPropelEzcWorkflowExecution($id);
    self::registerPlugin($execution);
    self::registerEventLogger($execution);
    return $execution;
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
  
  /**
   * @return an array of all ezcWorkflowExecution availables on the database
   */
  static public function retrieveAllExecutions()
  {
    //TODO: use a factory for sfPropel*
    $c = new Criteria();
    //TODO: change for PropelPager
    $executions = sfEzcWorkflowExecutionPeer::doSelectReturnEzcWorklflowExecution($c);
    return $executions;
  }
}