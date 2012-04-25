<?php

$IS_PRODUCTION = False;

if( $IS_PRODUCTION ) {

} else {
	$HOST = "localhost";
	$DATABASE = "nmp_dev_tim";
	$USERNAME = "root";
	$PASSWORD = "asap4u2u";
}

/** Time until idle sessions are disconnected */
$session_timeout = 600; // 10 minutes to logout
$thumbnail_path = "/media/thumbnails/";

/** Web Root */
$web_root = "/portfolio";
