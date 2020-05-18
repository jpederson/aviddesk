<?php

global $url;

if ( $url->is_home() ) {
	get_template( "home" );
} else if ( $url->is_url( "/invoices/" ) ||  $url->is_child_of( "/invoices/page/" ) ) {
	get_template( "invoices" );
} else if ( $url->is_url( "/invoices/new/" ) ) {
	get_template( "invoice-new" );
} else if ( $url->is_child_of( "/invoices/detail/" ) ) {
	get_template( "invoice-detail" );
} else if ( $url->is_child_of( "/invoices/edit/" ) ) {
	get_template( "invoice-edit" );
} else if ( $url->is_url( "/clients/" ) ) {
	get_template( "clients" );
} else if ( $url->is_url( "/clients/new/" ) ) {
	get_template( "client-new" );
} else if ( $url->is_child_of( "/clients/edit/" ) ) {
	get_template( "client-edit" );
} else if ( $url->is_url( "/employees/" ) ) {
	get_template( "employees" );
} else if ( $url->is_child_of( "/employees/edit/" ) ) {
	get_template( "employees-edit" );
} else if ( $url->is_url( "/settings/" ) ) {
	get_template( "settings" );
} else if ( $url->is_url( "/api/" ) ) {
	$api=new api;
} else {
	get_template( "404" );
}

?>