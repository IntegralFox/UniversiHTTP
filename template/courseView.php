<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['course']['course_title'] . ' - UniversiHTTP';
			require('head.php');
		?>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<h3 class="page-header"><?php echo htmlentities($template['course']['course_number'] . ' ' . $template['course']['course_title']); ?></h3>
					<h4><?php echo htmlentities($template['course']['user_name_first'] . ' ' . $template['course']['user_name_last']); ?></h4>
				</header>
				<ul>
					<li><a href="#">Instructor's Demos (Incomplete)</a></li>
					<li><a href="#">Static Course Files (Incomplete)</a></li>
					<li><a href="#">Sandbox (Incomplete)</a></li>
				</ul>
			</section>
			<section>
				<table class="table table-condensed" summary="Assignments">
					<thead>
						<tr>
							<th>Assignment</th>
							<th>Due Date</th>
							<th>Point Value</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($template['assignment'] as $a) { ?>
						<tr>
							<td><a href="/assignment/<?php echo $a['assignment_id']; ?>"><?php echo $a['assignment_name']; ?></a></td>
							<td><?php echo $a['assignment_due']; ?></td>
							<td><?php echo $a['assignment_points']; ?></td>
						</tr>
						<?php } ?>
				</table>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
