<?php
/**
 * Workflow definition storage handler that saves and loads workflow
 * definitions to and from a database using propel objects.
 
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class sfPropelEzcWorkflowDefinitionStorage implements ezcWorkflowDefinitionStorage
{
  /**
   * Load a workflow definition by name.
   *
   * @param  string  $workflowName
   * @param  int $workflowVersion
   * @return ezcWorkflow
   * @throws sfEzcWorkflowStorageException
   */
  public function loadByName($workflowName, $workflowVersion = 0)
  {
    // Load the current version of the workflow.
    if ($workflowVersion == 0)
    {
        $workflowVersion = sfEzcWorkflowPeer::getCurrentVersionNumber($workflowName);
    }
    $c = new Criteria();
    $c->add( sfEzcWorkflowPeer::NAME , $workflowName );
    $c->add( sfEzcWorkflowPeer::VERSION ,$workflowVersion );
    $sfEzcWorkflow = sfEzcWorkflowPeer::doSelectOne($c);
    if ( $sfEzcWorkflow instanceof sfEzcWorkflow ){
      $workflow = $sfEzcWorkflow->hydrateEzcWorkflow();
      $workflow->definitionStorage = $this;
      return $workflow;
    }else{
      throw new sfEzcWorkflowStorageException('Could not load workflow definition.');
    }
  }

  /**
   * Load a workflow definition by name.
   *
   * @param  string  $workflowName
   * @param  int $workflowVersion
   * @return ezcWorkflow
   * @throws sfEzcWorkflowStorageException
   */
  public function loadById($workflow_id)
  {
    // Load the current version of the workflow.
    $c = new Criteria();
    $c->add(sfEzcWorkflowPeer::ID , $workflow_id);
    $sfEzcWorkflow = sfEzcWorkflowPeer::doSelectOne($c);
    if ($sfEzcWorkflow instanceof sfEzcWorkflow)
    {
      $workflow = $sfEzcWorkflow->hydrateEzcWorkflow();
      $workflow->definitionStorage = $this;
      return $workflow;
    }
    else
    {
      throw new sfEzcWorkflowStorageException('Could not load workflow definition.');
    }
  }

  /**
   * Save a workflow definition to the database.
   *
   * @param  ezcWorkflow $workflow
   * @throws sfEzcWorkflowStorageException
   */
  public function save(ezcWorkflow $workflow)
  {
    // Verify the workflow.
    $workflow->verify();
    $propelWorkflow = new sfEzcWorkflow();
    try
    {
      $propelWorkflow->bindAndSaveEzcWorkflowObject($workflow);
    }
    catch(Exception $e)
    {
      throw new sfEzcWorkflowStorageException('An error has occurred storing the workflow definition. Error:'.$e->getMessage() );
    }
    
  }
}