<?php
session_start();

/**************************************************************
   
    SYSTEM:
    This function creates objects for each model in the
    system so they may simply be globalized to use anywhere
    in the system.
	
	REQUIRED CLASSES: db, url, user, detect, billing
	
 **************************************************************/
class system {

	function __construct() {

		include( PATH_ROOT . "model/user.php" );
		include( PATH_ROOT . "model/billing.php" );
		include( PATH_ROOT . "model/theme.php" );

		global $db, $url, $user, $detect, $billing;
		
		$url=new url;
		$user=new user;
		$detect=new detect;
		$billing=new billing;

		$this->get_settings();
	
		global $settings;
		$settings->title=( stristr( $settings->application_title, "|" ) ? str_replace( "|", "<span>", $settings->application_title ) . "</span>" : $settings->application_title );
		$settings->footer_filtered=nl2br( $settings->footer );
		$settings->biller_filtered=nl2br( $settings->biller );

		$update_settings=request( "save-settings" );
		if ( $update_settings ) {
			$settings_labels=array( "application_title", "show_powered_by", "biller", "footer" );
			foreach ( $settings_labels as $setting ) {
				$this->update_setting( $setting, request( $setting ) );
			}
			$url->redirect( URL_ROOT . "settings/?update-success" );
		}

	}

	function get_settings() {
		global $settings, $db;

		$all_settings=$db->query( "SELECT * FROM `setting`;" );

		$settings=new stdClass;
		if ( !empty( $all_settings ) ) {
			foreach ( $all_settings as $setting ) {
				$key=$setting->setting_name;
				$settings->$key=$setting->setting_value;
			}
		}
	}

	function update_setting( $label, $value ) {
		global $db;
		if ( $db->update( "UPDATE `setting` SET `setting_value`=\"" . addslashes( $value ) . "\" WHERE `setting_name`=\"$label\";" ) ) {
			return true;
		}
		return false;
	}

}



/**************************************************************
   
    URL FUNCTIONS:
    Two basic boolean functions to perform checks on the
    URL variables.
	
 **************************************************************/

class url {
	public $current="";
	public $current_full="";
	public $current_query="";
	public $parts=array();

	function __construct() {
		$this->current_full=$_SERVER["REQUEST_URI"];

		if ( stristr( $this->current_full, "?" ) ) {
			if ( substr( $this->current_full, strpos($this->current_full,"?")-1, 1 )!="/" && !stristr($this->current_full, ".") ) $this->redirect( str_replace( "?", "/?", $this->current_full) );
			$this->current=substr( $_SERVER["REQUEST_URI"], 0, strpos( $_SERVER['REQUEST_URI'], "?" ) );
			$this->current_query=$_SERVER["QUERY_STRING"];
		} else if ( stristr( $this->current_full, "#" ) ) {
			if ( substr( $this->current_full, strpos($this->current_full,"#")-1, 1 )!="/" && !stristr($this->current_full, ".") ) $this->redirect( str_replace( "#", "/#", $this->current_full) );
			$this->current=substr( $_SERVER["REQUEST_URI"], 0, strpos( $_SERVER['REQUEST_URI'], "#" ) );
			$this->current_query='';
		} else {
			if ( substr( $this->current_full, strlen($this->current_full)-1, 1 )!="/" && !stristr($this->current_full, ".") ) $this->redirect( $this->current_full . "/" );
			$this->current=$this->current_full;
			$this->current_query='';
		}

		$this->parts=explode( "/", substr( $this->current, 1, strlen( $this->current )-2 ) );
	}

	function is_home() {
		if ( $this->current==URL_ROOT ) {
			return true;
		}
		return false;
	}

	function is_url( $url ) {
		if ( $this->current==$url ) {
			return true;
		}
		return false;
	}

	function is_child_of( $url ) {
		if ( substr( $this->current, 0, strlen( $url ) )==$url ) {
			return true;
		}
		return false;
	}

	function redirect( $url ) {
		header( "Location: " . $url );
		exit;
	}

}

/**************************************************************
   
    MOBILE DETECTION:
    Instantiate this object and it will automatically get
    information about the visiting browser/OS and return
    it in simple functions so you may customize your site
    for mobile and screen devices.
	
	NO REQUIRED CLASSES OR TABLE STRUCTURES
	
 **************************************************************/
