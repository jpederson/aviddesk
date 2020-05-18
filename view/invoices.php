<?php

global $billing, $url;

get_header( "Invoices" );

$filter=new stdClass;
$filter->client_id=session("filter-client");
$filter->invoice_status=session("filter-status");

?>
		<section id="sidebar">
			<form name="invoices-narrow" method="post" action="<?php print $url->current ?>">
				<h3>Narrow Results</h3>
				<p>Client<br />
					<?php 
					$clients=$billing->get_clients();
					print "<select name=\"client_id\">";
					print "<option value=\"\" " . ( empty( $filter->client_id ) ? "selected" : "" ) . ">All Clients</option>";
					foreach ( $clients as $client ) {
						print "<option value=\"" . $client->client_id . "\" " . ( $client->client_id==$filter->client_id ? "selected" : "" ) . ">" . $client->client_name . "</option>";
					}
					print "</select>";
					?>
				</p>
				<p>Status<br />
					<?php 
					$clients=$billing->get_clients();
					print "<select name=\"invoice_status\">";
					print "<option value=\"\" " . ( $filter->invoice_status=="" ? "selected" : "" ) . ">All Statuses</option>";
					print "<option value=\"unpaid\" " . ( $filter->invoice_status=="unpaid" ? "selected" : "" ) . ">Unpaid</option>";
					print "<option value=\"paid\" " . ( $filter->invoice_status=="paid" ? "selected" : "" ) . ">Paid</option>";
					print "<option value=\"void\" " . ( $filter->invoice_status=="void" ? "selected" : "" ) . ">Void</option>";
					print "</select>";
					?>
				</p>
				<p><input type="submit" name="filter-invoices" value="Narrow" /></p>
			</form>
		</section>
		<section id="content">

			<?php 
			$new_invoice_result=request( "new-invoice-result" );
			if ( $new_invoice_result=='success' ) {
				print "<div class=\"success\">Invoice successfully created.</div>";
			} else if ( $new_invoice_result=='failure' ) {
				print "<div class=\"error\">There was an error creating your invoice. Check the error log and report it to the developer to get it fixed.</div>";
			}
			?>

			<div class="right-buttons"><a href="<?php print URL_ROOT ?>invoices/new/" class="button">New Invoice</a></div>
			<h1>Invoices</h1>
			<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<th>Invoice Information</td>
					<th>Total</td>
				</tr>
				<?php
				$billing=new billing;
				$invoices=$billing->get_invoices();
				$row_alternate=1;
				if ( !empty( $invoices ) ) {
					foreach ( $invoices as $invoice ) {
						?>
				<tr class="invoice row-<?php print $row_alternate; ?> <?php print $invoice->invoice_status ?>">
					<td><a href="<?php print URL_ROOT; ?>invoices/detail/<?php print $invoice->invoice_id ?>/"><?php print $invoice->invoice_id ?></a> :: <?php print $invoice->client_name ?> - <?php print date( "n/j", strtotime( $invoice->invoice_datetime ) ) ?></td>
					<td class="center">$<?php display_dollar( $invoice->invoice_total ); ?> <strong><?php print ucfirst( $invoice->invoice_status ) ?></strong></td>
				</tr>
						<?php
						$row_alternate=1-$row_alternate;
					}
				} else {
					print "<tr class=\"row-1\"><td colspan=\"4\">No results were found, try using fewer or different criteria.</td></tr>";
				}
				?>
				<tr class="bottom-row"><th colspan=4>&nbsp;</th></tr>
			</table>

		</section>
<?php

get_footer();

?>