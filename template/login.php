<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Log In - UniversiHTTP';
			require('head.php');
		?>
		<link rel="stylesheet" href="/static/css/signin.css">
	</head>
	<body>
		<div class="container">
			<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="POST" class="form-signin">
				<h1 class="page-header">UniversiHTTP</h1>
				<p class="text-warning"><?php if (isset($template['error'])) echo $template['error']; ?>&nbsp;</p>
				<div class="form-group">
					<label for="username" class="sr-only">Username</label>
					<input type="text" name="usernamephp" id="inputText" class="form-control" placeholder="Username" <?php if (isset($template['username'])) echo "value=\"{$template['username']}\""; ?> required <?php if (!isset($template['username'])) { ?>autofocus<?php } ?>>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="sr-only">Password</label>
					<input type="password" name="passwordphp" id="inputPassword" class="form-control" placeholder="Password" required <?php if (isset($template['username'])) { ?> autofocus <?php } ?>>
				</div>
				<div class="form-group">
					<input type="submit" class="btn-primary form-control" value="Log In">
				</div>
			</form>
		</div>
	</body>
</html>
