<?php

$IS_PRODUCTION = False;

if( $IS_PRODUCTION ) {

} else {
	$HOST = "localhost";
	$DATABASE = "mj_dev";
	$USERNAME = "asap";
	$PASSWORD = "asap4u";
}

/** Time until idle sessions are disconnected */
$session_timeout = 600; // 10 minutes to logout


/** Web Root */
$web_root = "/mainejournal";
