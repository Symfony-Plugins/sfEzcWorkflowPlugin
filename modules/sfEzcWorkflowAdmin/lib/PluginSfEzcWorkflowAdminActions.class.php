<?php
/**
 * sfEzcWorkflowAdmin actions.
 *
 * @package    plugin
 * @subpackage sfEzcWorkflowAdmin
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id: 
 */
class PluginSfEzcWorkflowAdminActions extends autoSfEzcWorkflowAdminActions
{
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new sfEzcWorkflowFromXmlForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('sfEzcWorkflow'), $request->getFiles('sfEzcWorkflow'));
      if ($this->form->isValid())
      {
        $file = $this->form->getValue('xml_file');
        $name = $this->form->getValue('name');
        $file->save(sfConfig::get('sf_upload_dir').'/'.$name.'_0.xml');

        $definition = new ezcWorkflowDefinitionStorageXml( sfConfig::get('sf_upload_dir').'/' );
        $workflow = $definition->loadByName($name);
        sfEzcWorkflowManager::saveWorkflowOnDatabase($workflow);
        $this->getUser()->setFlash('notice', $this->getUser()->getFlash('notice').' Workflow saved on database successfully.');
        $this->redirect('@sf_ezc_workflow');
      }else{
        $this->getUser()->setFlash('error', $this->getUser()->getFlash('error').' Workflow was not stored. Invalid data');
        $this->redirect('@sf_ezc_workflow');
      }
    }
  }
  
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->workflow = $this->doLoadWorkflow($request->getParameter('id'));
  }
  
  public function executeDownloadXml(sfWebRequest $request)
  {
    try
    {
      //TODO: validate id 
      $workflow = $this->doLoadWorkflow($request->getParameter('id'));
      $definition = new ezcWorkflowDefinitionStorageXml( sfConfig::get('sf_upload_dir') );
      $xml_content = $definition->saveToDocument($workflow, $workflow->version)->saveXML();
      $parameters=array();
      $parameters['mime']='text/xml';
      $parameters['filename']=$workflow->name.'_'.$workflow->version.'.xml';
      $parameters['content-length']=strlen($xml_content);
      $this->setDowloadHeaders($parameters);
      return $this->renderText($xml_content);
    }
    catch (Exception $e)
    {
      $this->getUser()->setFlash('error', $this->getUser()->getFlash('error').' Invalid request. '.get_class($e).':'.$e->getMessage());
      $this->redirect('@sf_ezc_workflow');
    }

  }
  
  public function executeDownloadDot(sfWebRequest $request)
  {
    try
    {
      //TODO: validate id
      $workflow = $this->doLoadWorkflow($request->getParameter('id'));
      $visitor = new ezcWorkflowVisitorVisualization;
      $workflow->accept( $visitor );
      $message = "/* You can upload this file on http://urlgreyhot.com/graphviz/index.php to get a nice graph */\n/* or on unix run 'dot -Tpng -O filename.dot' */\n";
      $dot_content = $message.$visitor->__toString();
      $parameters=array();
      $parameters['mime']='text/plain';
      $parameters['filename']=$workflow->name.'_'.$workflow->version.'.dot';
      $parameters['content-length']=strlen($dot_content);
      $this->setDowloadHeaders($parameters);
      return $this->renderText($dot_content);
    }
    catch (Exception $e)
    {
      $this->getUser()->setFlash('error', $this->getUser()->getFlash('error').' Invalid request. '.get_class($e).':'.$e->getMessage());
      $this->redirect('@sf_ezc_workflow');
    }
  }
  
  protected function doLoadWorkflow($workflow_id)
  {
    $workflow = sfEzcWorkflowManager::retrieveWorkflowDefinitionById($workflow_id);
    return $workflow;
  }

  protected function setDowloadHeaders($parameters = array())
  {
    $response = $this->getResponse();
    $response->clearHttpHeaders();
    $response->setContentType($parameters['mime']);
    $response->setHttpHeader('content-length',$parameters['content-length']);
    $response->setHttpHeader('Content-Disposition','attachment; filename='.$parameters['filename']);
    $this->setLayout(false);
  }
  
  public function executeDownloadPng(sfWebRequest $request)
  {
    //Requires http://pear.php.net/package/Image_GraphViz but Doesn't work :(
    $workflow = $this->doLoadWorkflow($request->getParameter('id'));
    $visitor = new ezcWorkflowVisitorVisualization;
    $workflow->accept( $visitor );
    $dot_content = $visitor->__toString();
    require_once 'Image/GraphViz.php';
    $graph = new Image_GraphViz();
    $graph->load($dot_content);
    $png_content = $graph->fetch('png');
    $parameters=array();
    $parameters['mime']='image/png';
    $parameters['filename']=$workflow->name.'_'.$workflow->version.'.png';
    $parameters['content-length']=sizeof($png_content);
    $this->setDowloadHeaders($parameters);
    $response->setHttpHeader('Content-Transfer-Encoding','binary');
    return $this->renderText($png_content);
  }

}
