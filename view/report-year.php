<?php

include( "load.php" );

$billing=new billing;
$year=$billing->get_year_report( $_REQUEST["year"] );

get_header( "Yearly Report &raquo; " . $_REQUEST["year"] . " | Billing" );

?>
<div id="billing-menu">
	<?php billing_menu() ?>
</div>
<div id="right-column">
	<h3>Client Report</h3>
	<p class="no-mobile"><?php year_report_select( $_REQUEST["year"] ); ?></p>
	<h2 class="print-only">Annual Report: <?php print $_REQUEST["year"] ?></h2>
	<table cellpadding=0 cellspacing=0 border=0 class="report-year">
	<?php
	$overall_total=0;
	foreach ( $year->invoices as $invoice ) {
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
			$invoice_total=$invoice_total+($inv_item->invoice_item_qty*$inv_item->invoice_item_cost);
		}
		$overall_total=$overall_total+$invoice_total;
	}
	?>
	</table>
	<div class="overall-total">
		<span>Number of Invoices:</span> <?php print count( $year->invoices ); ?><br />
		<span>Overall Total:</span> $<?php print number_format( $overall_total, 2, ".", "," ); ?>
	</div>
	<div class="clear"></div>
</div>
<?php

get_footer();

?>