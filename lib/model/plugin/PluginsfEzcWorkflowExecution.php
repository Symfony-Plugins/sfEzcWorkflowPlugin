<?php

class PluginsfEzcWorkflowExecution extends BasesfEzcWorkflowExecution
{
  public function setVariables($v){
    parent::setVariables( sfPropelEzcWorkflowUtil::serialize( $v ) );
  }
  public function getVariables( ){
    return sfPropelEzcWorkflowUtil::unserialize(parent::getVariables());
  }
  
  public function setWaitingFor($v){
    parent::setWaitingFor(sfPropelEzcWorkflowUtil::serialize($v));
  }
  public function getWaitingFor( ){
    return sfPropelEzcWorkflowUtil::unserialize(parent::getWaitingFor());
  }

  public function setThreads($v){
    parent::setThreads(sfPropelEzcWorkflowUtil::serialize($v));
  }
  public function getThreads( ){
    return sfPropelEzcWorkflowUtil::unserialize(parent::getThreads()) ;
  }
  public function __toString()
  {
    return $this->getsfEzcWorkflow->name;
  }

}
