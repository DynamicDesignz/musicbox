<?php

/**
 * config.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * Configuration information for the music box.
 */

return array(
	'usbPath' => $usbPath = '/mnt/usb',
	'ttyDevice' => '/dev/ttyAMA0',
	'mplayerFifo' => '/tmp/mplayercontrol',
	'startSound' => 'sounds/start.wav',
	'unknownTagLog' => '/tmp/unknown-tags.txt',
	'unknownTagSound' => 'sounds/unknown-tag.wav',
);
