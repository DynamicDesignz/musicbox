/**
 * musicbox.cpp
 * Copyright (c) 2015 by Alec Smecher
 *
 * A quick-and-dirty program that reads packets from an ID-12LA RFID chip via
 * the Raspberry Pi serial interface, and translates them into playlist loads
 * for mplayer running in "slave" mode.
 */

#include <stdio.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/ioctl.h>
#include <string.h>

const unsigned char RFID_START=0x02;
const unsigned char RFID_END=0x03;

const char *TTY_DEVICE="/dev/ttyAMA0";
const char *FIFO_FILE="/tmp/mplayercontrol";

typedef struct {
	unsigned char start;
	char data[10];
	char checksum[2];
	char terminator[2];
	unsigned char end;
} rfid_buf;

int main(int argc, char **argv) {
	int ttyfd = open(TTY_DEVICE, O_RDONLY | O_NOCTTY);
	if (ttyfd==-1) {
		fprintf(stderr, "An error occurred opening the serial port \"%s\".\n", TTY_DEVICE);
		return -1;
	}

	int mplayerfd = open(FIFO_FILE, O_WRONLY);
	if (mplayerfd==-1) {
		fprintf(stderr, "An error occurred opening the mplayer remote via FIFO \"%s\".\n", FIFO_FILE);
		close(ttyfd);
		return -2;
	}

	rfid_buf buf;
	buf.start=RFID_START;
	buf.end=RFID_END;

	int i=0;
	while (true) {
		unsigned char c;
		read(ttyfd, &c, 1);
		if (c == RFID_START) { // Start of the packet
			i=1;
		} else if (c == RFID_END) { // End of the packet
			// Null-terminate the ASCII part of the buffer
			buf.terminator[0] = 0;

			// Tell mplayer to load a playlist named after the RFID tag data
			char cmdbuf[80];
			snprintf(cmdbuf, sizeof(cmdbuf), "loadlist /mnt/usb/%s.pls\n", buf.data);
			write(mplayerfd, cmdbuf, strlen(cmdbuf));
		} else if (i < sizeof(buf)-1) { // Somewhere in the middle of the packet
			((unsigned char *) &buf)[i] = c;
			i++;
		}
	}

	// Unreachable code, but this is what we'd need to clean up
	close(ttyfd);
	close(mplayerfd);
	return 0;
}
