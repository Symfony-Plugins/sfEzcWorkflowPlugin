<?php
/**
 * sfTesterForm implements tests for Workflows
 *
 * @package    sfEzcWorkflowPlugin
 * @subpackage test
 * @author     Cinxgler Mariaca<cinxgler@gmail.com>
 */
class sfTesterEzcWorkflowExecution extends sfTester
{

  /**
   * Prepares the tester.
   */
  public function prepare()
  {
  }


  /**
   * Initiliazes the tester.
   */
  public function initialize()
  {
  }

  /**
   * Tests workflow execution is suspended
   *
   * @param  $id execution id
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isSuspended($id)
  {
    $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
    $this->tester->is($execution->isSuspended(), true, 'Execution id:'.$id.' is suspended');
    return $this->getObjectToReturn();
  }

  /**
   * Tests workflow execution is cancelled
   *
   * @param  $id execution id
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isCancelled($id)
  {
    $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
    $this->tester->is($execution->isCancelled(), true, 'Execution id:'.$id.' is cancelled');
    return $this->getObjectToReturn();
  }

  /**
   * Tests workflow execution is resumed
   *
   * @param  $id execution id
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isResumed($id)
  {
    $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
    $this->tester->is($execution->isResumed(), true, 'Execution id:'.$id.' is resumed');
    return $this->getObjectToReturn();
  }
  
  /**
   * Tests workflow execution is finished
   *
   * @param  $id execution id
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isFinished($id)
  {
    try
    {
      $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
      $this->tester->fail('Workflow execution id:'.$id.' is not finished');
    }catch(sfEzcWorkflowStorageException $e)
    {
      $this->tester->pass('Workflow execution id:'.$id.' is finished');
    }
    return $this->getObjectToReturn();
  }  

  /**
   * Tests if a workflow execution is waiting for a specific variable
   *
   * @param  $id execution id
   * @param  $variable variable name the execution is waiting for
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function isWaitingForVariable($id,$variable)
  {
    $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
    $waiting_for = $execution->getWaitingFor();
    if (sizeof($waiting_for) > 0)
    {
      $this->tester->is(array_key_exists($variable,$waiting_for), true, 'Execution id:'.$id.' is waiting for a variable called: '.$variable);
    }else
    {
      $this->tester->fail('Workflow execution is not waiting for any variable');
    }
    return $this->getObjectToReturn();
  }

  /**
   * Tests it workflow execution has a specific variable and value
   *
   * @param  $id execution id
   * @param  $variable variable name the execution is waiting for
   * @param  $value value to check
   * @param  $set set on false if the variable must not be set
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function check($id,$variable,$value,$set=true)
  {
    $execution = sfEzcWorkflowManager::retrieveWorkflowExecutionById($id);
    try
    {
      $current_value = $execution->getVariable($variable);
      if ($set === false )
      {
        $this->tester->fail('Workflow execution id:'.$id.' has set the variable: '.$variable. ' - when it must not');
      }
      else
      {
        $this->tester->is($current_value,$value,'Workflow execution id:'.$id.' has the variable: '.$variable . ' = '.var_export($value,true));
      }
    }catch(ezcWorkflowExecutionException $e)
    {
      if ($set === false )
      {
        $this->tester->pass('Workflow execution id:'.$id.' has not set the variable: '.$variable);
      }
      else
      {
        $this->tester->fail('Workflow execution id:'.$id.' has not set the variable: '.$variable. ' - when it must');
      }
    }
    return $this->getObjectToReturn();
  }
  
  
}
