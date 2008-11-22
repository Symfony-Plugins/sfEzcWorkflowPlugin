<?php
  $definition = new ezcWorkflowDefinitionStorageXml( sfConfig::get('sf_upload_dir') );
  $xml_content = $definition->saveToDocument($workflow, $workflow->version)->saveXML();
  print $xml_content;
