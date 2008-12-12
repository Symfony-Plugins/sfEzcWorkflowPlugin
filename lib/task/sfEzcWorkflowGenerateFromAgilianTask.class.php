<?php

/**
 * Transform an agilian diagram into a ezcWorkflow xml definition
 *
 * @package    sfEzcWorkflow
 * @subpackage task
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version    SVN: $Id
 */
class sfEzcWorkflowGenerateFromAgilianTask extends sfPropelBaseTask
{  
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('agilian_xml_file', sfCommandArgument::REQUIRED, 'Agilian project.xml file'),
    ));

    $this->addOptions(array(
      new sfCommandOption('output-dir', null, sfCommandOption::PARAMETER_REQUIRED, 'destination folder where the xml definition will be stored', 'data/workflow/'),
    ));

    $this->namespace = 'workflow';
    $this->name = 'generate-from-agilian';
    $this->briefDescription = 'Generate a ezcWorkflow definition (xml format) from a Business Process Diagram made on agilian (xml exported)';

    $this->detailedDescription = <<<EOF
The [sfEzcWorkflow:generate-from-agilian|INFO] task takes a Business Process Diagram generated with agilian v1.2 and exported as a XML file and transform it to a ezcWorkflow definition in xml format:

  [./symfony sfEzcWorkflow:generate-from-agilian /path/to/agilian/exported/project/project.xml|INFO]

