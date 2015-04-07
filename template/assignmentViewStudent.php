<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['assignment']['assignment_name'] . ' - UniversiHTTP';
			require('head.php');
		?>
		<link rel="stylesheet" href="/static/css/dropzone.css">
		<script src="/static/js/dropzone.js"></script>
		<script>
			$(function() {
			});
		</script>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<h3 class="page-header"><?php echo htmlentities($template['assignment']['assignment_name']); ?></h3>
				</header>
			</section>
			<section>
				<ul id="files">
				</ul>
			</section>
			<section>
				<form action="/file/upload" id="drop" class="dropzone">
					<input type="hidden" id="assignment" name="assignment" value="<?php echo $template['assignment']['assignment_id']; ?>">
					<input type="hidden" id="parent" name="parent" value="0">
				</form>
			</section>
			<section>
				<h4>Assignment Information</h4>
				<dl>
					<dt>Due Date</dt>
					<dd><?php echo utcToLocal($template['assignment']['assignment_due']); ?></dd>
					<dt>Points Worth</dt>
					<dd><?php echo $template['assignment']['assignment_points']; ?></dd>
					<dt>Description</dt>
					<dd><pre><?php echo nl2br(htmlentities($template['assignment']['assignment_description']), false); ?></pre></dd>
				</dl>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
