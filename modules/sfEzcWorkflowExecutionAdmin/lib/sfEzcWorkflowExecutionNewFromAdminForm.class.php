<?php

/**
 * sfEzcWorkflowExecution form to be used in the admin configurator.
 *
 * @package    sfEzcWorkflowPlugin
 * @subpackage sfEzcWorkflowAdmin
 * @author     Cinxgler Mariaca Minda < cinxgler at gmail dot com >
 * @version    SVN: $Id:
 */
class sfEzcWorkflowExecutionNewFromAdminForm extends BasesfEzcWorkflowExecutionForm
{
  public function setup()
  {
    $widget = sfEzcWorkflowManager::getWorkflowDefinitionIdFormWidget();
    $validator = sfEzcWorkflowManager::getWorkflowDefinitionIdValidator();
    $this->setWidgets(array(
      'workflow_id' => $widget,
    ));

    $this->setValidators(array(
      'workflow_id' => $validator,
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

  }

}
