<?php
/**
 * Logs workflow execution
 */
class sfEzcWorkflowEventLogger implements ezcWorkflowExecutionListener
{
  public function notify( $message, $type = ezcWorkflowExecutionListener::INFO )
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      sfContext::getInstance()->getLogger()->info('{sfEzcWorkflowExecution}'. $message);
    }
  }

}