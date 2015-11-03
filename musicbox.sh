#!/bin/sh

# musicbox.sh
# Copyright (c) 2015 by Alec Smecher
#
# Set up the music box processes:
# - Run mplayer in slave mode (listening for commands on a FIFO)
# - Run the musicbox program to translate RFIDs into playlist loads

cd /home/pi/musicbox
mkfifo /tmp/mplayercontrol
amixer set PCM 75%
mplayer -slave -idle -af pan=1:0.5:0.5 -input file=/tmp/mplayercontrol &
stty -F /dev/ttyAMA0 9600 raw
php daemon.php
