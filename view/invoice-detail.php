<?php

global $url, $billing, $settings;

$invoice_id=( isset( $url->parts[2] ) ? $url->parts[2] : "" );

get_header( $invoice_id, 1 );

?>
		<?php
		if ( !empty( $invoice_id ) ) {
			$invoice=$billing->get_invoice( $invoice_id );
			?>
			<div id="logo">
				<img src="<?php print URL_VIEW ?>images/logo.png" />
			</div>
			<?php if ( isset( $_REQUEST["update-invoice-result"] ) ) print "<div class='success'>Invoice successfully updated!</div>"; ?>
			<div id="invoice-buttons">
				<?php if ( $invoice->invoice_status=="unpaid" ) { ?><a href="<?php print URL_ROOT ?>invoices/detail/<?php print $invoice->invoice_id ?>/?set-status=paid" class="button">Mark Paid</a><?php } ?>
				<?php if ( $invoice->invoice_status=="unpaid" ) { ?><a href="<?php print URL_ROOT ?>invoices/detail/<?php print $invoice->invoice_id ?>/?set-status=void" class="button">Mark Void</a><?php } ?>
				<?php if ( $invoice->invoice_status=="void" || $invoice->invoice_status=="paid" ) { ?><a href="<?php print URL_ROOT ?>invoices/detail/<?php print $invoice->invoice_id ?>/?set-status=unpaid" class="button">Mark Unpaid</a><?php } ?>
				<a href="<?php print URL_ROOT ?>invoices/edit/<?php print $invoice->invoice_id ?>/" class="button">Edit</a>
			</div>
			<div id="invoice-info">
				<span class="label">Invoice ID</span> #<?php print $invoice->invoice_id; ?><br />
				<span class="label">Invoice Date</span> <?php print date( "n/j/Y", strtotime( $invoice->invoice_datetime ) ); ?><br />
				<span class="label">Client ID</span> #<?php print $invoice->client_id; ?>
			</div>
			<div id="biller">
				<h3>Biller</h3>
				<?php print $settings->biller_filtered; ?>
			</div>
			<div id="billee">
				<h3>Bill To</h3>
				<?php
				print $invoice->client_name . "<br />";
				print str_replace( "\n", "<br />", $invoice->client_address );
				?>
			</div>
			<div id="invoice-items">
				<table border=0 cellpadding="4" cellspacing="0">
					<tr class="no-border"><th class="qty">Qty</th><th>Description</th><th class="number">Unit</th><th class="number">Total</th></tr>
				<?php 
				$total=0; 
				$row=1;
				foreach ( $invoice->items as $item ) { 
					$total +=( $item->invoice_item_qty*$item->invoice_item_cost );
					print "<tr class=\"" . row_alternate(1) . "\"><td class='qty'>" . $item->invoice_item_qty . "</td><td>" . $item->invoice_item_description . "</td><td class='number'>" . number_format( $item->invoice_item_cost, 2, ".", "," ) . "</td><td class='number'>$" . number_format( $item->invoice_item_qty*$item->invoice_item_cost, 2, ".", "," ) . "</td></tr>";
				}
				?>
					<tr class="total-row"><th colspan=2></th><th class="number">Total:</th><th class="number invoice-total">$<?php print number_format( $total, 2, ".", "," ); ?></th></tr>
				</table>
			</div>
			<div id="invoice-footer" class="print-only"><?php print $settings->footer_filtered; ?></div>

			<?php
		} else {
			
		}
		?>
<?php

get_footer();

?>