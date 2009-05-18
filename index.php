<?php
  require_once('controllers/FrontController.php');
  $frontController = new FrontController();
  
  $notice_success = $frontController->getFlash('notice_success');
  $notice_error = $frontController->getFlash('notice_error');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt-br">
  <head>
    <title>Go2Images</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="index, follow"/>
    <meta name="author" content="Patrick Espake"/>
    <meta name="title" content="Go2Images"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <script type="text/javascript" src="public/javascripts/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="public/javascripts/thickbox-compressed.js"></script>
    <link rel="stylesheet" href="public/stylesheets/thickbox.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="public/stylesheets/application.css" type="text/css" media="all"/>
  </head>

  <body>
    <div id="content">
      <h1>Go2Images</h1>
      <img src="public/images/wrench.png" align="absmiddle" />&nbsp;<a href="?controller=imageType&action=list">Image Type</a>
      <br />
      <br />

      <?php if (!empty($notice_success)): ?>
        <div id="notice_success"><?php echo $notice_success ?></div>
      <?php endif; ?>

      <?php if (!empty($notice_error)): ?>
        <div id="notice_error"><?php echo $notice_error ?></div>
      <?php endif; ?>

      <?php $frontController->render() ?>
    </div>
  </body>
</html>
