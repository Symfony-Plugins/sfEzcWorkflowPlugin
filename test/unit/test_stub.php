<?php
class MyServiceObject implements ezcWorkflowServiceObject
{
    private $message;

    public function __construct( $message )
    {
        $this->message = $message;
    }

    public function execute( ezcWorkflowExecution $execution )
    {
        global $sf_ezc_testing_message ;
        $sf_ezc_testing_message = $this->message;
        return true;
    }

    public function __toString()
    {
        return "MyServiceObject, message {$this->message}";
    }
}
