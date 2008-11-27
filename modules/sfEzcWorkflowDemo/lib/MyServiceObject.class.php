<?php
/**
* Test class
*/

class MyServiceObject implements ezcWorkflowServiceObject
{
    private $message;

    public function __construct( $message )
    {
      $this->message = $message;
    }

    public function execute( ezcWorkflowExecution $execution )
    {
      //do whatever you need to do here ... ie. send an email
      mail('root@localhost', 'Automatic email', $this->message);
      // Manipulate the workflow.
      // Does not affect the workflow, for illustration only. 
      $execution->setVariable( 'email_sent', true );
      return true;
    }

    public function __toString()
    {
      return "MyServiceObject, message {$this->message}";
    }
}