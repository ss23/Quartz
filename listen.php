#!/usr/bin/php
<?php

require 'constants.php';

if ($argc != 2) {
	die("Usage: ./listen.php Network\n");
}

// Check the connection file
if (!is_readable('./connections/' . $argv[1])) {
	die("Cannot read {$argv[1]}\n");
}
if (($config = parse_ini_file('./connections/' . $argv[1], true)) === false) {
	die('Could not parse ini file');
}

$id = ftok('./connections/' . $argv[1], 'S');

// Create IPC thingy
$res = msg_get_queue($id);

// Check if we're the only 'listner' or not
$stats = msg_stat_queue($res);
if (!empty($stats['msg_lspid']) && file_exists("/proc/" . $stats['msg_lspid'])) {
	die("We are already connected\n");
}

// Create the connection
$attempts = $config['server']['reconnects'];

while (true) {
	if (($socket = fsockopen($config['server']['hostname'], $config['server']['port'])) !== false) {
		// We connected! Reset our attempts
		$attempts = $config['server']['reconnects'];

		while (($text = fgets($socket)) !== false) {
			msg_send($res, LISTEN_RAW, $text);
		}
	}
	// Tweak our error message, it doesn't work
	msg_send($res, SOCK_DISCONNECT, socket_last_error() . ':' . socket_strerror(socket_last_error()));

	// If this is our first failure, don't bother waiting. This could be a /kill or similar that isn't a connection issue
	if ($attempts !== $config['server']['reconnects']) {
		msg_send($res, LISTEN_DEBUG, 'Sleeping for ' . $config['server']['reconnectdelay'] . ' seconds');
		sleep($config['server']['reconnectdelay']);
	}

	// One less shot at freedom
	$attempts--;
	msg_send($res, LISTEN_DEBUG, 'Reconnection attempts left: ' . $attempts);
}
