<h2>Image Types</h2>

<?php require_once('helpers/Link.php') ?>

<table border="1" width="50%">
  <thead>
    <tr>
      <th>Name</th>
      <th>Width</th>
      <th>Height</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($results) > 0): ?>
      <?php foreach ($results as $result): ?>
        <tr>
          <td><?php echo $result->getName() ?></td>
          <td><?php echo $result->getWidth() ?></td>
          <td><?php echo $result->getHeight() ?></td>
          <td>
            <?php echo link_to_edit('imageType', $result) ?>
            <?php echo link_to_delete('imageType', $result) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr>
        <td colspan="4">No results</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<br />

<img src="public/images/add.png" align="absmiddle">&nbsp;<a href="?controller=imageType&action=new">New</a>

<br />
<br />

<img src="public/images/arrow_left.png" align="absmiddle">&nbsp;<a href="index.php">Back</a>
