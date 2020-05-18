<?php

class billing {
	
	var $report_range=90;

	function __construct() {

		global $url;

		// INVOICE LISTING FILTERING
		$filter=request( "filter-invoices", "" );
		if ( !empty( $filter ) ) {
			$_SESSION["filter-client"]=request( "client_id", "" );
			$_SESSION["filter-status"]=request( "invoice_status", "" );
			$url->redirect( URL_ROOT . "invoices/" );
		}

		// ADD INVOICE
		$create_invoice=request( "create_invoice", "" );
		if ( !empty( $create_invoice ) ) {
			if ( $this->add_invoice() ) {
				$url->redirect( URL_ROOT . "invoices/?new-invoice-result=success" );
			} else {
				$url->redirect( URL_ROOT . "invoices/?new-invoice-result=failure" );
			}
		}

		// UPDATE INVOICE
		$update_invoice=request( "update-invoice", "" );
		$invoice_id=( isset( $url->parts[2] ) ? $url->parts[2] : "" );
		if ( !empty( $update_invoice ) ) {
			if ( $this->update_invoice() ) {
				$url->redirect( URL_ROOT . "invoices/detail/$invoice_id/?update-invoice-result=success" );
			} else {
				$url->redirect( URL_ROOT . "invoices/detail/$invoice_id/?update-invoice-result=failure" );
			}
		}

		// UPDATE STATUSES
		$set_status=request( "set-status", "" );
		$invoice_id=( isset( $url->parts[2] ) ? $url->parts[2] : "" );
		if ( !empty( $set_status ) ) {
			if ( $this->set_status( $invoice_id, $set_status ) ) {
				
			}
		}

		// ADD CLIENT
		$create_client=request( "new-client", false );
		if ( $create_client ) {
			$client_data=new stdClass;
			$client_data->client_name=request( "client_name" );
			$client_data->client_address=request( "client_address" );
			$client_data->client_notes=request( "client_notes" );
			$new_client_id=$this->add_client( $client_data );
			if ( $new_client_id ) {
				$url->redirect( URL_ROOT . "clients/?add-client-success" );
			} else {
				$url->redirect( URL_ROOT . "clients/?add-client-failure" );
			}
		}

		// UPDATE CLIENT
		$update_client=request( "update-client", false );
		if ( $update_client ) {
			$client_data=new stdClass;
			$client_data->client_id=request( "client_id" );
			$client_data->client_name=request( "client_name" );
			$client_data->client_address=request( "client_address" );
			$client_data->client_notes=request( "client_notes" );
			if ( $this->update_client( $client_data ) ) {
				$url->redirect( URL_ROOT . "clients/?update-success" );
			} else {
				$url->redirect( URL_ROOT . "clients/?update-failure" );
			}
		}

	}
	
	function has_data() {
		global $db;
		$data=$db->query_one( "SELECT * FROM `invoice` LIMIT 1;" );
		if ( !empty( $data ) ) {
			return true;
		}
		return false;
	}

	/*******************************************/
	/**************** INVOICES *****************/
	/*******************************************/
	function get_invoices() {

		// Get the filters from the session
		$filter=new stdClass;
		$filter->client_id=session("filter-client");
		$filter->invoice_status=session("filter-status");

		// Are we filtering?
		$where=array();
		if ( !empty( $filter->client_id ) ) $where[]="inv.client_id=" . $filter->client_id;
		if ( !empty( $filter->invoice_status ) ) $where[]="inv.invoice_status=\"" . $filter->invoice_status ."\"";

		global $db;
		
		// Lets compile a query
		$select="SELECT inv.invoice_id, inv.invoice_status, inv.invoice_datetime, cli.client_id, cli.client_name, SUM( ii.invoice_item_qty*ii.invoice_item_cost ) AS invoice_total FROM 
			`invoice` inv 
			LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id 
			LEFT JOIN `client` cli ON inv.client_id=cli.client_id";
		if ( !empty( $where ) ) $select .=" WHERE " . implode( " AND ", $where );
		$select .=" GROUP BY inv.invoice_id ORDER BY inv.invoice_status DESC, inv.invoice_id DESC;";

		// Execute the query and return the results
		$invoices=$db->query( $select );
		return $invoices;
	}
	
