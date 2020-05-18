<?php

get_header( "Edit Client | Billing" );

$client=$billing->get_client( $url->parts[2] );

if ( !empty( $client ) ) {
?>
<h2>Client <span><span>|</span></span> <span><?php print htmlspecialchars( stripslashes( $client->client_name ) ) ?></span></h2>
<form name="edit-client" action="<?php print $url->current ?>" method="post">
	<div class="two-column">
		<input type="hidden" name="update-client" value="true" />
		<input type="hidden" name="client_id" value="<?php print $url->parts[2] ?>" />
		<p><label>Client Name:<br />
			<input type="text" name="client_name" size="40" class="wide-field" value="<?php print htmlspecialchars( stripslashes( $client->client_name ) ) ?>" /></label></p>
		<p><label>Client Address:<br />
			<textarea name="client_address" class="wide-field"><?php print stripslashes( $client->client_address ) ?></textarea></label><br />
			<span class="quiet">The address is displayed at the top of the invoice.</span>
		</p>
		<p><label>Account Notes:<br /><textarea name="client_notes" class="wide-field tall-field"><?php print stripslashes( $client->client_notes ) ?></textarea></label><br />
		<span class="quiet">Account notes are only accessible by internal users.</span></p>
		<p><input type="submit" value="Update Information" /></p>
	</div>
	<div class="two-column">
		<h2></h2>
	</div>
</form>
<?php
}

get_footer();

?>