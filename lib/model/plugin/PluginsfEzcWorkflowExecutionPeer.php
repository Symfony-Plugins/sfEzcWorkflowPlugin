<?php

class PluginsfEzcWorkflowExecutionPeer extends BasesfEzcWorkflowExecutionPeer
{
  static public function doSelectReturnEzcWorklflowExecution(Criteria $c)
  {
    $c->clearSelectColumns();
    //TODO: don't load the whole objects just the ID
    //$c->addSelectColumn(sfEzcWorkflowExecutionPeer::ID);
    $executions = self::doSelect($c);
    //var_dump($execution_ids);
    $result = array();
    foreach ($executions as $execution){
      $result[] = new sfPropelEzcWorkflowExecution($execution->getId());      
    }
    return $result;
  }
}
