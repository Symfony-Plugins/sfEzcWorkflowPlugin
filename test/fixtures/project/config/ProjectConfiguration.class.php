<?php

if (!isset($_SERVER['SYMFONY']))
{
  throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfigurationTest extends sfProjectConfiguration
{
  public function setup()
  {
    $this->setPlugins(array('sfEzcWorkflowPropelPlugin'));
    $this->setPluginPath('sfEzcWorkflowPropelPlugin', dirname(__FILE__).'/../../../..');
  }
}
