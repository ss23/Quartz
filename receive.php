#!/usr/bin/php
<?php

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

if (!msg_queue_exists($id)) {
	die("No listeners attached\n");
}

$res = msg_get_queue($id);
while (msg_queue_exists($id)) {
	if (msg_receive($res, 0, $msgType, 99999, $message)) {
		echo "Received '$message' of type '$msgType'\n";
	}
}

