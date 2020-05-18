<?php

get_header( "New Invoice" );

?>

			<form name="add-invoice" action="/invoices/" method="post">
				<input type="hidden" name="create_invoice" value="true" />
				<input type="hidden" name="invoice_datetime" value="<?php print date( "Y-m-d G:i:s" ); ?>" />
				<div id="invoice-info">
					<span class="label">Invoice Date</span> <?php print date( "M j, Y\<\b\\r \/\>g:i a" ); ?><br />
				</div>
				<div id="biller">
					<h3>Biller:</h3>
					<?php
					print $settings->biller_filtered;
					?>
				</div>
				<div id="billee">
					<h3>Bill To:</h3>
					<?php
					client_select('');
					?>
				</div>
				<div id="invoice-items">
					<table border=0 cellpadding="4" cellspacing="0">
						<tr><th class='qty'>Qty</th><th>Description</th><th class="number">Unit</th><th class="number">Subtotals</th></tr>
					<?php
					$row=1;
					$row_alternate=1;
					while ( $row<6 ) {
						?>
						<tr class="row-<?php print $row_alternate ?>">
							<td class="qty"><input type="text" name="invoice_item_qty[<?php print $row; ?>]" id="invoice_item_qty_<?php print $row; ?>" rel="<?php print $row; ?>" value="" class="update-price" /></td>
							<td><textarea rows=1 name="invoice_item_description[<?php print $row; ?>]" rel="<?php print $row; ?>"></textarea></td>
							<td class='number'><input type="text" name="invoice_item_cost[<?php print $row; ?>]" id="invoice_item_cost_<?php print $row; ?>" rel="<?php print $row; ?>" value="" class="update-price" /></td>
							<td class='number'><span id="invoice_item_<?php print $row ?>">-</span></td>
						</tr>
						<?php
						$row++;
						$row_alternate=1-$row_alternate;
					}
					?>
						<tr class="no-border total-row"><th colspan=2></th><th class="number">Total</th><th class="number" id="new-invoice-total">-</th></tr>
					</table>
					<div style="padding: 10px 0; text-align: right;">
						<input type="submit" value="Create Invoice" class="create-invoice-button" />
					</div>
				</div>
			</form>

<?php

get_footer();

?>