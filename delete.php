<?php

/**
 * delete.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * A script to delete a specified file from the music box.
 */

require('utilities.php');
$config = require('config.php');
$file = $config['usbPath'] . '/' . $_GET['file'];
checkPathSanity($file, $config) || die('Insane path!');
if (is_file($file)) unlink($file);
elseif (is_dir($file) && count(glob($file . '/*')) == 0) rmdir($file);
else die('Insane situation!');

header('Location: index.php?path=' . urlencode($_GET['path']) . '#fileManagement');

?>
