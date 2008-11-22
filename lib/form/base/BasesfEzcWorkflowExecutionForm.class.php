<?php

/**
 * sfEzcWorkflowExecution form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasesfEzcWorkflowExecutionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'workflow_id'                          => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflow', 'add_empty' => false)),
      'parent'                               => new sfWidgetFormInput(),
      'started'                              => new sfWidgetFormInput(),
      'variables'                            => new sfWidgetFormTextarea(),
      'waiting_for'                          => new sfWidgetFormTextarea(),
      'threads'                              => new sfWidgetFormTextarea(),
      'next_thread_id'                       => new sfWidgetFormInput(),
      'sf_ezc_workflow_execution_state_list' => new sfWidgetFormPropelChoiceMany(array('model' => 'sfEzcWorkflowNode')),
    ));

    $this->setValidators(array(
      'id'                                   => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowExecution', 'column' => 'id', 'required' => false)),
      'workflow_id'                          => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflow', 'column' => 'id')),
      'parent'                               => new sfValidatorInteger(),
      'started'                              => new sfValidatorInteger(),
      'variables'                            => new sfValidatorString(),
      'waiting_for'                          => new sfValidatorString(),
      'threads'                              => new sfValidatorString(),
      'next_thread_id'                       => new sfValidatorInteger(),
      'sf_ezc_workflow_execution_state_list' => new sfValidatorPropelChoiceMany(array('model' => 'sfEzcWorkflowNode', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowExecution';
  }


  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['sf_ezc_workflow_execution_state_list']))
    {
      $values = array();
      foreach ($this->object->getsfEzcWorkflowExecutionStates() as $obj)
      {
        $values[] = $obj->getNodeId();
      }

      $this->setDefault('sf_ezc_workflow_execution_state_list', $values);
    }

  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->savesfEzcWorkflowExecutionStateList($con);
  }

  public function savesfEzcWorkflowExecutionStateList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['sf_ezc_workflow_execution_state_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $c = new Criteria();
    $c->add(sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $this->object->getPrimaryKey());
    sfEzcWorkflowExecutionStatePeer::doDelete($c, $con);

    $values = $this->getValue('sf_ezc_workflow_execution_state_list');
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $obj = new sfEzcWorkflowExecutionState();
        $obj->setExecutionId($this->object->getPrimaryKey());
        $obj->setNodeId($value);
        $obj->save();
      }
    }
  }

}
