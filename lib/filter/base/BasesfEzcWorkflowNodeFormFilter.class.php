<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * sfEzcWorkflowNode filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasesfEzcWorkflowNodeFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'workflow_id'                          => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflow', 'add_empty' => true)),
      'classname'                            => new sfWidgetFormFilterInput(),
      'configuration'                        => new sfWidgetFormFilterInput(),
      'sf_ezc_workflow_execution_state_list' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowExecution', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'workflow_id'                          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfEzcWorkflow', 'column' => 'id')),
      'classname'                            => new sfValidatorPass(array('required' => false)),
      'configuration'                        => new sfValidatorPass(array('required' => false)),
      'sf_ezc_workflow_execution_state_list' => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowExecution', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_node_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function addsfEzcWorkflowExecutionStateListColumnCriteria(Criteria $criteria, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $criteria->addJoin(sfEzcWorkflowExecutionStatePeer::NODE_ID, sfEzcWorkflowNodePeer::ID);

    $value = array_pop($values);
    $criterion = $criteria->getNewCriterion(sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $value);

    foreach ($values as $value)
    {
      $criterion->addOr($criteria->getNewCriterion(sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, $value));
    }

    $criteria->add($criterion);
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowNode';
  }

  public function getFields()
  {
    return array(
      'workflow_id'                          => 'ForeignKey',
      'id'                                   => 'Text',
      'classname'                            => 'Text',
      'configuration'                        => 'Text',
      'sf_ezc_workflow_execution_state_list' => 'ManyKey',
    );
  }
}
