<?php

/**
 * upload.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * A script to upload a new file to the music box.
 */

require('utilities.php');
$config = require('config.php');
$path = $config['usbPath'] . '/' . $_POST['path'];
checkPathSanity($path, $config) || die('Insane path!');

switch ($_FILES['file']['error']) {
	case UPLOAD_ERR_OK:
		if (move_uploaded_file(
			$_FILES['file']['tmp_name'],
			$path . '/' . $_FILES['file']['name']
		)) {
			// Success
			header('Location: index.php?path=' . urlencode($_POST['path']) . '#fileManagement');
			exit();
		}
		$response = 'Could not move uploaded file.';
		break;
	case UPLOAD_ERR_INI_SIZE:
		$response = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
		break;
	case UPLOAD_ERR_FORM_SIZE:
		$response = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
		break;
	case UPLOAD_ERR_PARTIAL:
		$response = 'The uploaded file was only partially uploaded.';
		break;
	case UPLOAD_ERR_NO_FILE:
		$response = 'No file was uploaded.';
		break;
	case UPLOAD_ERR_NO_TMP_DIR:
		$response = 'Missing a temporary folder.';
		break;
	case UPLOAD_ERR_CANT_WRITE:
		$response = 'Failed to write file to disk.';
		break;
	case UPLOAD_ERR_EXTENSION:
		$response = 'File upload stopped by extension.';
		break;
	default:
		$response = 'Unknown error.';
		break;
}

die($response);

?>
