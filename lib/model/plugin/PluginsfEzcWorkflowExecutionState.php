<?php

class PluginsfEzcWorkflowExecutionState extends BasesfEzcWorkflowExecutionState
{
  public function setState($v){
    parent::setState(sfPropelEzcWorkflowUtil::serialize($v));
  }
  public function getState(){
    return sfPropelEzcWorkflowUtil::unserialize(parent::getState());
  }

  public function setActivatedFrom($v){
    parent::setActivatedFrom(sfPropelEzcWorkflowUtil::serialize($v));
  }
  public function getActivatedFrom(){
    return sfPropelEzcWorkflowUtil::unserialize(parent::getActivatedFrom());
  }
}
