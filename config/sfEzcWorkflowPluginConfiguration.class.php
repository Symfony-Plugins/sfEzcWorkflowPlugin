<?php

/**
 * sfEzcWorkflowPropelPlugin configuration.
 * 
 * @package     sfEzcWorkflowPropelPlugin
 * @subpackage  config
 * @author     Cinxgler Mariaca Minda <cinxgler at gmail.com>
 * @version     SVN: $Id$
 */
class sfEzcWorkflowPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    parent::initialize(); // load symfony autoloading first
    // Integrate eZ Components
    if ($sf_ez_lib_dir = sfConfig::get('app_ez_lib_dir')){
      set_include_path($sf_ez_lib_dir.PATH_SEPARATOR.get_include_path());
      require_once($sf_ez_lib_dir.'/Base/src/base.php');
      spl_autoload_register(array('ezcBase', 'autoload'));
    } else {
      require_once('ezc/Base/base.php');
      spl_autoload_register(array('ezcBase', 'autoload'));      
    }
    //Add routes for generated admin modules
    if (!is_null(sfConfig::get('sf_enabled_modules')))
    {
      foreach (array('sfEzcWorkflowExecutionAdmin','sfEzcWorkflowAdmin') as $module)
      {
        if (in_array($module, sfConfig::get('sf_enabled_modules')))
        {
          $this->dispatcher->connect('routing.load_configuration', array('sfEzcWorkflowRouting', 'addRouteFor'.str_replace('sfEzcWorkflow', '', $module)));
        }
      }
    }
  }
}
