<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Set New Password - UniversiHTTP';
			require('head.php');
		?>
		<link rel="stylesheet" href="/static/css/signin.css">
		<script>
			var attributeCount = 0;
			$(function() {
				$('#password').keyup(function() {
					attributeCount = 0;
					if ($(this).val().match(/.{10,72}/)) {
						$('#length').addClass('hasAttribute');
					} else {
						$('#length').removeClass('hasAttribute');
					}
					if ($(this).val().match(/[a-z]/)) {
						$('#lowercase').addClass('hasAttribute');
						++attributeCount;
					} else {
						$('#lowercase').removeClass('hasAttribute');
					}
					if ($(this).val().match(/[A-Z]/)) {
						$('#uppercase').addClass('hasAttribute');
						++attributeCount;
					} else {
						$('#uppercase').removeClass('hasAttribute');
					}
					if ($(this).val().match(/[0-9]/)) {
						$('#numbers').addClass('hasAttribute');
						++attributeCount;
					} else {
						$('#numbers').removeClass('hasAttribute');
					}
					if ($(this).val().match(/[~!@#$%\^&*()\-=_+]/)) {
						$('#special').addClass('hasAttribute');
						++attributeCount;
					} else {
						$('#special').removeClass('hasAttribute');
					}
				});
				$('#passwordConfirm').keyup(function() {
					if ($(this).val() && $(this).val() == $('#password').val()) {
						$('#confirm').addClass('hasAttribute');
					} else {
						$('#confirm').removeClass('hasAttribute');
					}
				});
				$('form').submit(function(e) {
					if (attributeCount < 2 || !$('#length').hasClass('hasAttribute') || !$('#confirm').hasclass('hasAttribute')) {
						e.preventDefault();
						alert('Your password does not match all of the requirements.');
					}
				})
			});
		</script>
		<style>
			li::before {
				content: '\2717  ';
			}
			li.hasAttribute {
				color: green;
			}
			li.hasAttribute::before {
				content: '\2713  ';
			}
			legend {
				font-size: 1em;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="POST" class="form-signin">
				<h1 class="page-header">Update Password</h1>
				<p class="text-warning"><?php if (isset($template['error'])) echo $template['error']; ?>&nbsp;</p>
				<div class="form-group">
					<label for="username" class="sr-only">New Password</label>
					<input type="password" id="password" name="password" class="form-control" placeholder="New Password" required autofocus>
				</div>
				<div class="form-group">
					<label for="inputPassword" class="sr-only">Confirm Password</label>
					<input type="password" id="passwordConfirm" name="passwordConfirm" class="form-control" placeholder="Confirm Password" required>
				</div>
				<fieldset class="form-group">
					<legend>Password Requirements</legend>
					<ul class="list-unstyled">
						<li id="length">Must be at least 10 characters</li>
						<li id="confirm">Confirmation must match</li>
				</fieldset>
				<fieldset class="form-group">
					<legend>Must have 2 of the following:</legend>
					<ul class="list-unstyled">
						<li id="uppercase">Uppercase Letters</li>
						<li id="lowercase">Lowercase Letters</li>
						<li id="numbers">Numbers</li>
						<li id="special">Special Characters (~!@#$%^&amp;*-=_+)</li>
					</ul>
				</fieldset>
				<div class="form-group">
					<input type="submit" class="btn-success form-control" value="Update">
				</div>
			</form>
		</div>
	</body>
</html>
