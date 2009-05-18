<?php if (isset($errors)): ?>

  <div id="errorExplanation" class="errorExplanation">
    <h2><?php echo count($errors) ?> prohibited this post from being saved</h2>
    <p>There were problems with the following fields:</p>
    <ul>
    <?php foreach ($errors as $field => $error): ?>
    <li><?php echo ucfirst($field) ?> <?php echo $error ?></li>
    <?php endforeach; ?>
    </ul>

  </div>

<?php endif; ?>
