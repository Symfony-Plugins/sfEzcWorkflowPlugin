<?php

/**
 * Input Node used to suspend the ezcWorkflow and make it to wait for a value
 * get from a sf action
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Cinxgler Mariaca <cinxgler at gmail.com>
 * @version    SVN: $Id$
 */

class ezcWorkflowNodeInputFromSf extends ezcWorkflowNodeInput {
  protected $action_uri = null;
  protected $is_secure = true;
  protected $credential = null;
  
  /**
  * This node besides ezcWorkflowNodeInput parameters receives an symfony
  * module/action uri and a auth credential.
  *
  * 
  * @param Array $configuration $configuration['sf_action_uri'] = (string)'module/action'
  *                             $configuration['sf_is_secure'] = (boolean) true a credential is required for this node
  *                             $configuration['sf_credential'] = (string) auth credential
  */
  
  
  function __construct( $configuration = array())
  {
    $this->action_uri=$configuration['sf_action_uri'];
    unset($configuration['sf_action_uri']);
    
    $this->is_secure=$configuration['sf_is_secure'];
    unset($configuration['sf_is_secure']);
    
    $this->credential=$configuration['sf_credential'];
    unset($configuration['sf_credential']);
    parent::__construct($configuration);
  }

  /**
   * Return the module/action that should be triggered when this node is reached
   */
  public function getActionUri()
  {
    return $this->action_uri;
  }
  
  /**
   * Return true when the node can be executed for users with an specific
   * credential (see getCredential())
   */
  public function isSecure()
  {
    return $this->is_secure;
  }
  
  /**
   * return the credential which should be presented by the user for set
   * the variable to resume the workflow
   */
  public function getCredential(){
    return $this->credential;
  }

  /**
   * Generate node configuration from XML representation.
   *
   * @param DOMElement $element 
   * @return array 
   */
  public static function configurationFromXML( DOMElement $element )
  {
    $configuration = array();
    $configuration = parent::configurationFromXML($element);

    foreach ( $element->getElementsByTagName( 'sf_variable' ) as $variable )
    {
      $configuration['sf_'.$variable->getAttribute( 'name' )] = $variable->getAttribute( 'value' );
    }
    
    return $configuration;
  }
  
  /**
   * Generate XML representation of this node's configuration.
   *
   * @param DOMElement $element 
   */
  public function configurationToXML( DOMElement $element )
  {
    parent::configurationToXML($element);
    //action_uri
    $xmlVariable = $element->appendChild(
      $element->ownerDocument->createElement('sf_variable') 
    );
    $xmlVariable->setAttribute('name', 'action_uri');
    $xmlVariable->setAttribute('value', $this->action_uri);

    //is_secure
    $xmlVariable = $element->appendChild(
      $element->ownerDocument->createElement('sf_variable') 
    );
    $xmlVariable->setAttribute('name', 'is_secure');
    $xmlVariable->setAttribute('value', $this->is_secure);
    
    //credential
    $xmlVariable = $element->appendChild(
      $element->ownerDocument->createElement('sf_variable') 
    );
    $xmlVariable->setAttribute('name', 'credential');
    $xmlVariable->setAttribute('value', $this->credential);

  }
  
  /**
   * return the configuration array with sf_variables included
   */
  public function getConfiguration()
  {
    $configuration = array();
    $configuration['sf_action_uri']=$this->action_uri;
    $configuration['sf_is_secure']=$this->is_secure;
    $configuration['sf_credential']=$this->credential;
    return array_merge($configuration,$this->configuration);
  }

}