class detect {
	public $accept;
	public $userAgent;
	public $is_mobile = false;
	public $is_android = null;
	public $is_androidtablet = null;
	public $is_iphone = null;
	public $is_ipad = null;
	public $is_blackberry = null;
	public $is_blackberrytablet = null;
	public $is_opera = null;
	public $is_palm = null;
	public $is_windows = null;
	public $is_windowsphone = null;
	public $is_generic = null;
	public $is_tablet = null;
	public $devices = array(
		"android" => "android.*mobile",
		"androidtablet" => "android(?!.*mobile)",
		"blackberry" => "blackberry",
		"blackberrytablet" => "rim tablet os",
		"iphone" => "(iphone|ipod)",
		"ipad" => "(ipad)",
		"palm" => "(avantgo|blazer|elaine|hiptop|palm|plucker|xiino)",
		"windows" => "windows ce; (iemobile|ppc|smartphone)",
		"windowsphone" => "windows phone os",
		"generic" => "(kindle|mobile|mmp|midp|pocket|psp|symbian|smartphone|treo|up.browser|up.link|vodafone|wap|opera mini)"
	);

	function __construct() {
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
		$this->accept = $_SERVER['HTTP_ACCEPT'];

		if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
			$this->is_mobile = true;
		} elseif (strpos($this->accept, 'text/vnd.wap.wml') > 0 || strpos($this->accept, 'application/vnd.wap.xhtml+xml') > 0) {
			$this->is_mobile = true;
		} else {
			foreach ($this->devices as $device => $regexp) {
				if ($this->is_device($device)) {
					if ( $device!="androidtablet" && $device!="ipad" && $device!="blackberrytablet" ) $this->is_mobile = true;
				}
			}
		}
	}

	function is_mobile() {
		return $this->is_mobile;
	}

	function is_tablet() {
		return ( $this->is_device( "androidtablet" ) || $this->is_device( "blackberrytablet" ) || $this->is_device( "ipad" ) ) ? true : false;
	}

	function is_device( $device ) {
		$var = "is_" . $device;
		$return = $this->$var === null ? (bool) preg_match("/" . $this->devices[strtolower($device)] . "/i", $this->userAgent) : $this->$var;
		if ($device != 'generic' && $return == true) {
			$this->is_generic = false;
		}

		return $return;
	}

}


/**************************************************************
   
    REQUEST HELPER:
    Instantiate this object and it will automatically get
    information about the visiting browser/OS and return
    it in simple functions so you may customize your site
    for mobile and screen devices.
	
	NO REQUIRED CLASSES OR TABLE STRUCTURES
	
 **************************************************************/
function request( $param, $default="" ) {
	return ( isset( $_REQUEST[$param] ) ? $_REQUEST[$param] : ( !empty( $default ) ? $default : "" ) );
}

/**************************************************************
   
    SESSION HELPER:
    Instantiate this object and it will automatically get
    information about the visiting browser/OS and return
    it in simple functions so you may customize your site
    for mobile and screen devices.
	
	NO REQUIRED CLASSES OR TABLE STRUCTURES
	
 **************************************************************/
function session( $param, $default="" ) {
	return ( isset( $_SESSION[$param] ) ? $_SESSION[$param] : ( !empty( $default ) ? $default : "" ) );
}



/**************************************************************
   
    DATABASE OBJECT:
    Instantiate this object and it opens our SQLite database
    and is ready for queries.
	
 **************************************************************/
class db {

	var $connection;

	function db( $db_path="" ) {
		if ( !empty( $db_path ) ) {
			$this->connection = new SQLite3( $db_path );
		} else {
			die( "Database connection couldn't be established." );
		}
	}

	function query( $query="" ) {
		if ( !empty( $query ) ) {
			$result = $this->connection->query( $query );
			
			if ( $result === false ) {
				$this->handle_error();
			} else {
				$records=array();
				$n=1;
				while ( $row = $result->fetchArray() ) {
					if ( !empty( $row ) ) {
						$records[$n] = new stdClass;
						foreach ( $row as $key=>$val ) {
							if ( !is_numeric( $key ) ) {
								$records[$n]->$key = $val;
							}
						}
						$n++;
					} else {
						return false;
					}
				}

				return $records;
			}
		}
	}

	function query_one( $query="" ) {
		if ( !empty( $query ) ) {
			$result = $this->connection->query( $query );
			
			if ( $result === false ) {
				$this->handle_error();
			} else {
				$row = $result->fetchArray();
				if ( !empty( $row ) ) {
					$record = new stdClass;
					foreach ( $row as $key=>$val ) {
						if ( !is_numeric( $key ) ) {
							$record->$key = $val;
						}
					}
					return $record;
				} else {
					return false;
				}
			}
		}
	}

	function insert( $query="" ) {
		if ( !empty( $query ) ) {
			if ( $this->connection->query( $query ) ) {
				return $this->connection->lastInsertRowID();
			} else {
				$this->handle_error();
			}
		}
	}

	function update( $query="" ) {
		if ( !empty( $query ) ) {
			if ( $this->connection->query( $query ) ) {
				return true;
			} else {
				$this->handle_error();
			}
		}
	}

	function handle_error() {
		print "<pre>";
		print $this->connection->lastErrorMsg();
		print_r(debug_backtrace());
		print "</pre>";
		die;
	}

}


?>