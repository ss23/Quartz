<?php

// X1X to X5X are standard messages
// X6X to X7X are errors
// X8X and up is debug

// Listener messages : 110 - 199
define('LISTEN_RAW', 110);
define('SOCK_FORCED_DISCONNECT', 120); // The socket was forced closed because of a receiver. This simulates a hard shutdown.
define('SOCK_CLOSING', 121); // The listening is preparing to close
define('SOCK_CLOSED', 122); // The listener has no closed on request. Think of it as a soft shutdown.

define('SOCK_DISCONNECT', 160); // The socket closed unexpectedly

define('LISTEN_DEBUG', 180); // General debugging information
