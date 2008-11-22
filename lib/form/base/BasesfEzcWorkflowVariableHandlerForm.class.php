<?php

/**
 * sfEzcWorkflowVariableHandler form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasesfEzcWorkflowVariableHandlerForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'workflow_id' => new sfWidgetFormInputHidden(),
      'variable'    => new sfWidgetFormInput(),
      'classname'   => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'workflow_id' => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflow', 'column' => 'id', 'required' => false)),
      'variable'    => new sfValidatorString(array('max_length' => 255)),
      'classname'   => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowVariableHandler', 'column' => 'classname', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_variable_handler[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowVariableHandler';
  }


}
