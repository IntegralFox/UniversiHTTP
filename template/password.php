<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Set New Password - UniversiHTTP';
			require('head.php');
		?>
		<link rel="stylesheet" href="/static/css/signin.css">
	</head>
	<body>
		<div class="container">
			<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="POST" class="form-signin">
				<h1 class="page-header">Update Password</h1>
				<p class="text-warning"><?php if (isset($template['error'])) echo $template['error']; ?>&nbsp;</p>
				<div class="form-group">
					<label for="username" class="sr-only">New Password</label>
					<input type="password" name="password" class="form-control" placeholder="New Password" required autofocus>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="sr-only">Confirm Password</label>
					<input type="password" name="passwordConfirm" class="form-control" placeholder="Confirm Password" required>
				</div>
				<div class="form-group">
					<input type="submit" class="btn-success form-control" value="Update">
				</div>
			</form>
		</div>
	</body>
</html>