	function get_invoice( $invoice_id ) { 
		global $db;
		$invoice=$db->query_one( "SELECT * FROM invoice, client WHERE client.client_id=invoice.client_id AND invoice.invoice_id=$invoice_id;" );
		$invoice->items=$this->get_invoice_items( $invoice->invoice_id );
		$invoice->total=0;
		foreach ( $invoice->items as $item ) {
			$invoice->total +=$item->invoice_item_qty*$item->invoice_item_cost;
		}
		return $invoice;
	}
	
	function add_invoice() {

		// Get request information
		$invoice_datetime=request( "invoice_datetime", date( "Y-m-d H:i:s" ) );
		$client_id=request( "client_id", "" );
		$invoice_item_qtys=request( "invoice_item_qty" );
		$invoice_item_descriptions=request( "invoice_item_description" );
		$invoice_item_costs=request( "invoice_item_cost" );

		// Add the invoice.
		global $db;
		if ( isset( $invoice_datetime ) && isset( $client_id ) ) {
			$new_invoice_id=$db->insert( "INSERT INTO `invoice` ( `invoice_datetime` , `client_id` ) VALUES ( '" . $invoice_datetime . "',  '" . $client_id . "' );");
			if ( $new_invoice_id ) {
				foreach ( $invoice_item_costs as $key=>$record ) {
					if ( !empty( $invoice_item_qtys[$key] ) && !empty( $invoice_item_descriptions[$key] ) && !empty( $invoice_item_costs[$key] ) ) {
						$item_data=new stdClass;
						$item_data->invoice_id=$new_invoice_id;
						$item_data->invoice_item_qty=$invoice_item_qtys[$key];
						$item_data->invoice_item_description=$invoice_item_descriptions[$key];
						$item_data->invoice_item_cost=$invoice_item_costs[$key];
						$this->add_invoice_item( $item_data );
					}
				}
				return $new_invoice_id;
			}
		}
		return false;
	}

	function update_invoice() {
		global $url, $db;

		// Get request information
		$invoice_id=( isset( $url->parts[2] ) ? $url->parts[2] : "" );
		$invoice_datetime=date( "Y-m-d H:i:s", strtotime( request( "invoice_datetime", '' ) ) );
		$client_id=request( "client_id", "" );
		$invoice_item_qtys=request( "invoice_item_qty" );
		$invoice_item_descriptions=request( "invoice_item_description" );
		$invoice_item_costs=request( "invoice_item_cost" );

		$new_invoice_item_qty=request( "new_invoice_item_qty" );
		$new_invoice_item_cost=request( "new_invoice_item_cost" );
		$new_invoice_item_description=request( "new_invoice_item_description" );

		// Update the invoice.
		if ( !empty( $client_id ) && !empty( $invoice_id ) ) {
			if ( $db->update( "UPDATE `invoice` SET `invoice_datetime`=\"" . $invoice_datetime . "\", `client_id`='" . $client_id . "' WHERE `invoice_id`=$invoice_id;") ) {
				foreach ( $invoice_item_costs as $key=>$record ) {
					if ( !empty( $invoice_item_qtys[$key] ) && !empty( $invoice_item_descriptions[$key] ) && !empty( $invoice_item_costs[$key] ) ) {
						$item_data=new stdClass;
						$item_data->invoice_item_id=$key;
						$item_data->invoice_item_qty=$invoice_item_qtys[$key];
						$item_data->invoice_item_description=$invoice_item_descriptions[$key];
						$item_data->invoice_item_cost=$invoice_item_costs[$key];
						$this->update_invoice_item( $item_data );
					}
				}

				if ( !empty( $new_invoice_item_qty ) && !empty( $new_invoice_item_cost ) && !empty( $new_invoice_item_description ) ) {
					$item_data=new stdClass; //edit
					$item_data->invoice_id=$invoice_id;
					$item_data->invoice_item_qty=$new_invoice_item_qty;
					$item_data->invoice_item_description=$new_invoice_item_description;
					$item_data->invoice_item_cost=$new_invoice_item_cost;
					$this->add_invoice_item( $item_data );
				}
				return true;
			}
		}
		return false;
	}

	function set_status( $invoice_id, $status ) {
		global $db;
		return $db->update( "UPDATE `invoice` SET `invoice_status`=\"" . $status . "\" WHERE `invoice_id`=$invoice_id;" );
	}
	
	
	
