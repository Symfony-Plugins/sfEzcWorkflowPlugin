<?php use_helper('I18N', 'Date') ?>
<?php include_partial('sfEzcWorkflowExecutionAdmin/assets') ?>

<div id="sf_admin_container">
  <h1><?php echo __('Workflow execution details', array(), 'messages') ?></h1>

  <?php include_partial('sfEzcWorkflowExecutionAdmin/flashes') ?>

  <div id="sf_admin_header">
    <?php include_partial('sfEzcWorkflowExecutionAdmin/show_header', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_content">
    <?php include_partial('sfEzcWorkflowExecutionAdmin/show', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
  </div>

  <div id="sf_admin_footer">
    <?php include_partial('sfEzcWorkflowExecutionAdmin/show_footer', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
  </div>
</div>
