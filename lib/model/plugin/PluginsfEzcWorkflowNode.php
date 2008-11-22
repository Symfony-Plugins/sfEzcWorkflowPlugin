<?php

class PluginsfEzcWorkflowNode extends BasesfEzcWorkflowNode
{
  public function __toString()
  {
    return $this->getClassname();
  }
}
