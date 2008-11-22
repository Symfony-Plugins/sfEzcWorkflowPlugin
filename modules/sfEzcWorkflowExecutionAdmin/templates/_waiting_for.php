<ul class="sf_ezc_workflow_admin_waiting_for">
<?php
//TODO: build a component instead of a partial?
foreach ($sf_ezc_workflow_execution->getWaitingFor() as  $var_name => $var_value)
{
  echo '<li>variable: '.$var_name.'<ul>';
  foreach($var_value as $key => $value)
  {
    if ($key === 'node')
    {
      $node = sfEzcWorkflowNodePeer::retrieveByPK($value);
      $value = $node ;
    }
    echo '<li>'.$key.' = '.$value.'</li>'; 
  }
  echo '</ul></li>';
}
?>
</ul>