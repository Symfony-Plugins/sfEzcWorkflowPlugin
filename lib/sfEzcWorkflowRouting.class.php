<?php
/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */
class sfEzcWorkflowRouting
{
  static public function addRouteForExecutionAdmin(sfEvent $event)
  {
    $event->getSubject()->prependRoute('sf_ezc_workflow_execution_admin', new sfPropelRouteCollection(array(
      'name'                => 'sfEzcWorkflowExecutionAdmin',
      'model'               => 'sfEzcWorkflowExecution',
      'module'              => 'sfEzcWorkflowExecutionAdmin',
      'prefix_path'         => 'sfEzcWorkflowExecutionAdmin',
      'with_wilcard_routes' => true,
      'requirements'        => array(),
    )));
  }
  static public function addRouteForAdmin(sfEvent $event)
  {
    $event->getSubject()->prependRoute('sf_ezc_workflow', new sfPropelRouteCollection(array(
      'name'                => 'sf_ezc_workflow',
      'model'               => 'sfEzcWorkflow',
      'module'              => 'sfEzcWorkflowAdmin',
      'prefix_path'         => 'sfEzcWorkflowAdmin',
      'with_wilcard_routes' => true,
      'requirements'        => array(),
    )));
  }
}