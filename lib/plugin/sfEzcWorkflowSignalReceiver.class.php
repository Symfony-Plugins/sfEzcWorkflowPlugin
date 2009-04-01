<?php
/**
 * Base class which receives the signals send by a workflow execution and redirect
 * it to symfony event system.
 * See http://www.symfony-project.org/book/1_2/17-Extending-Symfony#chapter_17_events
 */
class sfEzcWorkflowSignalReceiver
{
  protected $dispatcher = null;
 
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }
  /**
   * notify the event sf_ezc_workflow_execution.after_execution_started
   */
  public function afterExecutionStarted( ezcWorkflowExecution $execution )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_execution_started', array(
      'workflow_execution'        => $execution
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_execution_suspended
   */ 
  public function afterExecutionSuspended( ezcWorkflowExecution $execution )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_execution_suspended', array(
      'workflow_execution'        => $execution
    )));
  }
  
  /**
   * notify the event sf_ezc_workflow_execution.after_execution_resumed
   */  
  public function afterExecutionResumed( ezcWorkflowExecution $execution )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_execution_resumed', array(
      'workflow_execution'        => $execution
    )));
  }
 
  /**
   * notify the event sf_ezc_workflow_execution.after_execution_cancelled
   */   
  public function afterExecutionCancelled( ezcWorkflowExecution $execution )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_execution_cancelled', array(
      'workflow_execution'        => $execution
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_execution_ended
   */    
  public function afterExecutionEnded( ezcWorkflowExecution $execution )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_execution_ended', array(
      'workflow_execution'        => $execution
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.before_node_activated
   */     
  public function beforeNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node, ezcWorkflowSignalSlotReturnValue $return )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.before_node_activated', array(
      'signal_slot_returned_value'=>$return,
      'workflow_execution'        => $execution,
      'node'=>$node
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_node_activated
   */      
  public function afterNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_node_activated', array(
      'workflow_execution'        => $execution,
      'node'=>$node
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_node_executed
   */       
  public function afterNodeExecuted( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_node_executed', array(
      'workflow_execution'        => $execution,
      'node'=>$node
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_thread_started
   */ 
  public function afterThreadStarted( ezcWorkflowExecution $execution, $threadId, $parentId, $numSiblings )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_thread_started', array(
      'workflow_execution' => $execution,
      'thread_id'=>$threadId,
      'parent_id'=>$parentId,
      'num_siblings'=>$numSiblings
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.after_thread_ended
   */  
  public function afterThreadEnded( ezcWorkflowExecution $execution, $threadId )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_thread_ended', array(
      'workflow_execution' => $execution,
      'thread_id'=>$threadId
    )));
  }

  /**
   * notify the event sf_ezc_workflow_execution.before_variable_set
   */   
  public function beforeVariableSet( ezcWorkflowExecution $execution, $variableName, $value, ezcWorkflowSignalSlotReturnValue $return )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.before_variable_set', array(
      'workflow_execution' => $execution,
      'variable_name'=>$variableName,
      'value'=>$value,
      'signal_slot_returned_value'=>$return
    )));
  }
 
  /**
   * notify the event sf_ezc_workflow_execution.after_variable_set
   */   
  public function afterVariableSet( ezcWorkflowExecution $execution, $variableName, $value )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_variable_set', array(
      'workflow_execution' => $execution,
      'variable_name'=>$variableName,
      'value'=>$value
    )));
  }
 
  /**
   * notify the event sf_ezc_workflow_execution.before_variable_unset
   */   
  public function beforeVariableUnset( ezcWorkflowExecution $execution, $variableName, ezcWorkflowSignalSlotReturnValue $return )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.before_variable_unset', array(
      'workflow_execution' => $execution,
      'variable_name'=>$variableName,
      'signal_slot_returned_value'=>$return
    )));
  }
 
  /**
   * notify the event sf_ezc_workflow_execution.after_variable_unset
   */   
  public function afterVariableUnset( ezcWorkflowExecution $execution, $variableName )
  {
    $this->dispatcher->notify(new sfEvent($this, 'sf_ezc_workflow_execution.after_variable_unset', array(
      'workflow_execution' => $execution,
      'variable_name'=>$variableName
    )));
  } 
}