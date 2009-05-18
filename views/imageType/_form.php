<fieldset>
  <legend>Informations</legend>

  <?php if ($imageType->newRecord()): ?>
  <form name="imageType_form" method="post" action="?controller=imageType&action=create">
  <?php else: ?>
  <form name="imageType_form" method="post" action="?controller=imageType&action=update">
    <input type="hidden" name="imageType[id]" id="imageType_id" value="<?php echo $imageType->getId() ?>" />
  <?php endif; ?>
    <label for="imageType_name">Name</label>
    <input type="text" name="imageType[name]" id="imageType_name" value="<?php echo $imageType->getName() ?>" />
    <label for="imageType_width">Width</label>
    <input type="text" name="imageType[width]" id="imageType_width" value="<?php echo $imageType->getWidth() ?>" />
    <label for="imageType_height">Height</label>
    <input type="text" name="imageType[height]" id="imageType_height" value="<?php echo $imageType->getHeight() ?>" />
    <br />
    <br />

    <?php if ($imageType->newRecord()): ?>
      <input type="submit" value="Create" />
    <?php else: ?>
      <input type="submit" value="Update" />
    <?php endif; ?>
  </form>
</fieldset>
