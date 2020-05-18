<?php

get_header( "Create Client | Billing" );

?>
<div id="right-column">
	<h1>New Client</h1>
	<section class="two-column">
		<form name="add-client" action="<?php print $url->current ?>" method="post">
			<input type="hidden" name="new-client" value="true" />
			<p><label>Client Name:<br />
				<input type="text" name="client_name" size="40" class="wide-field" value="" /></label></p>
			<p><label>Client Address:<br />
				<textarea name="client_address" class="wide-field"></textarea></label><br />
				<span class="quiet">The address is displayed at the top of each invoice.</span></p>
			<p><label>Account Notes:<br />
				<textarea name="client_notes" class="wide-field tall-field"></textarea></label><br />
				<span class="quiet">Account notes are only accessible by internal users.</span></p>
			<p><input type="submit" value="Create Client" /></p>
		</form>
	</section>
</div>
<?php

get_footer();

?>