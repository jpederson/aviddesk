<?php

get_header( "AvidDesk Dashboard" );

$redirect_to=request( "redirect_to", URL_ROOT );

if ( $user->logged_in() ) {

	if ( $billing->has_data() ) {
		$clients=$billing->get_totals_by_client();
		$statuses=$billing->get_totals_by_status();
		$chart_data=$billing->date_graph_data();
		?>
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		    <script type="text/javascript">
		      google.load("visualization", "1", {packages:["corechart"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['Month', 'Amount Invoiced', 'Amount Due'],
		          <?php 
		          foreach ( $chart_data as $record ) {
		          	echo "['" . $record->invoice_month . "', " . $record->invoice_total . ", " . ( !empty( $record->unpaid_total ) ? $record->unpaid_total : "0" ) . "],";
		          }
		          ?>
		        ]);

		        var options = {
		          backgroundColor: "#f5f5f5",
		          hAxis: {title: 'Month', titleTextStyle: {color: 'gray'}},
		          colors: ["0e71ce", "ad1010", "8bba0d", "df8e0e"],
		          vAxis: {title: 'Amount Invoiced', titleTextStyle: {color: 'gray'}},
		          legend: {position: 'none'},
		          /*colors: ["#8BDB0B", "#0B70DB", "#DB0B0B", "#A00BDB", "#DB770B", "#E8E80E", "#13C9D6"],*/
		          vAxis: {format: "$#,###"}
		        };

		        var chart = new google.visualization.ColumnChart(document.getElementById('bar-chart'));
		        chart.draw(data, options);
		      }
		    </script>
		    <script type="text/javascript">
		      google.load("visualization", "1", {packages:["corechart"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['Client', 'Total'],
		          <?php 
		          foreach ( $clients as $client ) {
		          	echo "['" . $client->client_name . "', " . $client->invoice_total . "],";
		          }
		          ?>
		        ]);

		        var options = {
		          backgroundColor: "#f5f5f5",
		          colors: ["0e71ce", "8bba0d", "df8e0e", "ad1010"],
		          legend: {position: 'none'},
 				  chartArea: {left:20, top:20, width:"90%", height:"90%" },
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('pie-chart'));
		        chart.draw(data, options);
		      }
		    </script>
	          <?php 
	          foreach ( $statuses as $status ) {
	          	if ( $status->invoice_status=="unpaid" ) $unpaid_total=$status->invoice_total;
	          	if ( $status->invoice_status=="paid" ) $paid_total=$status->invoice_total;
	          }
	          ?>
		    <script type="text/javascript">
		      google.load("visualization", "1", {packages:["corechart"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = google.visualization.arrayToDataTable([
		          ['Invoice Status', 'Total'],
		          ['Paid', <?php print $paid_total ?>],
		          ['Unpaid', <?php print $unpaid_total ?>]
		        ]);

		        var options = {
		          backgroundColor: "#f5f5f5",
		          legend: {position: 'none'},
		          colors: ["8bba0d", "ad1010"],
 				  chartArea: {left:20, top:20, width:"90%", height:"90%" },
		        };

		        var chart = new google.visualization.PieChart(document.getElementById('status-pie-chart'));
		        chart.draw(data, options);
		      }
		    </script>

		    <h1>Dashboard</h1>
			<p>The data visualizations below summarize transactions in this system over the <strong>last <?php print $billing->report_range ?> days</strong>.</p>
			<h2>Invoices by Month</h2>
			<div class="graph-padding">
				<div id="bar-chart"></div>
			</div>
			<section class="two-column clear">
				<h2>Invoices by Client</h2>
				<div id="pie-chart"></div>
			</section>
			<section class="two-column">
				<h2>&nbsp;</h2>
				<table cellspacing="0" cellpadding="4" border="0">
					<tr>
						<th class="left">Client Name</th>
						<th class="number">Total</th>
					</tr>
				<?php
				if ( !empty( $clients ) ) {
					foreach ( $clients as $client ) {
				?>
				<tr class="<?php row_alternate() ?>">
					<td><a href="<?php print URL_ROOT; ?>invoices/?filter-invoices=true&client_id=<?php print $client->client_id ?>"><?php print $client->client_name ?></a></td>
					<td class="number">$<?php print number_format( $client->invoice_total, 2, ".", "," ) ?></td>
				</tr>
						<?php
					}
				}
				?>
				</table>
			</section>
			<section class="two-column clear">
				<h2>Invoices by Status</h2>
				<div id="status-pie-chart"></div>
			</section>
			<section class="two-column">
				<h2>&nbsp;</h2>
				<table cellspacing="0" cellpadding="4" border="0">
					<tr>
						<th class="left">Status</th>
						<th class="number">Totals</th>
					</tr>
				<?php
				if ( !empty( $statuses ) ) {
					foreach ( $statuses as $status ) {
				?>
				<tr class="<?php row_alternate() ?>">
					<td><a href="<?php print URL_ROOT; ?>invoices/?filter-invoices=true&invoice_status=<?php print $status->invoice_status ?>"><?php print ucfirst( $status->invoice_status ) ?></a></td>
					<td class="number">$<?php print number_format( $status->invoice_total, 2, ".", "," ) ?></td>
				</tr>
						<?php
					}
				}
				?>
				</table>
			</section>
	<?php
	} else {
		?>
		<h5 class="deck">Welcome to aviddesk</h5>
		<h1 class="deck-title">Almost ready, just a couple of steps and you're invoicing!</h1>
		<p>Currently there's nothing to show here. I know, it's sad - but not to worry, it's easy to solve!</p>
		<ol>
			<li><strong><a href="<?php print URL_ROOT ?>settings/">Configure Your Settings</a></strong><br />
				First, you should head over to the settings tab to edit the configuration of your billing system. 
				There, you can set up the biller information and invoice footer or customize the display of aviddesk.</li>
			<li><strong><a href="<?php print URL_ROOT ?>clients/">Add a Client</a></strong><br />
				The first thing you should take a look at is the clients section where you can add and manage your 
				client list.</li>
			<li><strong><a href="<?php print URL_ROOT ?>invoices/">Create an Invoice</a></strong><br />
				Once the client is in there, you're ready to add an invoice. Just click Invoices on the top menu and 
				then click "New Invoice" on the top of the listing.</li>
		</ol>
		<?php
	}
} else {
	?>
			<h1>Login</h1>
			<?php if ( isset( $_REQUEST["login-error"] ) ) { ?><div class="error">Your username or password doesn't match our records. Please try again or contact an administrator for assistance.</div><?php } ?>
			<p>Welcome to your billing system. Enter your email address and password below to login to the billing system.</p>
			<form name="login" method="post" action="<?php print URL_ROOT ?>">
				<input type="hidden" name="redirect_to" value="<?php print $redirect_to; ?>" />
				<p>Email:<br />
					<input type="text" name="login_email" value="" style="width: 200px;" /></p>
				<p>Password:<br />
					<input type="password" name="login_password" value="" style="width: 200px;" /></p>
				<p><input type="submit" name="login" value="Login" /></p>
			</form>
	<?php
}

get_footer();

?>