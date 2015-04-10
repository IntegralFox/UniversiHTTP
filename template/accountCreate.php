<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Create New Accounts - UniversiHTTP';
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
					<h2 class="page-header">Create Single Account</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
						<label class="control-label col-sm-4">Last Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="lastName" pattern="^[a-zA-Z][a-zA-Z ]{0,29}$" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Middle Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="middleName" pattern="^[a-zA-Z][a-zA-Z ]{0,29}$">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">First Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="firstName" pattern="^[a-zA-Z][a-zA-Z ]{0,29}$" required>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox col-sm-7 col-sm-offset-4">
							<label>
								<input type="checkbox" name="faculty" value="1"> Faculty Account
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4">
							<input type="submit" class="btn btn-default pull-right" value="Create Account">
						</div>
					</div>
				</form>
			</section>
		</div>
		<div class="center-section">
			<section>
				<header>
					<h2 class="page-header">Batch Create Student Accounts</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>/multiple" enctype="multipart/form-data">
					<p>
						The CSV should have a single student on each line with portions of their names delimited by commas.<br>
						Example line: LastName, FirstName, MiddleName (optional)<br>
						<br>
						This can be accomplished easily in Excel and similar by placing last names in column A, first names in column B, and middle names in column C, then exporting as a CSV.<br>
						If options selected during export included headers in the CSV, please select the "First Line Contains Headers" option below.
					</p>
					<div class="form-group">
						<label class="control-label col-sm-4">CSV File:</label>
						<div class="col-sm-7">
							<input type="file" class="form-control" name="csv" required>
						</div>
					</div>
					<div class="form-group">
						<div class="checkbox col-sm-7 col-sm-offset-4">
							<label>
								<input type="checkbox" name="lineOneHeader" value="1"> First Line Contains Headers
							</label>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4">
							<input type="submit" class="btn btn-default pull-right" value="Create Accounts">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
