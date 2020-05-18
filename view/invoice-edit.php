<?php

global $url, $billing;
$invoice_id=( isset( $url->parts[2] ) ? $url->parts[2] : "" );
get_header( $invoice_id, 1 );

		if ( !empty( $invoice_id ) ) {
			$invoice=$billing->get_invoice( $invoice_id );
			?>
			<div id="logo">
				<img src="<?php print URL_VIEW ?>images/logo.png" />
			</div>
			<form name="edit-invoice" method="post" action="/invoices/detail/<?php print $invoice_id ?>/">
				<div id="invoice-info">
					<span class="label">Invoice ID</span> #<?php print $invoice->invoice_id; ?><br />
					<span class="label">Invoice Date</span> <input type="text" name="invoice_datetime" value="<?php print date( "n/j/Y g:i a", strtotime( $invoice->invoice_datetime ) ); ?>" class="right" /><br />
				</div>
				<div id="biller">
					<h3>Biller</h3>
					<?php
					print $settings->biller_filtered;
					?>
				</div>
				<div id="billee">
					<h3>Bill To:</h3>
					<?php
					client_select( $invoice->client_id );
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
						print "<tr class=\"row-$row\">
							<td class='qty'><input type='text' name='invoice_item_qty[" . $item->invoice_item_id . "]' id='invoice_item_qty_" . $item->invoice_item_id . "' value=\"" . $item->invoice_item_qty . "\" rel='" . $item->invoice_item_id . "' class='center update-price' /></td>
							<td><input type='text' name='invoice_item_description[" . $item->invoice_item_id . "]' value=\"" . $item->invoice_item_description . "\" /></td>
							<td class='number'><input type='text' name='invoice_item_cost[" . $item->invoice_item_id . "]' id='invoice_item_cost_" . $item->invoice_item_id . "' value=\"" . number_format( $item->invoice_item_cost, 2, ".", "," ) . "\" rel='" . $item->invoice_item_id . "' class='right update-price' /></td>
							<td class='number' id='invoice_item_" . $item->invoice_item_id . "'>$" . number_format( $item->invoice_item_qty*$item->invoice_item_cost, 2, ".", "," ) . "</td>
						</tr>";
						$row=1-$row;
					}
					?>
						<tr class="row-<?php print $row ?>">
							<td class='qty'><input type='text' name='new_invoice_item_qty' rel="new" id="invoice_item_qty_new" value="" class='center update-price' /></td>
							<td><input type='text' name='new_invoice_item_description' value="" /></td>
							<td class='number'><input type='text' name='new_invoice_item_cost' rel="new" id="invoice_item_cost_new" value="" class='right update-price' /></td>
							<td class='number' id="invoice_item_new"></td>
						</tr>
						<tr class="total-row"><th colspan=2></th><th class="number">Total:</th><th class="number invoice-total" id="new-invoice-total">$<?php print number_format( $total, 2, ".", "," ); ?></th></tr>
					</table>
				</div>
				<div class="right-buttons"><input type="submit" name="update-invoice" value="Update Invoice" class="button" /></div>
			</form>
			<?php
		} else {
			
		}

get_footer();

?>