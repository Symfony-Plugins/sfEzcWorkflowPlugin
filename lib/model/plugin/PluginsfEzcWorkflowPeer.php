<?php
/**
 * Workflow definition storage handler that saves and loads workflow
 * definitions to and from a database using propel objects.
 
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class PluginsfEzcWorkflowPeer extends BasesfEzcWorkflowPeer
{
  /**
  * Returns the current version number for a given workflow name.
  *
  * @param  string $workflowName
  * @return int
  */
  public static function getCurrentVersionNumber( $workflowName )
  {
    //SELECT MAX(workflow_version) AS version FROM workflow WHERE workflow_name = $workflowName
    $c = new Criteria();
    $c->clearSelectColumns();
    $c->addSelectColumn('MAX(' . sfEzcWorkflowPeer::VERSION . ')');
    $c->add(sfEzcWorkflowPeer::NAME, $workflowName );
    $result = sfEzcWorkflowPeer::doSelectStmt($c);
    $version = $result->fetchColumn(0);
    if ( $version > 0 ){
        return $version;
    }else{
        return 0;
    }
  }

}
