<ul class="sf_ezc_workflow_admin_states">
<?php
foreach ($sf_ezc_workflow_execution->getsfEzcWorkflowExecutionStatesJoinsfEzcWorkflowNode() as  $state)
{
  $conf = sfPropelEzcWorkflowUtil::unserialize($state->getsfEzcWorkflowNode()->getConfiguration());
  if (isset($conf['sf_action_uri']))//ignores ezcWorkflowNodeInputFromSf nodes
  {
    continue;
  }
  echo '<li>'.$conf['class'].' ['.var_export($conf['arguments'],true).'] = '.var_export($state->getState(),true).'</li>';
}
?>
</ul>