<?php

/**
 * edit.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * An editor for text files in the music box administration interface.
 */

$config = require('config.php');
require_once('utilities.php');
$filesPath = isset($_GET['path'])?$_GET['path']:$_POST['path'];
checkPathSanity($config['usbPath'] . '/' . $filesPath, $config) || die('Insane path!');

$rawFile = isset($_GET['file'])?$_GET['file']:$_POST['file'];
if (isset($_POST['relative'])) $rawFile = $filesPath . '/' . $rawFile;
$file = $config['usbPath'] . '/' . $rawFile;
$basename = basename($file);

if (!preg_match('/^[a-zA-Z0-9.]+$/', $basename) || in_array($basename, array('', '.', '..'))) die('Insane filename!');
checkPathSanity(dirname($file), $config) || die('Insane file path!');
if (file_exists($file) && !is_file($file)) die('Insane file type!');

// Are we writing the contents?
if (isset($_POST['contents'])) {
	file_put_contents($file, $_POST['contents']);
	header('Location: index.php?path=' . urlencode($_POST['path']) . '#fileManagement');
	exit();
}

?>
<html lang="en">
 <head>
  <title>Music Box: Editing <?php echo htmlspecialchars(basename($file)); ?></title>
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
    <h2>Editing <?php echo htmlspecialchars(basename($file)); ?></h2>
    <p>You may use this tool to create or edit several types of files, particularly playlists (.pls), shell scripts (.sh), and text scripts (.txt). Creating a file in the root directory, named for the RFID tag's ID, and with one of these suffixes, will cause the player to invoke the file according to its type.</p>
    <p><a class="btn btn-primary btn-lg" href="http://cassettepunk.com/small-projects/music-box/" role="button">More information &raquo;</a></p>
   </div><!-- container -->
  </div><!-- jumbotron -->

  <div class="container">
   <form class="well" action="edit.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="path" value="<?php echo htmlspecialchars($filesPath); ?>">
    <div class="form-group">
     <label for="file">Currently Editing:</label>
     <input type="text" name="file" size="60" value="<?php echo htmlspecialchars($rawFile); ?>">
    </div>
    <div class="form-group">
     <label for="contents">File Contents</label><br/>
     <textarea rows="20" cols="120" name="contents"><?php if (file_exists($file)) echo htmlspecialchars(file_get_contents($file)); ?></textarea>
    </div>
    <input type="submit" class="btn btn-lg btn-primary" value="Save">
   </form>
  </div>
 </body>
</html>
