<?php

/**
 * Base Components for the sfEzcWorkflowPlugin sfEzcWorkflow module.
 * 
 * @package     sfEzcWorkflowPlugin
 * @subpackage  sfEzcWorkflow
 * @author      Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version     SVN: $Id:
 */
abstract class BasesfEzcWorkflowComponents extends sfComponents
{
  public function executeWaitingExecutions(){
    $executions = sfEzcWorkflowManager::retrieveAllExecutions();
    $user = $this->getUSer();
    $this->executions = sfEzcWorkflowManager::getExecutionsWaitingForUser($user, $executions);
  }
}
