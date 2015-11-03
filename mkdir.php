<?php

/**
 * mkdir.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * A script to create a new directory in the music box.
 */

require('utilities.php');
$config = require('config.php');
$path = $config['usbPath'] . '/' . $_POST['path'];
checkPathSanity($path, $config) || die('Insane path!');
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['name'])) die('Insane pathname!');
mkdir($path . '/' . $_POST['name']);
header('Location: index.php?path=' . urlencode($_POST['path']) . '#fileManagement');

?>
