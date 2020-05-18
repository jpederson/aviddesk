<?php

get_header( "Clients | Billing" );

?>

	<?php if ( isset( $_REQUEST["update-success"] ) ) print "<div class='success'>Client successfully updated!</div>"; ?>
	<div class="right-buttons"><a href="<?php print URL_ROOT ?>clients/new/" class="button">New Client</a></div>
	<h1>Clients</h1>
	<?php
	if ( isset( $_REQUEST["save-client"] ) ) {
		$data=new stdClass;
		$data->client_id=$_REQUEST["client_id"];
		$data->client_name=$_REQUEST["client_name"];
		$data->client_address=$_REQUEST["client_address"];
		$data->client_notes=$_REQUEST["client_notes"];
		if ( $billing->save_client( $data ) ) {
			print "<div class='success'>client successfully updated.</div>";
		} else {
			print "<div class='error'>Failed to update client.</div>";
		}
	}
	if ( isset( $_REQUEST["add-client-success"] ) ) print "<div class='success'>Client successfully added.</div>";
	if ( isset( $_REQUEST["add-client-failure"] ) ) print "<div class='error'>Failed to create client.</div>";
	?>
	<table cellpadding="5" cellspacing="0" border="0">
		<tr>
			<th>Client Information</th>
			<th>Invoices</th>
		</tr>
		<?php
		$billing=new billing;
		$clients=$billing->get_clients();
		if ( !empty( $clients ) ) {
			$row_alternate=1;
			foreach ( $clients as $client ) {
				?>
				<tr class="row-<?php print $row_alternate ?>">
					<td><a href="<?php print URL_ROOT ?>clients/edit/<?php print $client->client_id ?>/"><?php print $client->client_name ?></a><br /><?php print str_replace( "\n", "<br />", $client->client_address ); ?></td>
					<td><a href="<?php print URL_ROOT ?>invoices/?client_id=<?php print $client->client_id ?>&filter-invoices=true"><?php print $client->invoice_count; ?> invoice<?php print ( $client->invoice_count==1 ? "" : "s" ) ?></a> &nbsp; <span>(<?php print $client->invoice_item_count ?> items)</span><br />
						<span>Total:</span> $<?php display_dollar( $client->invoice_total ); ?></td>
				</tr>
				<?php
				$row_alternate=1-$row_alternate;
			}
		} else {
			print "<tr class=\"row-1\"><td colspan=2>Nothing to show here yet...</td></tr>";
		}
		?>
		<tr class="bottom-row"><th colspan="2"></th></tr>
	</table>

<?php

get_footer();

?>