<?php
echo form_tag('sfEzcWorkflowDemo/choice');
?>
  Choice:
  <input type="hidden" name="sf_ezc_wf_execution_id" value="<?php echo $sf_ezc_wf_execution_id ?>" />
  <select name="choice">
    <option value="true">True</option>
    <option value="false">False</option>
  </select>
  <input type="submit">
</form>
