<?php

$server_keys    = json_decode( getenv( 'SSH_FINGERPRINTS' ), true );
$default_key    = getenv( 'SSH_FINGERPRINT' );
$current_branch = getenv( 'CIRCLE_BRANCH' );

if ( json_last_error() === JSON_ERROR_NONE ) {

	if ( isset( $server_keys[ $current_branch ] ) ) {
		return $server_keys[ $current_branch ];
	} else {
		return $default_key;
	}

}
