<h2>Create a new workflow from XML definition</h2>
<form action="<?php echo url_for('@sf_ezc_workflow_new') ?>" method="POST" enctype="multipart/form-data">
  <table>
    <?php echo new sfEzcWorkflowFromXmlForm() ?>
    <tr>
      <td colspan="2">
        <input type="submit" value="<?php echo __('Create workflow from xml definition')?>" />
      </td>
    </tr>
  </table>
</form>
