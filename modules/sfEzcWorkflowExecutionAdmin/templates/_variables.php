<ul class="sf_ezc_workflow_admin_variables">
<?php
foreach ($sf_ezc_workflow_execution->getVariables() as  $key => $value)
{
  echo '<li>'.$key.' = '.var_export($value,true).'</li>';
}
?>
</ul>