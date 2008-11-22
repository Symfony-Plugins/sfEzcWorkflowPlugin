<?
/**
 * sf_ezc_workflow_execution actions.
 *
 * @package    ci
 * @subpackage sf_ezc_workflow_execution
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class PluginsfEzcWorkflowExecutionAdminActions extends autoSfEzcWorkflowExecutionAdminActions
{
  
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new sfEzcWorkflowExecutionNewFromAdminForm();
    $this->sf_ezc_workflow_execution = $this->form->getObject();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new sfEzcWorkflowExecutionNewFromAdminForm();
    $this->form->bind($request->getParameter($this->form->getName()));
    $this->sf_ezc_workflow_execution = $this->form->getObject();
    $is_valid = $this->form->getValue('workflow_id');
    if ($is_valid)
    {
      try
      {
        $storage = new sfPropelEzcWorkflowDefinitionStorage();
        $workflow = $storage->loadById($this->form->getValue('workflow_id'));
        $execution = new sfPropelEzcWorkflowExecution();
        $execution->workflow = $workflow;
        $id = $execution->start();
        if ($request->hasParameter('_save_and_add'))
        {
          $this->getUser()->setFlash('notice', $this->getUser()->getFlash('notice').' Workflow instance created sucessfully. You create another one below.');
        }
        else
        {
          $this->getUser()->setFlash('notice', $this->getUser()->getFlash('notice').' Workflow instance created sucessfully.');
        }
      }catch(Exception $e)
      {
        $this->getUser()->setFlash('error', 'The item has not been saved due an internal error: '. get_class($e).': '.$e->getMessage());
      }
      if ($request->hasParameter('_save_and_add'))
      {
        $this->redirect('@sfEzcWorkflowExecutionAdmin_new');
      }
      else
      {
        $this->redirect('@sfEzcWorkflowExecutionAdmin');
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.');
    }
    $this->setTemplate('new');
  }
  
  /**
   * Validate an resume request and execute doResume
   */
  public function executeResume(sfWebRequest $request)
  {
    $execution_id = $request->getParameter('id');
    if ( $execution_id > 0)
    {
      try
      {
        $this->doResume($execution_id);
        $this->getUser()->setFlash('error', 'There isn\'t any ezcWorkflowNodeInputFromSf node waiting for being executed');
      }catch(Exception $e)
      {
        $this->getUser()->setFlash('error', 'The workflow could\'nt be resumed : '. get_class($e).': '.$e->getMessage());
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The workflow instance is invalid.');
    }
    $this->redirect('@sfEzcWorkflowExecutionAdmin');
  }

  /**
   * Validate an cancel request and execute doCancel
   */
  public function executeCancel(sfWebRequest $request)
  {
    $execution_id = $request->getParameter('id');
    if ( $execution_id > 0)
    {
      try
      {
        $this->doCancel($execution_id);
        $this->getUser()->setFlash('notice', $this->getUser()->getFlash('notice').' Workflow instance cancelled sucessfully.');
      }catch(Exception $e)
      {
        $this->getUser()->setFlash('error', 'The workflow could\'nt be Cancel : '. get_class($e).': '.$e->getMessage());
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The workflow instance is invalid.');
    }
    $this->redirect('@sfEzcWorkflowExecutionAdmin');
  }
  
  /**
   * Resume a workflow suspended by an ezcWorkflowNodeInputFromSf node
   */
  public function doResume($execution_id)
  {
    $execution = new sfPropelEzcWorkflowExecution($execution_id);
    sfEzcWorkflowActionsUtil::doProcessRemainingNodes($execution,$this);
  }

  /**
   * Cancel a workflow workflow
   */
  public function doCancel($execution_id)
  {
    $execution = new sfPropelEzcWorkflowExecution($execution_id);
    $execution->cancel();
  }
  
  /**
   * Cancel in batch
   */
  protected function executeBatchCancelAll(sfWebRequest $request)
  {
    $ids = $request->getParameter('ids');

    foreach ($ids as $execution_id)
    {
      try
      {
        $this->doCancel($execution_id);
      }catch(Exception $e)
      {
        $this->getUser()->setFlash('error', 'The workflow '.$execution_id.'could\'nt be Cancel : '. get_class($e).': '.$e->getMessage());
      }
    }
    $this->redirect('@sfEzcWorkflowExecutionAdmin');
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404();
  }

  protected function executeBatchDelete(sfWebRequest $request)
  {
    $this->forward404();
  }

}