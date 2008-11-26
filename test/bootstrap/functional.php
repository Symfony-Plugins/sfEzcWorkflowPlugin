<?php

if (!isset($_SERVER['SYMFONY']))
{
  require_once('config/ProjectConfiguration.class.php');
  $configuration = new ProjectConfiguration();
  $_SERVER['SYMFONY'] = $configuration->getSymfonyLibDir();
  //throw new RuntimeException('Could not find symfony core libraries.');
}

if (!isset($app))
{
  $app = 'frontend';
}

require_once $_SERVER['SYMFONY'].'/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

require_once dirname(__FILE__).'/../fixtures/project/config/ProjectConfiguration.class.php';
$configuration = ProjectConfigurationTest::getApplicationConfiguration($app, 'test', isset($debug) ? $debug : true);
sfContext::createInstance($configuration);

// remove all cache
sfToolkit::clearDirectory(sfConfig::get('sf_app_cache_dir'));
