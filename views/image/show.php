<h2>Image Details</h2>

<p>
  <b>Title: </b> <?php echo $image->getTitle() ?>
</p>
<p>
  <b>Description: </b> <?php echo $image->getDescription() ?>
</p>
<p>
  <b>Image original: </b> <a href="<?php echo $image->getFileName() ?>" class="thickbox">Click here</a>
</p>

<table border="1" width="100%">
  <thead>
    <tr>
      <th>Type</th>
      <th>Size</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($image->getSizes() as $size): ?>
      <tr>
        <td><a href="<?php echo $size->getFileName() ?>" class="thickbox"><?php echo $size->getImageType()->getName() ?></a></td>
        <td><?php echo $size->getImageType()->getWidth() ?>x<?php echo $size->getImageType()->getHeight() ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<img src="public/images/arrow_left.png" />&nbsp;<a href="?controller=image&action=list">Back</a>
