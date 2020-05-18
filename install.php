<?php

include( "model/system.php" );

$root=str_replace( "install.php", "", $_SERVER["DOCUMENT_ROOT"] . $_SERVER["SCRIPT_NAME"] );
$relative_url=str_replace( "install.php", "", $_SERVER["SCRIPT_NAME"] );
define( 'PATH_ROOT', $root );
define( 'PATH_VIEW', PATH_ROOT . "view/" );
define( "URL_ROOT", $relative_url );
define( "URL_VIEW", URL_ROOT . "view/" );

$queries[]="CREATE TABLE IF NOT EXISTS `client` (
  `client_id` int(9) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_address` longtext COLLATE utf8_unicode_ci NOT NULL,
  `client_notes` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
$queries[]="CREATE TABLE IF NOT EXISTS `invoice` (
  `invoice_id` int(9) NOT NULL AUTO_INCREMENT,
  `invoice_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invoice_status` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unpaid',
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1000 ;";
$queries[]="CREATE TABLE IF NOT EXISTS `invoice_item` (
  `invoice_item_id` int(9) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(9) NOT NULL,
  `invoice_item_description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `invoice_item_qty` double NOT NULL,
  `invoice_item_cost` double NOT NULL,
  PRIMARY KEY (`invoice_item_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";
$queries[]="CREATE TABLE IF NOT EXISTS `setting` (
  `setting_id` int(5) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` longtext NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
$queries[]="INSERT INTO `setting` (`setting_name`, `setting_value`) VALUES
('application_title', 'avid|desk'),
('biller', '[click settings to add your information]'),
('show_powered_by', 'yes'),
('footer', '[click settings and add payment terms and details to the invoice footer]');";
$queries[]="CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fname` varchar(100) NOT NULL,
  `user_lname` varchar(100) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_login` varchar(100) NOT NULL,
  `user_password` varchar(100) NOT NULL,
  `user_permission_passwords` tinyint(1) NOT NULL,
  `user_permission_clients` tinyint(1) NOT NULL,
  `user_permission_projects` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
$queries[]="INSERT INTO `user` ( `user_fname`, `user_lname`, `user_email`, `user_login`, `user_password`, `user_permission_passwords`, `user_permission_clients`, `user_permission_projects`) VALUES
( 'Admin', 'User', 'owner@aviddesk.com', 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 1, 1, 1);";

?><html>
<head>
	<title>Install Aviddesk</title>
	<link href="view/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?php

	$system=new system;

	?>
	<h3>Installing aviddesk...</h3>
	<p><?php
		foreach ( $queries as $query ) {
			if ( $db->update( $query ) ) {
				print "Successful query: " . substr( $query, 0, 50 ) . "<br />";
			} else {
				print "Query failed:<br />" . $query . "<br /><br />";
			}
		}
	?></p>


</body>
</html>
