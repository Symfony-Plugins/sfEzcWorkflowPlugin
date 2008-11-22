<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * sfEzcWorkflowNodeConnection filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasesfEzcWorkflowNodeConnectionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'incoming_node_id' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowNode', 'add_empty' => true)),
      'outgoing_node_id' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowNode', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'incoming_node_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfEzcWorkflowNode', 'column' => 'id')),
      'outgoing_node_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfEzcWorkflowNode', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_node_connection_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowNodeConnection';
  }

  public function getFields()
  {
    return array(
      'incoming_node_id' => 'ForeignKey',
      'outgoing_node_id' => 'ForeignKey',
      'id'               => 'Text',
    );
  }
}
