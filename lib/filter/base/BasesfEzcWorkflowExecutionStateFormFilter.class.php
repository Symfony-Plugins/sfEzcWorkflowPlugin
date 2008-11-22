<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * sfEzcWorkflowExecutionState filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasesfEzcWorkflowExecutionStateFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'state'          => new sfWidgetFormFilterInput(),
      'activated_from' => new sfWidgetFormFilterInput(),
      'thread_id'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'state'          => new sfValidatorPass(array('required' => false)),
      'activated_from' => new sfValidatorPass(array('required' => false)),
      'thread_id'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_execution_state_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowExecutionState';
  }

  public function getFields()
  {
    return array(
      'execution_id'   => 'ForeignKey',
      'node_id'        => 'ForeignKey',
      'state'          => 'Text',
      'activated_from' => 'Text',
      'thread_id'      => 'Text',
    );
  }
}
