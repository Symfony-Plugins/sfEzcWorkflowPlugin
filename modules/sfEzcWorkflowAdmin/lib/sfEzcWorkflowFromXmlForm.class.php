<?php
/**
 * sfEzcWorkflowFromXmlForm get the xml file for creating a new sfEzcWorkflow
 *
 * @package    plugin
 * @subpackage sfEzcWorkflowAdmin
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id: 
 */
class sfEzcWorkflowFromXmlForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'name'        => new sfWidgetFormInput(),
      'xml_file'    => new sfWidgetFormInputFile(),
    ));
    $this->widgetSchema->setNameFormat('sfEzcWorkflow[%s]');
 
    $xml_mime_category = array('xml_files'=>array('text/xml'));
    
    $this->setValidators(array(
      'name'    => new sfValidatorString(array('required' => true, 'min_length' => 4, 'max_length' => 255)),
      'xml_file'=> new sfValidatorFile(array('required' => true, 'mime_categories'=>$xml_mime_category)),
    ));
  }
}