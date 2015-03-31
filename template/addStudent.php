<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Add Student - UniversiHTTP';
			require('head.php');
		?>
		<style>
			.center-section {
			max-width: 800px;
		}
		</style>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section"> 
			<section>
				<header>
					<h2 class="page-header">Add New Student Account</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
						<label class="control-label col-sm-4">Last Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="lastName">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Middle Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="middleName">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">First Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="firstName">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Username:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="username">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4">
							<input type="submit" class="btn btn-default" value="Create">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
