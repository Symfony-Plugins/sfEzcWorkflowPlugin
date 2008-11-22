<?php

/**
 * sfEzcWorkflowExecutionState form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasesfEzcWorkflowExecutionStateForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'execution_id'   => new sfWidgetFormInputHidden(),
      'node_id'        => new sfWidgetFormInputHidden(),
      'state'          => new sfWidgetFormTextarea(),
      'activated_from' => new sfWidgetFormTextarea(),
      'thread_id'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'execution_id'   => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowExecution', 'column' => 'id', 'required' => false)),
      'node_id'        => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNode', 'column' => 'id', 'required' => false)),
      'state'          => new sfValidatorString(),
      'activated_from' => new sfValidatorString(),
      'thread_id'      => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution_state[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowExecutionState';
  }


}
