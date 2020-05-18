<?php

class user {
	
	function __construct() {
		global $db, $url;

		// Check for login request variables
		$login_email=request( "login_email", "" );
		$login_password=request( "login_password", "" );
		$redirect_to=request( "redirect_to", "" );

		// Log them in!
		if ( !empty( $login_email ) && !empty( $login_password ) ) {
			$user_record=$db->query_one( "SELECT * FROM `user` WHERE ( `user_email`=\"$login_email\" OR `user_login`=\"$login_email\" ) AND `user_password`=\"" . md5( $login_password ) . "\" LIMIT 1;" );
			if ( !empty( $user_record->user_id ) ) {
				$_SESSION["user"]=$user_record->user_id;
				if ( !empty( $redirect_to ) ) {
					$url->redirect( $redirect_to );
				} else {
					$url->redirect( URL_ROOT );
				}
			} else {
				$url->redirect( URL_ROOT . "?login-error" );
			}
		}

		// Log them out!
		$logout=request( "logout", "unset" );
		if ( $logout!="unset" ) {
			session_destroy();
			$url->redirect( URL_ROOT );
		}

		if ( !$url->is_home() && !$this->logged_in() && !stristr( $url->current, "install.php" ) ) {
			$url->redirect( URL_ROOT . "?redirect_to=" . $_SERVER["REQUEST_URI"] );
		} 

	}

	function get_users() {
		global $db;
		$query="SELECT * FROM `user`;";
		$users=$db->query( $query );
		//print_r( $users );
		return $users;
	}

	function get_user( $user_id ) {
		global $db;
		$query="SELECT * FROM `user` WHERE `user_id`=" . $user_id . ";";
		$the_user=$db->query_one( $query );
		//print_r( $users );
		return $the_user;
	}

	function logged_in() {
		return isset( $_SESSION["user"] );
	}

}

?>