The result is stored on [data/workflow|INFO] by default.
EOF;
  }

  /**
   * parse xml document with SimpleXML, get the tasks, properties and connections, create an ezcWorkflow and store it as XML
   * 
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $document = simplexml_load_file($arguments['agilian_xml_file']);
    $this->log("Workflow name: ".$document->Diagrams->Diagram['name']."\n");
    $nodes = $this->getEzcNodes($document);
    
    //CONNECT NODES
    foreach ($nodes as $id=>$node)
    {
        $element = $this->getNodeById($document,$id);
        $this->log("Connecting $id (".$element['name'].") ");
        if (!$node instanceof ezcWorkflowNodeStart) // Set In Node except for Start nodes
        {
            foreach ($element->ToSimpleRelationships->RelationshipRef as $relationship )
            {
                if (!is_null($nodes[(string)$relationship['from']]))
                {
                    $tmp = $this->getNodeById($document,(string)$relationship['from']);
                    $this->log("\t comes from ".$relationship['from']." (".$tmp['name'].")");
                    $node->addInNode($nodes[(string)$relationship['from']]);
                }
            }
        }
        if (!$node instanceof ezcWorkflowNodeEnd) // Set Out Node except for End nodes
        {
            foreach ($element->FromSimpleRelationships->RelationshipRef as $relationship )
            {
                if (!is_null($nodes[(string)$relationship['to']]))
                {
                    $tmp = $this->getNodeById($document,(string)$relationship['to']);
                    $this->log("\tconnecting to ".$relationship['to']." (".$tmp['name'].")");
                    if ($node instanceof ezcWorkflowNodeLoop)
                    {
                        $conditional = $this->getConditionalNodeFor((string)$relationship['id'],$document);
                        //$this->log("\tconditional: ".var_export($conditional));
                        $node->addConditionalOutNode($conditional, $nodes[(string)$relationship['to']]);
                    }else
                    {
                        $node->addOutNode($nodes[(string)$relationship['to']]);
                    }
                }
            }
        }
    }
    //CREATE WORKFLOW
    $workflow = new ezcWorkflow( (string)$document->Diagrams->Diagram['name'], $this->startNode, $this->defaultEndNode, $this->finallyNode );
    @mkdir($options['output-dir'],0755,true);
    $definition = new ezcWorkflowDefinitionStorageXml( $options['output-dir'].'/' );
    // Save workflow definition to database.
    $definition->save( $workflow );
    $workflow->verify();
    
    $this->log("nodes:".sizeof($nodes)."\n");
  }
  
  /**
   * Parse the XML document and via xpath find the objects and create the corresponding ezcWorkflowNodes
   * @param SimpleXMLElement $document root element from agilian xml project
   * @return array array of ezcWorkflowNodes
   */
  protected function getEzcNodes(SimpleXMLElement $document)
  {
    $nodes = array();
    
    //Create nodes for BPServiceTask
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPServiceTask']") as $task) 
    {
        $parent = $this->getTaskFromTaskType($task);
        $arguments = eval(html_entity_decode($this->getCustomProperty($task,'arguments')));
        $classname = $this->getCustomProperty($task,'class');
        //var_export($arguments);
        switch($classname)
        {
            case 'ezcWorkflowNodeVariableSet':
                if (!is_array($arguments))
                {
                    $this->log("\nError: 'arguments' parameter received for '".$parent['name']."' is not an array, you need to set up an array with the list of variables to set and the values.");
                    var_dump($arguments);
                    exit(1);
                }
                if (is_array($arguments[0]))
                {
                    $this->log("\nError: 'arguments' parameter received for '".$parent['name']."' is an array of arrays, you need to set up just an array with the list to variables to set and the values.");
                    var_dump($arguments);
                    exit(1);
                }
                $options = $arguments;
                $nodes[(string)$parent['id']] = new ezcWorkflowNodeVariableSet($options);
            break;
            case 'ezcWorkflowNodeVariableUnset':
                if (is_array($arguments) && is_array($arguments[0]))
                {
                    $this->log("\nError: 'arguments' parameter received for '".$parent['name']."' is an array of arrays, you need to set up just an array or a string with the list of variables to unset.");
                    var_dump($arguments);
                    exit(1);
                }
                $options = $arguments;
                $nodes[(string)$parent['id']] = new ezcWorkflowNodeVariableUnset($options);
            break;
            default:
                $options=array('class'=>$classname,'arguments'=>$arguments);
                $nodes[(string)$parent['id']] = new ezcWorkflowNodeAction($options);
            break;
        }
    }
    
    //create nodes for BPUserTask
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPUserTask']") as $task) 
    {
        $options=array('sf_action_uri'=>$this->getCustomProperty($task,'sf_action_uri'),
                       'sf_is_secure'=>$this->getCustomProperty($task,'sf_is_secure'),
                       'sf_credential'=>$this->getCustomProperty($task,'sf_credential')
                       );
        $variables=$this->getInputVariables($task);
        $parent = $this->getTaskFromTaskType($task);
        $arguments = array_merge($variables,$options);
        $nodes[(string)$parent['id']] = new ezcWorkflowNodeInputFromSf($arguments);//new ezcWorkflowNodeInputFromSf
    }
    
    //create nodes for BPScriptTask
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPScriptTask']") as $task) 
    {
        $options=array('class'=>getCustomProperty($task,'class'),'arguments'=>$this->getCustomProperty($task,'arguments'));
        $parent = $this->getTaskFromTaskType($task);
        $nodes[(string)$parent['id']] = new ezcWorkflowNodeAction($options);//new ezcWorkflowNodeAction
    }
    
    //create nodes for BPSendTask
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPSendTask']") as $task) 
    {
        $options=array('class'=>$this->getCustomProperty($task,'class'),'arguments'=>$this->getCustomProperty($task,'arguments'));
        $parent = $this->getTaskFromTaskType($task);
        $nodes[(string)$parent['id']] = new ezcWorkflowNodeAction($options);//new ezcWorkflowNodeAction
    }
    
    //create nodes for BPManualTask
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPManualTask']") as $task) 
    {
        $options=array('sf_action_uri'=>$this->getCustomProperty($task,'sf_action_uri'),
                       'sf_is_secure'=>$this->getCustomProperty($task,'sf_is_secure'),
                       'sf_credential'=>$this->getCustomProperty($task,'sf_credential')
                       );
        $parent = $this->getTaskFromTaskType($task);
        $nodes[(string)$parent['id']] = new ezcWorkflowNodeInputFromSf($options);//new ezcWorkflowNodeInputFromSf
    }
    
    
    //create nodes for BPGatewayDataBasedXOR
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPGatewayDataBasedXOR']") as $conditional) 
    {
        $parent = $this->getTaskFromTaskType($conditional);
        if (countInputNodes($parent) == 1)
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeExclusiveChoice($options);//new ezcWorkflowNodeExclusiveChoice
        }
        else
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeSimpleMerge($options);//new ezcWorkflowNodeSimpleMerge
        }
    }
    
    //create nodes for BPGatewayDataBasedOR
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPGatewayOR']") as $conditional) 
    {
        $parent = $this->getTaskFromTaskType($conditional);
        if ($this->countInputNodes($parent) == 1)
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeMultiChoice($options);//new ezcWorkflowNodeMultiChoice
        }
        else
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeDiscriminator($options);//new ezcWorkflowNodeDiscriminator
        }
    }
    
    //create nodes for BPGatewayDataBasedAND
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPGatewayAND']") as $conditional) 
    {
        $parent = $this->getTaskFromTaskType($conditional);
        if ($this->countInputNodes($parent) == 1)
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeParallelSplit($options);//new ezcWorkflowNodeParallelSplit
        }
        else
        {
            $nodes[(string)$parent['id']] = new ezcWorkflowNodeSynchronization($options);//new ezcWorkflowNodeSynchronization
        }
    }
    
    //BPGatewayComplex (used for loops)
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model/ModelProperties/ModelProperty/Model[@modelType='BPGatewayComplex']") as $conditional) 
    {
        $parent = $this->getTaskFromTaskType($conditional);
        $nodes[(string)$parent['id']] = new ezcWorkflowNodeLoop();//$options;//new ezcWorkflowNodeLoop
        //If 2 or more input it's a merge
    }
    
    //BPIntermediateEvent -> finally
    $this->finallyNode = null;
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model[@modelType='BPIntermediateEvent']") as $finally_node) 
    {
        $nodes[(string)$finally_node['id']] = new ezcWorkflowNodeFinally();//$options;//new ezcWorkflowNodeFinally
        $this->finallyNode = $nodes[(string)$finally_node['id']];
        break;//Just one start node is allowed
    }
    
    //BPStartEvent
    $this->startNode = null;
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model[@modelType='BPStartEvent']") as $start_node) 
    {
        $nodes[(string)$start_node['id']] = new ezcWorkflowNodeStart();//$options;//new ezcWorkflowNodeStart
        $this->startNode = $nodes[(string)$start_node['id']];
        break;//Just one start node is allowed
    }
    
    //BPEndEvent
    $this->defaultEndNode = null;
    foreach ($document->xpath("/Project/Models/Model/ChildModels/Model[@modelType='BPEndEvent']") as $end_node) 
    {
        $nodes[(string)$end_node['id']] = new ezcWorkflowNodeEnd();//$options;//new ezcWorkflowNodeEnd
        if ($defaultEndNode == null)
        {
            $this->defaultEndNode = $nodes[(string)$end_node['id']];
        }
    }
    return $nodes;
  }

  /**
   * Return a task node by ID
   */
  function getNodeById($document,$id)
  {
      $result = $document->xpath("/Project/Models/Model/ChildModels/Model[@id='$id']");
      return $result[0];
  }
  
  /**
  * Return a simple ezcWorkflowConditional from a agilian sequence flow that follows this format 'something condition something', ie. foo == bar
  */
  function getConditionalNodeFor($id,$document)
  {
      $result = $document->xpath("/Project/Models/Model[@id='$id']");
      $conditional = explode(' ',(string)$result[0]['name']);
      switch (strtolower($conditional[1]))
      {
          case '==':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsEqual( $conditional[2] ));
          break;
          case '>=':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsEqualOrGreaterThan( $conditional[2] ));
          break;
          case '<=':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsEqualOrLessThan( $conditional[2] ));
          break;
          case '<':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsLessThan( $conditional[2] ));
          break;
          case '>':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsGreaterThan( $conditional[2] ));
          break;
          case '!=':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsNotEqual( $conditional[2] ));
          break;
          case '<>':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsNotEqual( $conditional[2] ));
          break;
          case 'istrue':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsTrue());
          break;
          case 'isfalse':
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsFalse());
          break;
          default:
              return new ezcWorkflowConditionVariable($conditional[0],new ezcWorkflowConditionIsAnything());
          break;
      }
  }
  /**
  * returns a custom property for a task type
  */
  function getCustomProperty(SimpleXMLElement $element,$property_name)
  {
       foreach($element->xpath("../../ModelsProperty/Model[@name='$property_name']") as $property)
       {
          foreach($property->ModelProperties->xpath("StringProperty[@name='type']") as $value)
          {
              return (string)$value['value'];
          }
       }
  }
  
  /**
  * returns an array of custom properties for a task type
  */
  function getInputVariables(SimpleXMLElement $element)
  {
      $result = array();
       foreach($element->xpath("../../ModelsProperty/Model[@name='variable']") as $property)
       {
          //var_export($property);
          foreach($property->ModelProperties->xpath("StringProperty[@name='type']") as $value)
          {
              //var_export($value);
              $definition = explode(':',(string)$value['value']); //variablename:type
              switch(strtolower($definition[1]))
              {
                  case 'string':
                      $className='ezcWorkflowConditionIsString';
                  break;
                  case 'bool':
                  case 'boolean':
                      $className='ezcWorkflowConditionIsBool';
                  break;
                  case 'object':
                      $className='ezcWorkflowConditionIsObject';
                  break;
                  case 'float':
                      $className='ezcWorkflowConditionIsFloat';
                  break;
                  case 'array':
                      $className='ezcWorkflowConditionIsArray';
                  break;
                  case 'integer':
                      $className='ezcWorkflowConditionIsInteger';
                  break;
                  default:
                      $className='ezcWorkflowConditionIsAnything';
                  break;
              }
              $result[$definition[0]]=new $className;
              $this->log($definition[0].'('.$definition[1].')'.' => '.$className."\n");
          }
       }
       return $result;
  }
  
  /**
  * return the task element from a task subtype
  */
  function getTaskFromTaskType(SimpleXMLElement $element)
  {
       foreach($element->xpath("../../..") as $parent)
       {
          return $parent;
       }
  }
  
  /**
  * return the number of input-from relationships per Model node
  */
  function countInputNodes(SimpleXMLElement $element)
  {
       return sizeof ($element->xpath("ToSimpleRelationships/RelationshipRef"));
  }

}