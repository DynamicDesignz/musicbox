<?php

/**
 * utilities.php
 * Copyright (c) 2015 by Alec Smecher (credit as noted below)
 * See LICENSE.
 *
 * Utility functions.
 */

// By rommel at rommelsantor dot com; see http://php.net/manual/en/function.filesize.php
function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

/**
 * Check the "sanity" of a user-specified path based on the configuration.
 * @param $path string Path to sanitize, relative to a base path specified in the configuration.
 * @param $config array Configuration information for the music box.
 * @return boolean true iff success; false iff the path could not be sanitized.
 */
function checkPathSanity($path, $config) {
	$usbPath = realpath($config['usbPath']);
	$path = realpath($path);
	while ($path && $path != '/') {
		if ($path == $usbPath) return true;
		$path = dirname($path);
	}
	return false;
}

?>
