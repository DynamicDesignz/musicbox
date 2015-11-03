#!/bin/sh

# Toggle administration mode.
# When in administration mode, mounts are read-write and wireless is up.
# Otherwise, everything is mounted read-only and wireless is off.

if [ -f /tmp/admin-mode ]; then
	# Enter administration mode
	mplayer /home/pi/musicbox/sounds/warning.wav
	mount / -o remount,ro
	mount /mnt/usb -o remount,ro
	ifdown wlan0
	mplayer /home/pi/musicbox/sounds/start.wav
	rm /tmp/admin-mode
else
	# Leave administration mode
	mplayer /home/pi/musicbox/sounds/warning.wav
	mount / -o remount,rw
	mount /mnt/usb -o remount,rw
	ifup wlan0
	# Say the current IP address, in case the administrator needs to know.
	ifconfig wlan0 | sed -n -e "s/.*addr:\([^ ]\+\).*/\1/p" | festival --tts
	touch /tmp/admin-mode
fi
