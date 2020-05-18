<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php print PAGE_TITLE ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="<?php print URL_VIEW ?>js/main.js" type="text/javascript"></script>
<?php if ( $detect->is_tablet() ) { ?>
    <link href="<?php print URL_VIEW ?>css/tablet.css" type="text/css" rel="stylesheet"/>
<?php } else if ( $detect->is_mobile() ) { ?>
    <link href="<?php print URL_VIEW ?>css/phone.css" type="text/css" rel="stylesheet"/>
<?php } else { ?>
    <link href="<?php print URL_VIEW ?>css/style.css" type="text/css" rel="stylesheet" media="screen" />
<?php } ?>
    <link href="<?php print URL_VIEW ?>css/print.css" type="text/css" rel="stylesheet" media="print" />
    <meta name="viewport" content="width=device-width" />
</head>
<body>
    <div id="container">
        <header>
            <h1><a href="<?php print URL_ROOT ?>"><?php print $settings->title ?></a></h1>
            <nav>
                <?php if ( $user->logged_in() ) { ?>
                <a href="<?php print URL_ROOT ?>" class="phone-only">Home</a>
                <a href="<?php print URL_ROOT ?>clients/">Clients</a>
                <a href="<?php print URL_ROOT ?>invoices/">Invoices</a>
                <a href="<?php print URL_ROOT ?>employees/">Employees</a>
                <a href="<?php print URL_ROOT ?>settings/">Settings</a>
                <?php } ?>
            </nav>
            <div class="clear"></div>
        </header>
        <section id="main-content">
