<?php

/**
 * sfEzcWorkflowNodeConnection form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasesfEzcWorkflowNodeConnectionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'incoming_node_id' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowNode', 'add_empty' => false)),
      'outgoing_node_id' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowNode', 'add_empty' => false)),
      'id'               => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'incoming_node_id' => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNode', 'column' => 'id')),
      'outgoing_node_id' => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNode', 'column' => 'id')),
      'id'               => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNodeConnection', 'column' => 'id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_node_connection[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowNodeConnection';
  }


}
