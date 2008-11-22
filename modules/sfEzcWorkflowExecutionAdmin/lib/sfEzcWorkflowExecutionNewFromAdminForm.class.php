<?php

/**
 * sfEzcWorkflowExecution form to be used in the admin configurator.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormTemplate.php 10377 2008-07-21 07:10:32Z dwhittle $
 */
class sfEzcWorkflowExecutionNewFromAdminForm extends BasesfEzcWorkflowExecutionForm
{
  public function setup()
  {
    $this->setWidgets(array(
      'workflow_id'                          => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflow', 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'workflow_id'                          => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflow', 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

  }

}
