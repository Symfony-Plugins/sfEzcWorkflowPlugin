<?php

class PluginsfEzcWorkflowExecution extends BasesfEzcWorkflowExecution
{
  public function setVariables($v){
    if (is_array($v))
    {
      parent::setVariables( sfPropelEzcWorkflowUtil::serialize( $v ) );
    }
    else
    {
      if (is_array(sfPropelEzcWorkflowUtil::unserialize($v)))
      {
        parent::setVariables( $v );
      }    
      else
      {
        throw new sfException('wtf!!!, you are adding a '.gettype($v). ' unserialized value format is: '.gettype(sfPropelEzcWorkflowUtil::unserialize($v)). ' value to set: '.sfPropelEzcWorkflowUtil::unserialize($v));
      }
    }
  }
  public function getVariables(){
    $v = parent::getVariables();
    if (is_array($v))
    {
      return $v;
    }
    else
    {
      $value = sfPropelEzcWorkflowUtil::unserialize($v);
      if (is_array($value))
      {
        return(sfPropelEzcWorkflowUtil::unserialize($v));
      }
      else
      {
        throw new sfException('wtf!!!, you are getting '.gettype($v). ' unserialized value format is: '.gettype(sfPropelEzcWorkflowUtil::unserialize($v)). ' value to set: '.sfPropelEzcWorkflowUtil::unserialize($v));
      }
    }
    //return sfPropelEzcWorkflowUtil::unserialize();
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
