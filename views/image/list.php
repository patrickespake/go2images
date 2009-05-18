<h2>Images</h2>

<?php require_once('helpers/Link.php') ?>

<table border="1" width="100%">
  <thead>
    <tr>
      <th>File</th>
      <th>Title</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($results) > 0): ?>
      <?php foreach ($results as $result): ?>
        <tr>
        <td><img src="<?php echo $result->getMinimumSize()->getFileName() ?>" alt="<?php echo $result->getTitle() ?>" /></td>
          <td><?php echo $result->getTitle() ?></td>
          <td><?php echo $result->getDescription() ?></td>
          <td>
            <?php echo link_to_show('image', $result) ?>
            <?php echo link_to_edit('image', $result) ?>
            <?php echo link_to_delete('image', $result) ?>
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

<img src="public/images/add.png" align="absmiddle">&nbsp;<a href="?controller=image&action=new">New</a>
