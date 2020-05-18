<?php


// TEMPLATE FUNCTIONS

function get_header( $title="Dashboard", $hide_site_name=0 ) {
    global $settings;
    define( "PAGE_TITLE", $title . " | " . str_replace( "|", "", $settings->application_title ) );
    get_template( "header" );
}

function get_footer() {
    get_template( "footer" );
}

function get_template( $file_stem="" ) {
    global $url, $billing, $settings, $user, $detect;
    if ( !empty( $file_stem ) ) {
        if ( file_exists( PATH_VIEW . $file_stem . ".php" ) ) {
            include( PATH_VIEW . $file_stem . ".php" );
        } else {
            fatal_error( "The \"" . $file_stem . ".php\" template doesn't exist in the view folder." );
        }
    } else {
        fatal_error( "No template specified!" );
    }
}


// FORMATTING FUNCTIONS
function display_dollar( $number ) {
    print ( !stristr( $number, "." ) && !empty( $number ) ? $number : number_format( $number, 2, ".", "," ) );
}

function row_alternate( $return=0 ) {
    global $avid_row_alternate;
    $avid_row_alternate=1-$avid_row_alternate;
    if ( $return ) return "row-" . $avid_row_alternate;
        else print "row-" . $avid_row_alternate;
}

// ERROR FUNCTIONS

function fatal_error( $message="" ) {
    ?>
    <body style="padding: 70px 100px; margin: 0; background: #222; color: #999; font-size: 10pt; font-family: Arial, sans-serif; line-height: 16pt;">
        <strong>AVID ERROR:</strong><br />
        <?php print $message ?>
    </body>
    <?php
    die;
}

?>