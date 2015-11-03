<?php

/**
 * index.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * The homepage for the music box administration tool.
 */

$config = require('config.php');
require_once('utilities.php');
$filesPath = isset($_GET['path'])?$_GET['path']:'/';
checkPathSanity($config['usbPath'] . '/' . $filesPath, $config) || die('Insane path!');

?>
<html lang="en">
 <head>
  <title>Music Box</title>
  <!-- meta http-equiv="refresh" content="5" -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css" integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
 </head>
 <body>
  <div class="jumbotron">
   <div class="container">
    <h1>Music Box</h1>
    <p>Welcome to Music Box administration. You can use this tool to control the actions performed by swiping RFID tags past the reader, and to upload music to the music box.</p>
    <p><a class="btn btn-primary btn-lg" href="http://cassettepunk.com/small-projects/music-box/" role="button">More information &raquo;</a></p>
   </div><!-- container -->
  </div><!-- jumbotron -->

  <div class="container">
   <h2 class="sub-header" id="fileManagement">File Management</h2>
   <h3>
    <?php
    $pathList = array();
    foreach (explode('/', $filesPath) as $i => $pathComponent) {
     if ($i == 0) {
      echo '<a href="index.php#fileManagement">Root</a>';
      continue;
     } elseif ($pathComponent) {
      $pathList[] = $pathComponent;
      ?>
       / <a href="?path=<?php echo urlencode('/' . implode('/', $pathList)); ?>#fileManagement"><?php echo htmlspecialchars($pathComponent); ?></a>
      <?php
     }
    }
    ?>
   </h3>
   <div class="table-responsive">
    <table class="table table-striped">
     <thead>
      <tr>
       <th>Filename</th>
       <th>Size</th>
       <th>Actions</th>
      </tr>
     </thead>
     <tbody>
      <?php foreach (glob($config['usbPath'] . $filesPath . '/*') as $path) {
       $relativePath = str_replace($config['usbPath'] . '/', '', $path);
       ?>
       <tr>
        <td>
         <?php if (is_file($path)) { ?>
          <span class="glyphicon <?php if (in_array(substr($path, strrpos($path, '.')+1), array('pls', 'wav', 'mp3'))) echo "glyphicon-music"; else echo "glyphicon-file"; ?>" aria-hidden="true"></span>&nbsp;&nbsp;<?php echo htmlspecialchars(basename($path)); ?>
         <?php } elseif (is_dir($path)) { ?>
          <a href="?path=<?php echo urlencode($relativePath); ?>#fileManagement"><span class="glyphicon glyphicon-folder-close" aria-hidden="true"></span>&nbsp;&nbsp;<?php echo htmlspecialchars(basename($path)); ?>
         <?php } ?>
        </td>
        <td><?php echo human_filesize(filesize($path)); ?></td>
        <td>
         <?php if (is_file($path)) { ?>
          <?php if (in_array(substr($path, strrpos($path, '.')+1), array('txt', 'sh', 'pls'))) { ?>
           <a title="Edit" href="edit.php?file=<?php echo urlencode($relativePath); ?>&path=<?php echo urlencode($filesPath); ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
          <?php } ?>
          <a title="Delete" onclick="return confirm('Are you sure you wish to delete this file?');" href="delete.php?file=<?php echo urlencode($relativePath); ?>&path=<?php echo urlencode($filesPath); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
         <?php } elseif (is_dir($path) && count(glob($path . '/*')) == 0) { ?>
          <a title="Delete" onclick="return confirm('Are you sure you wish to delete this directory?');" href="delete.php?file=<?php echo urlencode($relativePath); ?>&path=<?php echo urlencode($filesPath); ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
         <?php } ?>
        </td>
       </tr>
      <?php } ?>
     </tbody>
    </table>
   </div>
   <div class="row">
    <div class="col-md-4">
     <form class="well" action="upload.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="path" value="<?php echo htmlspecialchars($filesPath); ?>">
      <div class="form-group">
       <label for="file">Select a file to upload</label>
       <input type="file" name="file">
       <p class="help-block">Uploads with a maximum size of <?php echo ini_get("upload_max_filesize"); ?> are allowed.</p>
      </div>
      <input type="submit" class="btn btn-lg btn-primary" value="Upload">
     </form>
    </div><!-- col-md-4 -->
    <div class="col-md-4">
     <form class="well" action="mkdir.php" method="post">
      <input type="hidden" name="path" value="<?php echo htmlspecialchars($filesPath); ?>">
      <div class="form-group">
       <label for="name">Create a directory</label><br/>
       <input type="text" name="name">
       <p class="help-block">Some special characters are not allowed in pathnames.</p>
      </div>
      <input type="submit" class="btn btn-lg btn-primary" value="Go">
     </form>
    </div><!-- col-md-4 -->
    <div class="col-md-4">
     <form class="well" action="edit.php" method="post">
      <input type="hidden" name="path" value="<?php echo htmlspecialchars($filesPath); ?>">
      <input type="hidden" name="relative" value="1">
      <div class="form-group">
       <label for="name">Create a file</label><br/>
       <input type="text" name="file">
       <p class="help-block">Some special characters are not allowed in filenames.</p>
      </div>
      <input type="submit" class="btn btn-lg btn-primary" value="Go">
     </form>
    </div><!-- col-md-4 -->
   </div><!-- row -->

   <h2 class="sub-header" id="unknownTags">Unknown Tags</h2>
   <?php if (file_exists($config['unknownTagLog'])) {
   	echo "<ul>";
   	foreach (file($config['unknownTagLog'], FILE_IGNORE_NEW_LINES) as $tag) {
   		echo "<li>$tag</li>\n";
   	}
   	echo "</ul>";
   } else {
   	echo "<p>No known tags detected yet.</p>\n";
   } ?>
   <a href="index.php?refresher=<?php echo uniqid(); ?>#unknownTags">Refresh</a><br/>
   <hr>
   <footer>
    <p>&copy; 2015 by Alec Smecher</p>
   </footer>
  </div><!-- container -->
 </body>
</html>
