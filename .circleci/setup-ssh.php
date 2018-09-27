<?php

$server_keys    = json_decode( getenv( 'SERVER_DETAILS' ), true );
$default_key    = getenv( 'SSH_FINGERPRINT' );
$current_branch = getenv( 'CIRCLE_BRANCH' );

if ( json_last_error() === JSON_ERROR_NONE ) {

	// Send key value if key is set in server details variable
	if ( isset( $server_keys[ $current_branch ] ) && ! empty( $server_keys[ $current_branch ]->key ) ) {
		return $server_keys[ $current_branch ]->key;
	} else {
		return $default_key;
	}

}
