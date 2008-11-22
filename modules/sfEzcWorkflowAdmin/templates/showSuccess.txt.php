/* You can upload this file on http://urlgreyhot.com/graphviz/index.php to get a nice graph */
/* or on unix run 'dot -Tpng -O filename.dot' */
<?php
  $visitor = new ezcWorkflowVisitorVisualization;
  $workflow->accept( $visitor );
  echo $visitor->__toString();