<?php

error_reporting( E_ALL );
ini_set( "display_errors", 1 );


// Let's set up the PATH_ROOT constant so we can include things!
$root=str_replace( "index.php", "", $_SERVER["DOCUMENT_ROOT"] . $_SERVER["SCRIPT_NAME"] );
$relative_url=str_replace( "index.php", "", $_SERVER["SCRIPT_NAME"] );
define( 'PATH_ROOT', $root );
define( 'PATH_VIEW', PATH_ROOT . "view/" );
define( "URL_ROOT", $relative_url );
define( "URL_VIEW", URL_ROOT . "view/" );

// Load up the system model
include( PATH_ROOT . "model/system.php" );

// Set the name of your database file.
$db_filename="avid.sqlite";

// Look for your database file in the directory above the current directory.
if ( file_exists( "../" . $db_filename ) ) {
	$db=new db( "../" . $db_filename );
// If it doesn't exist, check for an empty example, and copy it to where it should be.
} else if ( file_exists( $db_filename ) ) {
	$db=new db( $db_filename );
// If it doesn't exist, check for an empty example, and copy it to where it should be.
} else if ( file_exists( "avid-empty.sqlite" ) ) {
	copy( "avid-empty.sqlite", $db_filename );
	header( "Location: /" );
	exit;
}

// Run init function to instantiate all system objects for ease of use.
$system=new system;

// Aaand, leave the rest to the controller!
include( PATH_ROOT . "controller.php" );


