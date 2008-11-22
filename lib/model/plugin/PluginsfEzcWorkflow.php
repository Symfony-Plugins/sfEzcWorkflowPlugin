<?php
/**
 * Handler that stores a ezcWorkflow definition on a database using propel objects
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class PluginsfEzcWorkflow extends BasesfEzcWorkflow
{
  
  protected $ezcWorkflow = null;
  
  /**
   * Populates and save the propel object using the workflow definition
   * @param ezcWorkflow $ezcWorkflow object to bind
   * @throws ezcWorkflowDefinitionStorageException
   * @throws PropelException
   */
  public function bindAndSaveEzcWorkflowObject( ezcWorkflow $ezcWorkflow ){
    $this->ezcWorkflow = $ezcWorkflow;
    $con = Propel::getConnection(sfEzcWorkflowPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
    $con->beginTransaction();
    try{
      $this->setName( $ezcWorkflow->name );
      $workflowVersion = sfEzcWorkflowPeer::getCurrentVersionNumber( $ezcWorkflow->name ) + 1;
      $this->setVersion( $workflowVersion );
      $this->save();
      $this->bindAndSaveEzcWorkflowNodes( $ezcWorkflow->nodes );
      $this->bindAndSaveEzcWorkflowVariableHandlers( $ezcWorkflow->getVariableHandlers() );
      $ezcWorkflow->id = $this->getId();
      $ezcWorkflow->version = $this->getVersion();
      $con->commit();
    } catch (PropelException $e) {
      $con->rollBack();
      throw $e;
    }
  }
  
  /**
   * Create sfEzcWorkflowNode and add them to sfEzcWorklow
   * @param Array $ezcNodes array of ezcWorkflowNode
   */
  protected function bindAndSaveEzcWorkflowNodes( $ezcNodes = array() ){
    if  (sizeof ( $ezcNodes ) == 0  ){
      throw new ezcWorkflowDefinitionStorageException( 'There aren\'t nodes to be processed.');
    }
    
    $keys = array_keys( $ezcNodes );
    $numNodes = count( $ezcNodes );
    //create Nodes
    for ( $i = 0; $i < $numNodes; $i++ )
    {
        $id = $keys[$i];
        $node = $ezcNodes[$id];
        $sfEzcNode = new sfEzcWorkflowNode();
        $sfEzcNode->setConfiguration( sfPropelEzcWorkflowUtil::serialize( $node->getConfiguration() ) );
        $sfEzcNode->setClassname( get_class( $node ) );
        $this->addsfEzcWorkflowNode( $sfEzcNode );
        $sfEzcNode->save();
        $node->setId( $sfEzcNode->getId() );
    }
    //Create connections between nodes
    for ( $i = 0; $i < $numNodes; $i++ ){
      $id = $keys[$i];
      $node = $ezcNodes[$id];
      foreach ( $node->getOutNodes() as $outNode ){
        $sfEzcNodeOut = new sfEzcWorkflowNode();
        $sfEzcNodeConnection= new sfEzcWorkflowNodeConnection();
        $sfEzcNodeConnection->setOutgoingNodeId( $outNode->getId() );
        $sfEzcNode = sfEzcWorkflowNodePeer::retrieveByPK( $node->getId() );
        $sfEzcNode->addsfEzcWorkflowNodeConnectionRelatedByIncomingNodeId($sfEzcNodeConnection);
        $sfEzcNode->save();
      }
    }
  }

  /**
   * Create sfEzcWorkflowVariableHandler and add them to sfEzcWorklow
   * @param Array $ezcVars array of ezcWorkflowVariableHandler
   */
  protected function bindAndSaveEzcWorkflowVariableHandlers( $ezcVars = array() ){
    foreach ( $ezcVars as $variable => $class ){
      $sfEzcVar = new sfEzcWorkflowVariableHandler();
      $sfEzcVar->setVariable( $variable );
      $sfEzcVar->setClassname( $class );
      $sfEzcVar->save();
      $this->addsfEzcWorkflowVariableHandler( $sfEzcVar );
    }
  }

  /**
   * Create sfEzcWorkflowVariableHandler and add them to sfEzcWorklow
   * @param Array $ezcVars array of ezcWorkflowVariableHandler
   */
  public function hydrateEzcWorkflow( ){
    
    $this->loadEzcNodesStructure();
    $this->ezcWorkflow->id = $this->getId();
    $this->ezcWorkflow->version = $this->getVersion();
    $this->loadEzcVariableHandler();
    $this->ezcWorkflow->verify();
    return $this->ezcWorkflow;
  }

  /**
   * Create wokflow's node structure and store it on $this->ezcWorkflow
   */  
  protected function loadEzcNodesStructure(){
    $nodes = array();
    $startNode = null;
    $defaultEndNode = null;
    $finallyNode = null;
    
    // Create ezcWorkflowNode objects.
    $result = $this->getsfEzcWorkflowNodes();
    foreach ( $result as $node ){
      //retrieve serialized configuration
      $configuration =  sfPropelEzcWorkflowUtil::unserialize( $node->getConfiguration() );
      $classname = $node->getClassname();
      $node_id = $node->getId();
      if ( is_null($configuration) ){
        $configuration = ezcWorkflowUtil::getDefaultConfiguration( $classname );
      }
      $nodes[ $node_id ] = new $classname( $configuration );
      $nodes[ $node_id ]->setId( $node_id );
      //identify start, end and finally type nodes
      if ( $classname == 'ezcWorkflowNodeStart' ){
        $startNode = $nodes[ $node_id ];
      }else if ( $classname == 'ezcWorkflowNodeEnd' && !isset( $defaultEndNode ) ){
        $defaultEndNode = $nodes[ $node_id ];
      }else if ( $classname == 'ezcWorkflowNodeFinally' && !isset( $finallyNode ) ){
        $finallyNode = $nodes[ $node_id ];
      }
    }
    
    //Validate there's start node and at least one end node
    if ( !isset( $startNode ) || !isset( $defaultEndNode ) ){
      throw new ezcWorkflowDefinitionStorageException( 'Could not load workflow definition. There is not a start or end node' );
    }

    // Connect node objects.
    $c = new Criteria();
    $c->add( sfEzcWorkflowNodePeer::WORKFLOW_ID, $this->getId() );
    $connections = sfEzcWorkflowNodeConnectionPeer::doSelectJoinsfEzcWorkflowNodeRelatedByIncomingNodeId($c , null, Criteria::INNER_JOIN);
    
    foreach ( $connections as $connection ){
      $nodes[ $connection->getIncomingNodeId() ]->addOutNode( $nodes[ $connection->getOutgoingNodeId() ] );
    }
    
    if ( !isset( $finallyNode ) || count( $finallyNode->getInNodes() ) > 0 ){
      $finallyNode = null;
    }

    // Create workflow object and add the node objects to it.
    $workflow = new ezcWorkflow( $this->getName(), $startNode, $defaultEndNode, $finallyNode );
    $this->ezcWorkflow = $workflow;
  }
  
  /**
   * Fetch stored ezcVariableHandlers and load them on $this->ezcWorkflow
   */  
  protected function loadEzcVariableHandler(){
    $c = new Criteria();
    $c->add( sfEzcWorkflowVariableHandlerPeer::WORKFLOW_ID, $this->getId() );
    $result = sfEzcWorkflowVariableHandlerPeer::doSelect( $c );
    foreach ( $result as $variableHandler )
    {
      $this->ezcWorkflow->addVariableHandler( $variableHandler->getVariable(), $variableHandler->getClassname() );
    }
  }

  public function __toString()
  {
    return $this->getName().' v'.$this->getVersion();
  }
}
