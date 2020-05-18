<?php

get_header( "Invoices | aviddesk" );

?>
			<h1>Settings</h1>
			<?php if ( isset( $_REQUEST["update-success"] ) ) print "<div class='success'>Settings successfully updated!</div>"; ?>
			<p>Adjust the settings below to tweak the way your support desk system operates and handles your data.</p>
			<form name="settings" action="<?php print $url->current ?>" method="post">
				<section class="two-column">
					<h2>Application Title</h2>
					<p><span class="quiet">Customize the title of the application to fit your business.</span><br />
						<input type="text" name="application_title" value="<?php print $settings->application_title; ?>" class="wide-field" /></p>
				</section>
				<section class="two-column">
					<h2>Credit</h2>
					<p class="quiet">We always appreciate your support, but you have the option to disable the "Powered by aviddesk" footer message - afterall, who needs the fluff?</p>
					<p><label><input type="radio" name="show_powered_by" value="yes"<?php print ( $settings->show_powered_by=="yes" ? " checked=checked" : "" ) ?> /> Show "Powered by aviddesk" message.</label> <label><input type="radio" name="show_powered_by" value="no"<?php print ( $settings->show_powered_by=="no" ? " checked=checked" : "" ) ?> /> Hide it</label></p>
				</section>
				<section class="two-column clear">
					<h2>Biller Information</h2>
					<p><textarea name="biller"><?php print $settings->biller; ?></textarea><br />
					<span class="quiet">Displays on the top of each invoice as the Biller address.</span></p>
				</section>
				<section class="two-column">
					<h2>Invoice Footer</h2>
					<p><textarea name="footer"><?php print $settings->footer; ?></textarea><br />
					<span class="quiet">Displays on the bottom of all printed invoices. Recommend &lt;100 words. Include available payment methods, instructions and terms (Checks payable to "XYZ", NET 30, etc).</span></p>
				</section>
				<section class="two-column clear">
					&nbsp;
				</section>
				<section class="two-column">
					<input type="submit" name="save-settings" value="Save Settings" class="right" />
					<p class="quiet">Don't forget to save the settings &raquo;</p>
				</section>
			</form>

<?php

get_footer();

?>