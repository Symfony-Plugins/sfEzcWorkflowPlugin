<?php

/**
 * sfEzcWorkflowNode form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasesfEzcWorkflowNodeForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'workflow_id'                          => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflow', 'add_empty' => false)),
      'id'                                   => new sfWidgetFormInputHidden(),
      'classname'                            => new sfWidgetFormInput(),
      'configuration'                        => new sfWidgetFormTextarea(),
      'sf_ezc_workflow_execution_state_list' => new sfWidgetFormPropelChoiceMany(array('model' => 'sfEzcWorkflowExecution')),
    ));

    $this->setValidators(array(
      'workflow_id'                          => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflow', 'column' => 'id')),
      'id'                                   => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNode', 'column' => 'id', 'required' => false)),
      'classname'                            => new sfValidatorString(array('max_length' => 255)),
      'configuration'                        => new sfValidatorString(),
      'sf_ezc_workflow_execution_state_list' => new sfValidatorPropelChoiceMany(array('model' => 'sfEzcWorkflowExecution', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_node[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowNode';
  }


  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['sf_ezc_workflow_execution_state_list']))
    {
      $values = array();
      foreach ($this->object->getsfEzcWorkflowExecutionStates() as $obj)
      {
        $values[] = $obj->getExecutionId();
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
    $c->add(sfEzcWorkflowExecutionStatePeer::NODE_ID, $this->object->getPrimaryKey());
    sfEzcWorkflowExecutionStatePeer::doDelete($c, $con);

    $values = $this->getValue('sf_ezc_workflow_execution_state_list');
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $obj = new sfEzcWorkflowExecutionState();
        $obj->setNodeId($this->object->getPrimaryKey());
        $obj->setExecutionId($value);
        $obj->save();
      }
    }
  }

}
