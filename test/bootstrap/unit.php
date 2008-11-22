<?php

if (!isset($_SERVER['SYMFONY']))
{
  require_once('config/ProjectConfiguration.class.php');
  $configuration = new ProjectConfiguration();
  $_SERVER['SYMFONY'] = $configuration->getSymfonyLibDir();
  //throw new RuntimeException('Could not find symfony core libraries.');
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
$configuration = new sfProjectConfiguration(getcwd());
require_once $configuration->getSymfonyLibDir().'/vendor/lime/lime.php';

require_once dirname(__FILE__).'/../../config/sfEzcWorkflowPluginConfiguration.class.php';
$plugin_configuration = new sfEzcWorkflowPluginConfiguration($configuration, dirname(__FILE__).'/../..');
new sfDatabaseManager(ProjectConfiguration::getApplicationConfiguration('frontend', 'test', true));
