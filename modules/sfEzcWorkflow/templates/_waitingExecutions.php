<?php use_helper('I18N') ?>
<table class="sf_ezc_workflow_component_user_waiting_executions">
  <thead>
    <tr>
      <th><?php echo __('Message') ?></th>
      <th><?php echo __('Actions') ?></th>
    </tr>
  </thead>
<?php foreach($executions as $execution): ?>
    <tr>
      <td>
        <?php echo __('Workflow \'%1%\' is waiting for your input',array('%1%'=>$execution->workflow->name),'sfEzcWorkflow') ?>
      </td>
      <td>
        <ul class="sf_ezc_workflow_component_user_waiting_executions_td_actions">
          <li class="sf_ezc_workflow_component_user_waiting_executions_action_resume">
            <?php echo link_to(__('Resume'), 'sfEzcWorkflowExecutionAdmin/Resume?id='.$execution->getId(), array(), 'messages'); ?>
          </li>
          <li class="sf_ezc_workflow_component_user_waiting_executions_action_cancel">
            <?php echo link_to(__('Cancel'), 'sfEzcWorkflowExecutionAdmin/Cancel?id='.$execution->getId(), 'confirm=Are you sure you want to cancel the workflow instance?', 'messages') ?>
          </li>
        </ul>
      </td>

    </tr>
<?php endforeach ?>
</table>