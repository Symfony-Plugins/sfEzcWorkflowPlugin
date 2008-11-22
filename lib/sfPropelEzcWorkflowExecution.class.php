<?php
/**
 * Workflow executer that suspends and resumes workflow
 * execution states to and from a database using propel.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */
class sfPropelEzcWorkflowExecution extends ezcWorkflowExecution
{
  /**
  * Flag that indicates whether the execution has been loaded.
  *
  * @var boolean
  */
  protected $loaded = false;

  /**
   * PropelPDO instance to be used.
   *
   * @var PropelPDO
   */
  protected $db;

  /**
   * Construct a new propel database execution.
   *
   * @param PropelPDO $db
   * @param int $executionId
   */
  public function __construct ( $executionId = null, PropelPDO $db = null )
  {
    if ($db === null) {
      $db = Propel::getConnection(sfEzcWorkflowPeer::DATABASE_NAME, Propel::CONNECTION_READ);
    }
    $this->db = $db;
    $this->properties['definitionStorage'] = new sfPropelEzcWorkflowDefinitionStorage();
    if ( !is_null($executionId) )
    {
      $this->loadExecution( $executionId );
    }
  }

  /**
   * Start workflow execution.
   *
   * @param  int $parentId
   */
  protected function doStart( $parentId )
  {
    $this->db->beginTransaction();
    $sfEzcExecution = new sfEzcWorkflowExecution();
    $sfEzcExecution->setWorkflowId($this->workflow->id);
    $sfEzcExecution->setParent($parentId);
    $sfEzcExecution->setStarted( time() );
    $sfEzcExecution->setVariables( $this->variables );
    $sfEzcExecution->setWaitingFor( $this->waitingFor );
    $sfEzcExecution->setThreads( $this->threads );
    $sfEzcExecution->setNextThreadId( $this->nextThreadId );
    $sfEzcExecution->save( $this->db );
    $this->id = $sfEzcExecution->getId();
  }

  /**
   * Suspend workflow execution.
   */
  protected function doSuspend()
  {
    $sfEzcExecution = sfEzcWorkflowExecutionPeer::retrieveByPK( $this->id, $this->db  );
    $sfEzcExecution->setVariables( $this->variables );
    $sfEzcExecution->setWaitingFor( $this->waitingFor );
    $sfEzcExecution->setThreads( $this->threads );
    $sfEzcExecution->setNextThreadId( $this->nextThreadId );
    $sfEzcExecution->save( $this->db );

    foreach ( $this->activatedNodes as $node )
    {
      $sfEzcNodeExecution = new sfEzcWorkflowExecutionState();
      $sfEzcNodeExecution->setExecutionId( $this->id );
      $sfEzcNodeExecution->setNodeId( $node->getId() );
      $sfEzcNodeExecution->setState( $node->getState() );
      $sfEzcNodeExecution->setActivatedFrom( $node->getActivatedFrom() );
      $sfEzcNodeExecution->setThreadId( $node->getThreadId() );
      $sfEzcNodeExecution->save( $this->db );
    }
    $this->db->commit();
  }

  /**
   * Resume workflow execution.
   */
  protected function doResume()
  {
    $this->db->beginTransaction();
    $this->cleanupExecutionState();
  }

  /**
   * End workflow execution.
   *
   */
  protected function doEnd()
  {
    $this->cleanupExecutionState();
    $this->cleanupExecution();
    if ( !$this->isCancelled() )
    {
      $this->db->commit();
    }
  }

  /**
   * Returns a new execution object for a sub workflow.
   *
   * @param  int $id
   * @return ezcWorkflowExecution
   */
  protected function doGetSubExecution( $id = null )
  {
      return new sfPropelEzcWorkflowExecution( $id, $this->db );
  }

  /**
   * Cleanup ExecutionState records.
   *
   */
  protected function cleanupExecutionState()
  {
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $this->id );
    sfEzcWorkflowExecutionStatePeer::doDelete( $c, $this->db );
  }

  /**
   * Cleanup Execution records.
   *
   */
  protected function cleanupExecution()
  {
    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionPeer::ID, $this->id );
    sfEzcWorkflowExecutionPeer::doDelete( $c, $this->db );
    $c = new Criteria();
    $c->Add( sfEzcWorkflowExecutionPeer::PARENT, $this->id );
    sfEzcWorkflowExecutionPeer::doDelete( $c, $this->db );

  }

  /**
   * Load execution state.
   *
   * @param int $executionId  ID of the execution to load.
   * @throws sfEzcWorkflowStorageException
   */
  protected function loadExecution( $executionId )
  {
    $result = sfEzcWorkflowExecutionPeer::retrieveByPK($executionId, $this->db);
    
    if ( !isset($result) ){
      throw new sfEzcWorkflowStorageException( 'Execution instance does\'nt exist.');
    }
    $this->id = $executionId;
    $this->nextThreadId = $result->getNextThreadId();
    $this->threads = $result->getThreads();
    $this->variables = $result->getVariables();
    $this->waitingFor = $result->getWaitingFor();

    $workflowId     = $result->getWorkflowId();
    $this->workflow = $this->properties['definitionStorage']->loadById( $workflowId );

    $c = new Criteria();
    $c->add( sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $executionId );
    $result = sfEzcWorkflowExecutionStatePeer::doSelect($c, $this->db);
    $activatedNodes = array();
    foreach ( $result as $row ){
      $activatedNodes[$row->getNodeId()] = array(
        'state' => $row->getState(),
        'activated_from' => $row->getActivatedFrom(),
        'thread_id' => $row->getThreadId()
      );
    }

    foreach ( $this->workflow->nodes as $node ){
      $nodeId = $node->getId();
      if ( isset( $activatedNodes[$nodeId] ) ){
        $node->setActivationState( ezcWorkflowNode::WAITING_FOR_EXECUTION );
        $node->setThreadId( $activatedNodes[$nodeId]['thread_id'] );
        $node->setState( $activatedNodes[$nodeId]['state'] );
        $node->setActivatedFrom( $activatedNodes[$nodeId]['activated_from'] );
        $this->activate( $node, false );
      }
    }

    $this->loaded = true;
  }
}
