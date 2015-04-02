<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Account Creation Results - UniversiHTTP';
			require('head.php');
		?>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section theme-showcase" role="main">
			<div class="page-header">
					<h1>Accounts Created</h1>
			</div>
			<div>
				<p class="text-warning">NOTE: Temporary passwords cannot be retrieved once you have navigated away from this page. Please record them to distribute to users.</p>
				<table class="table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Username</th>
							<th>Temporary Password</th>
					</thead>
					<tbody>
						<?php foreach ($template['createdAccounts'] as $a) { ?>
						<tr>
							<td><?php echo $a['name']; ?></td>
							<td><?php echo $a['login']; ?></td>
							<td><?php echo $a['password']; ?> </td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
