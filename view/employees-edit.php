<?php

get_header( "Edit Client | Billing" );

$the_user=$user->get_user( $url->parts[2] );

if ( !empty( $the_user ) ) {
?>
<h2>Edit Employee <span><span>|</span></span> <span><?php print $the_user->user_id ?></span></h2>
<form name="edit-employee" action="<?php print $url->current ?>" method="post">
	<div class="two-column">
		<input type="hidden" name="update-employee" value="true" />
		<input type="hidden" name="user_id" value="<?php print $url->parts[2] ?>" />
		<p><label>First Name:<br />
			<input type="text" name="user_fname" size="40" class="wide-field" value="<?php print htmlspecialchars( stripslashes( $the_user->user_fname ) ) ?>" /></label></p>
		<p><label>Last Name:<br />
			<input type="text" name="user_lname" size="40" class="wide-field" value="<?php print htmlspecialchars( stripslashes( $the_user->user_lname ) ) ?>" /></label></p>
		<p><label>Email Address:<br />
			<input type="text" name="user_fname" size="40" class="wide-field" value="<?php print htmlspecialchars( stripslashes( $the_user->user_email ) ) ?>" /></label></p>
		<p><label>Username:<br />
			<input type="text" name="user_fname" size="40" class="wide-field" value="<?php print htmlspecialchars( stripslashes( $the_user->user_login ) ) ?>" /></label></p>
		</p>
		<p><input type="submit" value="Update Employee" /></p>
	</div>
	<div class="two-column">
		<h2></h2>
	</div>
</form>
<?php
}

get_footer();

?>