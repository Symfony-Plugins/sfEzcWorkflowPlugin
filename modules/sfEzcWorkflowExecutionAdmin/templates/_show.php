<table>
  <tbody>
    <tr>
      <th>
        Workflow definition
      </th>
      <td>
        <?php echo $sf_ezc_workflow_execution->getsfEzcWorkflow(); ?>
      </td>
    </tr>
    <tr>
      <th>
        Parent
      </th>
      <td>
        <?php echo $sf_ezc_workflow_execution->getParent(); ?>
      </td>
    </tr>
    <tr>
      <th>
        Started
      </th>
      <td>
        <?php include_partial('sfEzcWorkflowExecutionAdmin/started', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
      </td>
    </tr>
    <tr>
      <th>
        Variables
      </th>
      <td>
        <?php include_partial('sfEzcWorkflowExecutionAdmin/variables', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
      </td>
    </tr>
    <tr>
      <th>
        Status
      </th>
      <td>
        <?php include_partial('sfEzcWorkflowExecutionAdmin/status', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
      </td>
    </tr>
    <tr>
      <th>
        Waiting for
      </th>
      <td>
        <?php include_partial('sfEzcWorkflowExecutionAdmin/waiting_for', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
      </td>
    </tr>
    <tr>
      <th>
        Actions
      </th>
      <?php include_partial('sfEzcWorkflowExecutionAdmin/list_td_actions', array('sf_ezc_workflow_execution' => $sf_ezc_workflow_execution, 'configuration' => $configuration)) ?>
    </tr>
  </tbody>
</table>