<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * sfEzcWorkflowExecution filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasesfEzcWorkflowExecutionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'workflow_id'                          => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflow', 'add_empty' => true)),
      'parent'                               => new sfWidgetFormFilterInput(),
      'started'                              => new sfWidgetFormFilterInput(),
      'variables'                            => new sfWidgetFormFilterInput(),
      'waiting_for'                          => new sfWidgetFormFilterInput(),
      'threads'                              => new sfWidgetFormFilterInput(),
      'next_thread_id'                       => new sfWidgetFormFilterInput(),
      'sf_ezc_workflow_execution_state_list' => new sfWidgetFormPropelChoice(array('model' => 'sfEzcWorkflowNode', 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'workflow_id'                          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'sfEzcWorkflow', 'column' => 'id')),
      'parent'                               => new sfValidatorInteger(array('required' => false)),
      'started'                              => new sfValidatorInteger(array('required' => false)),
      'variables'                            => new sfValidatorPass(array('required' => false)),
      'waiting_for'                          => new sfValidatorPass(array('required' => false)),
      'threads'                              => new sfValidatorPass(array('required' => false)),
      'next_thread_id'                       => new sfValidatorInteger(array('required' => false)),
      'sf_ezc_workflow_execution_state_list' => new sfValidatorPropelChoice(array('model' => 'sfEzcWorkflowNode', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution_filters[%s]');

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

    $criteria->addJoin(sfEzcWorkflowExecutionStatePeer::EXECUTION_ID, sfEzcWorkflowExecutionPeer::ID);

    $value = array_pop($values);
    $criterion = $criteria->getNewCriterion(sfEzcWorkflowExecutionStatePeer::NODE_ID, $value);

    foreach ($values as $value)
    {
      $criterion->addOr($criteria->getNewCriterion(sfEzcWorkflowExecutionStatePeer::NODE_ID, $value));
    }

    $criteria->add($criterion);
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowExecution';
  }

  public function getFields()
  {
    return array(
      'id'                                   => 'Text',
      'workflow_id'                          => 'ForeignKey',
      'parent'                               => 'Text',
      'started'                              => 'Text',
      'variables'                            => 'Text',
      'waiting_for'                          => 'Text',
      'threads'                              => 'Text',
      'next_thread_id'                       => 'Text',
      'sf_ezc_workflow_execution_state_list' => 'ManyKey',
    );
  }
}
