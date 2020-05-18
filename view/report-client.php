<?php

include( "load.php" );

$billing=new billing;
$company=$billing->get_company_report( $_REQUEST["id"] );

get_header( "Client Report &raquo; " . $company->company_name . " | Billing" );

?>
<div id="billing-menu">
	<?php billing_menu() ?>
</div>
<div id="right-column">
	<h3>Client Report</h3>
	<p class="no-mobile"><?php company_report_select( $_REQUEST["id"] ); ?></p>
	<?php
	print "<h2 class='print-only'>" . $company->company_name . "</h2>";
	print "<p class='print-only'>" . str_replace( "\n", "<br />", $company->company_address ) . "</p>";
	?>
	<table cellpadding=0 cellspacing=0 border=0>
	<?php
	$overall_total=0;
	foreach ( $company->invoices as $invoice ) {
		$invoice_total=0;
		foreach ( $invoice->items as $inv_item ) {
			?><tr>
				<td class="qty"><?php print $invoice->invoice_id; ?></td>
				<td class="qty"><?php print $inv_item->invoice_item_qty; ?></td>
				<td><?php print $inv_item->invoice_item_description; ?></td>
				<td class="number">$<?php print number_format( $inv_item->invoice_item_cost, 2, ".", "," ); ?></td>
				<td class="number">$<?php print number_format( $inv_item->invoice_item_qty*$inv_item->invoice_item_cost, 2, ".", "," ); ?></td>
			</tr>
			<?php 
			$invoice_total=$invoice_total+( $inv_item->invoice_item_qty*$inv_item->invoice_item_cost );
		}
		$overall_total=$overall_total+$invoice_total;
	}
	?>
	</table>
	<div class="overall-total">
		<span>Number of Invoices:</span> <?php print count( $company->invoices ); ?><br />
		<span>Overall Total:</span> $<?php print number_format( $overall_total, 2, ".", "," ); ?>
	</div>
	<div class="clear"></div>
</div>
<?php

get_footer();

?>