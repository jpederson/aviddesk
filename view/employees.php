<?php

get_header( "Manage Users" );

$user = new user;
$users = $user->get_users();

?>
		<h1>Employees</h1>
		<table cellspacing=0 class="employees">
			<tr>
				<th class="left">Name</th>
				<th>Email</th>
				<th>Username</th>
				<th>Options</th>
			</tr>
			<?php
			$row_alternate=0;
			foreach ( $users as $a_user ) { 
				?>
			<tr class="row-0">
				<td class="bold"><?php 
				if ( !empty( $a_user->user_fname ) && !empty( $a_user->user_lname ) ) {
					echo $a_user->user_fname . " " . $a_user->user_lname;
				} else {
					echo $a_user->user_login;
				}
				?></td>
				<td class="center"><?php if ( !empty( $a_user->user_email ) ) echo $a_user->user_email; ?></td>
				<td class="center" class="center"><?php if ( !empty( $a_user->user_login ) ) echo $a_user->user_login; ?></td>
				<td class="center"><a href="<?php print URL_ROOT ?>employees/edit/<?php print $a_user->user_id ?>/">[edit]</a></td>
			</tr>
				<?php 
				$row_alternate=1-$row_alternate;
			} 
			?>
		</table>
<?php

get_footer();

?>