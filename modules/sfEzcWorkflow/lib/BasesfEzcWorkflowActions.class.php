<?php

/**
 * Base Actions for the sfEzcWorkflowPlugin sfEzcWorkflow module.
 * 
 * @package     sfEzcWorkflowPlugin
 * @subpackage  sfEzcWorkflow
 * @author      Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version     SVN: $Id:
 */
abstract class BasesfEzcWorkflowActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->redirect('@homepage');
  }
}
