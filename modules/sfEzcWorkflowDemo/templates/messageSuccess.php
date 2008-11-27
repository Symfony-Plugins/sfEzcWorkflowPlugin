<?php
echo form_tag('sfEzcWorkflowDemo/message');
?>
  Message:
  <input type="hidden" name="sf_ezc_wf_execution_id" value="<?php echo $sf_ezc_wf_execution_id ?>" />
  <textarea name="message"></textarea>
  <input type="submit">
</form>
