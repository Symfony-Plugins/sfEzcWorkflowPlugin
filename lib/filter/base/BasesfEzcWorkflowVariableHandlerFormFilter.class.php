<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * sfEzcWorkflowVariableHandler filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasesfEzcWorkflowVariableHandlerFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'variable'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'variable'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sf_ezc_workflow_variable_handler_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfEzcWorkflowVariableHandler';
  }

  public function getFields()
  {
    return array(
      'workflow_id' => 'ForeignKey',
      'variable'    => 'Text',
      'classname'   => 'Text',
    );
  }
}
