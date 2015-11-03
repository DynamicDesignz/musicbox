<?php

/**
 * daemon.php
 * Copyright (c) 2015 by Alec Smecher
 * See LICENSE.
 *
 * A PHP "daemon" that reads packets from an ID-12LA RFID chip via the
 * Raspberry Pi serial interface, and translates them into playlist, .wav/.mp3,
 * text file mplayer script, or shell script commands, potentially invoking
 * these via mplayer running in "slave" mode.
 */

$config = require('config.php');
$usbPath = $config['usbPath'];

($tty = fopen($config['ttyDevice'], 'r')) || die('An error occurred opening the serial port.');
($mplayer = fopen($config['mplayerFifo'], 'w')) || die('An error occurred opening the mplayer FIFO.');

// Play the start sound.
fwrite($mplayer, "loadfile $config[startSound]\n");

while (true) {
	// Read a code from the serial port (anything ending in a newline).
	$code = substr(trim(fgets($tty)), -13);

	// Ensure that it begins with a 2 (start code).
	if (bin2hex($code[0]) != '02') continue;

	// Discard the start code we just checked and test the length.
	$code = substr($code, 1);
	if (strlen($code) != 12) continue;

	switch (true) {
		// Does a .pls playlist exist with the tag ID? Play it.
		case file_exists($fn = "$usbPath/$code.pls"):
			echo "Loading playlist \"$fn\".\n";
			fwrite($mplayer, "loadlist $fn\n");
			break;
		// Does an .mp3 or .wav file exist with the tag ID? Play it.
		case file_exists($fn = "$usbPath/$code.mp3") || file_exists($fn = "$usbPath/$code.wav"): // MP3/WAV
			echo "Playing file \"$fn\".\n";
			fwrite($mplayer, "loadfile $fn\n");
			break;
		// Does a .txt file exist with the tag ID? (For mplayer remote mode commands)
		case file_exists($fn = "$usbPath/$code.txt"): // Text file
			$command = file_get_contents($fn);
			echo "Sending command to mplayer:\n$command\n";
			fwrite($mplayer, $command);
			break;
		// Does a .sh file exist with the tag ID? Execute it.
		case file_exists($fn = "$usbPath/$code.sh"):
			echo "Invoking shell script \"$fn\".\n";
			system("sudo sh $fn");
			break;
		// Otherwise, it's an unknown tag. Log it and boop at the user.
		default:
			echo "Logging unknown tag \"$code\".";
			fwrite($mplayer, "loadfile $config[unknownTagSound]\n");
			($log = fopen($config['unknownTagLog'], 'a')) || die('An error occurred opening the unknown tag log.');
			fwrite($log, $code . "\n");
			fclose($log);
	}
}
