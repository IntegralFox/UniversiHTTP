<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'UniversiHTTP';
			require('head.php');
		?>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section theme-showcase" role="main">
			<div class="page-header">
					<h1><?php if (!$template['faculty']) { ?>Enrolled <?php } ?>Courses</h1>
			</div>
			<div>
				<div class="list-group">
					<?php foreach ($template['course'] as $c) { ?>
					<a href="/course/<?php echo $c['course_id']; ?>" class="list-group-item"><?php echo htmlentities("{$c['course_number']} {$c['course_title']}"); ?></a>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
