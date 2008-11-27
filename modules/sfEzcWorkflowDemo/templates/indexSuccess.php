<h1>sfEzcWorkflow demonstration module</h1>

<?php echo link_to('start a demo workflow','sfEzcWorkflowDemo/start'); ?>

<h2>sfEzcWorkflow Components</h2>

<h3>Waiting Executions</h3>
<p>use:</p>
  <pre>
  include_component('sfEzcWorkflow', 'waitingExecutions')
  </pre>

<p>to get:</p>

  <?php include_component('sfEzcWorkflow', 'waitingExecutions') ?>