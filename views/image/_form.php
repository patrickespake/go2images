<fieldset>
  <legend>Informations</legend>

  <?php if ($image->newRecord()): ?>
    <form name="image_form" method="post" action="?controller=image&action=create" enctype="multipart/form-data">
  <?php else: ?>
  <form name="image_form" method="post" action="?controller=image&action=update" enctype="multipart/form-data">
    <input type="hidden" name="image[id]" id="image_id" value="<?php echo $image->getId() ?>" />
  <?php endif; ?>
  <label for="image_title">Title</label>
  <input type="text" name="image[title]" id="image_title" value="<?php echo $image->getTitle() ?>" />
  <label for="image_description">Description</label>
  <textarea name="image[description]" id="image_description" rows="10" cols="80"><?php echo $image->getDescription() ?></textarea>
  <label for="image_file">File</label>
  <input type="file" name="image[file]" id="image_file" />
  <br />
  <br />

  <?php if ($image->newRecord()): ?>
    <input type="submit" value="Create" />
  <?php else: ?>
    <input type="submit" value="Update" />
  <?php endif; ?>
  </form>
</fieldset>