	/*******************************************/
	/************** INVOICE ITEMS **************/
	/*******************************************/
	function get_invoice_items( $invoice_id ) {
		global $db;
		return $db->query( "SELECT * FROM `invoice_item` WHERE `invoice_id`=$invoice_id;" );
	}
	
	function add_invoice_item( $data ) {
		global $db;
		if ( isset( $data->invoice_id ) && isset( $data->invoice_item_description ) && isset( $data->invoice_item_qty ) && isset( $data->invoice_item_cost ) ) {
			if ( $db->insert( "INSERT INTO `invoice_item` ( `invoice_id` , `invoice_item_description` , `invoice_item_qty` , `invoice_item_cost` ) VALUES ( '" . $data->invoice_id . "',  '" . $data->invoice_item_description . "',  '" . $data->invoice_item_qty . "',  '" . $data->invoice_item_cost . "' );") ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	function update_invoice_item( $data ) {
		global $db;
		if ( isset( $data->invoice_item_description ) && isset( $data->invoice_item_qty ) && isset( $data->invoice_item_cost ) && isset( $data->invoice_item_id ) ) {
			if ( $db->update( "UPDATE `invoice_item` SET `invoice_item_description`=\"" . $data->invoice_item_description . "\", `invoice_item_qty`=\"" . $data->invoice_item_qty . "\", `invoice_item_cost`=\"" . $data->invoice_item_cost . "\" WHERE `invoice_item_id`=" . $data->invoice_item_id . ";") ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/*******************************************/
	/**************** clients ****************/
	/*******************************************/
	function get_clients() {
		global $db;
		$data=$db->query( "SELECT cli.*, COUNT(inv.invoice_id) AS invoice_item_count, SUM(ii.invoice_item_qty*ii.invoice_item_cost) AS invoice_total FROM 
			`client` cli
			LEFT JOIN `invoice` inv ON cli.client_id=inv.client_id
			LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id
			GROUP BY cli.client_id ORDER BY cli.client_name LIMIT 100;" );
		if ( !empty( $data ) ) {
			foreach ( $data as $key=>$record ) {
				$count=$db->query_one( "SELECT COUNT(invoice_id) as invoice_count FROM invoice WHERE client_id=" . $record->client_id );
				$data[$key]->invoice_count=$count->invoice_count;
			}
		}
		return $data;
	}
	
	function get_client( $client_id ) {
		global $db;
		$client_info=$db->query_one( "SELECT * FROM `client` WHERE `client_id`=$client_id;" );
		return $client_info;
	}
	
	function add_client( $data ) {
		global $db;
		if ( !empty( $data->client_name ) && !empty( $data->client_address ) ) {
			if ( $db->insert( "INSERT INTO `client` ( `client_name`, `client_address`, `client_notes` ) VALUES ( \"" . addslashes( $data->client_name ) . "\", \"" . addslashes( $data->client_address ) . "\", \"" . addslashes( $data->client_notes ) . "\" );" ) ) { 
				return true; 
			}
 		}
		return false;
	} 

	function update_client( $data ) {
		global $db;
		if ( !empty( $data->client_name ) && !empty( $data->client_address ) && isset( $data->client_id ) ) {
			if ( $db->update( "UPDATE `client` SET `client_name`=\"" . addslashes( $data->client_name ) . "\", `client_address`=\"" . addslashes( $data->client_address ) . "\", `client_notes`=\"" . addslashes( $data->client_notes ) . "\" WHERE `client_id`=" . $data->client_id . ";" ) ) { 
				return true; 
			} 
		}
		return false;
	} 


	/*******************************************/
	/**************** reporting ****************/
	/*******************************************/
	function get_client_report( $client_id ) {
		global $db;
		$client_info=$db->query( "SELECT * FROM `client` WHERE `client_id`=$client_id;" );
		$client=$client_info[0];
		$invoices=$db->query( "SELECT * FROM `invoice` WHERE `client_id`=$client_id ORDER BY `invoice_datetime`;" );
		foreach ( $invoices as $inv ) {
			$client->invoices[$inv->invoice_id]=$this->get_invoice( $inv->invoice_id );
		}
		return $client;
	}
	
	function get_years() {
		global $db;
		$years=$db->query( "SELECT DISTINCT YEAR( invoice_datetime ) AS `year` FROM `invoice` ORDER BY `year` DESC;" );
		return $years;
	}
	
	function get_year_report( $year ) {
		global $db;
		$invoices=$db->query( "SELECT * FROM `invoice` WHERE `invoice_datetime`>\"$year-1-1 00:00:00\" AND `invoice_datetime`<\"$year-12-31 23:59:59\" ORDER BY `invoice_datetime` ASC;" );
		foreach ( $invoices as $inv ) {
			$client->invoices[$inv->invoice_id]=$this->get_invoice( $inv->invoice_id );
		}
		return $client;
	}
	
	function get_totals_by_status() {
		global $db;
		$days_ago=time()-(86400*$this->report_range);
		$data=$db->query( "SELECT inv.invoice_status, SUM(ii.invoice_item_qty*ii.invoice_item_cost) AS invoice_total FROM 
			`invoice` inv
			LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id
			WHERE inv.invoice_datetime>'" . date( "Y-m-d H:i:s", $days_ago ) . "'
			GROUP BY inv.invoice_status ORDER BY invoice_total DESC;" );
		return $data;
	}

	function get_totals_by_client() {
		global $db;
		$days_ago=time()-(86400*$this->report_range);
		$data=$db->query( "SELECT cli.client_id, cli.client_name, SUM(ii.invoice_item_qty*ii.invoice_item_cost) AS invoice_total FROM 
			`invoice` inv
			LEFT JOIN `client` cli ON inv.client_id=cli.client_id
			LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id
			WHERE inv.invoice_datetime>'" . date( "Y-m-d H:i:s", $days_ago ) . "'
			GROUP BY cli.client_name ORDER BY invoice_total DESC;" );
		if ( !empty( $data ) ) {
			foreach ( $data as $key=>$record ) {
				$count=$db->query_one( "SELECT COUNT(invoice_id) as invoice_count FROM invoice WHERE client_id=" . $record->client_id );
				$data[$key]->invoice_count=$count->invoice_count;
			}
		}
		return $data;
	}

	function date_graph_data() {
		global $db;
		$days_ago=time()-(86400*$this->report_range);
		$data=$db->query( "SELECT strftime(\"%m-%Y\", inv.invoice_datetime) as invoice_month, SUM(ii.invoice_item_qty*ii.invoice_item_cost) AS invoice_total FROM 
			`invoice` inv
			LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id
			WHERE inv.invoice_datetime>'" . date( "Y-m-d H:i:s", $days_ago ) . "'
			GROUP BY invoice_month;" );
		if ( !empty( $data ) ) {
			foreach ( $data as $key=>$record ) {
				$unpaid_total=$db->query_one( "SELECT SUM(ii.invoice_item_qty*ii.invoice_item_cost) AS invoice_total FROM invoice inv LEFT JOIN `invoice_item` ii ON inv.invoice_id=ii.invoice_id WHERE inv.invoice_status=\"unpaid\" AND strftime( \"%m-%Y\", inv.invoice_datetime)=\"" . $record->invoice_month . "\";" );
				$data[$key]->unpaid_total=$unpaid_total->invoice_total;
			}
		}
		return $data;
	}

}

function client_select( $initial ) {
	$billing=new billing;
	$clients=$billing->get_clients();
	print "<select name=\"client_id\">";
	foreach ( $clients as $client ) {
		print "<option value=\"" . $client->client_id . "\"" . ( $client->client_id==$initial ? " selected=\"selected\"" : "" ) . ">" . $client->client_name . "</option>";
	}
	print "</select>";
}

function client_report_select( $initial=0 ) {
	$billing=new billing;
	$clients=$billing->get_clients();
	print "<select name=\"client_id\" id=\"client-report\" class=\"wide-field\">";
	print "<option value=''>Select Client</option>";
	foreach ( $clients as $client ) {
		print "<option value=\"" . $client->client_id . "\" " . ( $client->client_id==$initial ? "selected='selected'" : "" ) . ">" . $client->client_name . "</option>";
	}
	print "</select>";
}

function year_report_select( $initial=0 ) {
	$billing=new billing;
	$years=$billing->get_years();
	print "<select name=\"year\" id=\"year-report\" class=\"wide-field\">";
	print "<option value=''>Select Year</option>";
	foreach ( $years as $year ) {
		$year=$year->year;
		print "<option value=\"" . $year . "\" " . ( $year==$initial ? "selected='selected'" : "" ) . ">" . $year . "</option>";
		$year--;
	}
	print "</select>";
}



?>