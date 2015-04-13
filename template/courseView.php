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
					<?php if ($template['faculty']) { ?>
					<a href="/course/edit/<?php echo $template['course']['course_id']; ?>" class="pull-right">Edit Course</a>
					<?php } ?>
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
							<td>
								<?php echo utcToLocal($a['assignment_due']); ?>
								<?php
								if ($a['assignment_overdue']) {
									if ($a['assignment_hours_left'] < 24) {
										echo '(' . (int)($a['assignment_hours_left']) . ' hours left)';
									} else if ($a['assignment_hours_left'] / 24 < 30) {
										echo '(' . (int)($a['assignment_hours_left'] / 24) . ' days left)';
									} else {
										echo '(' . (int)($a['assignment_hours_left'] / 24 / 30) . ' months left)';
									}
								} else {
									if ($a['assignment_hours_left'] * -1 < 24) {
										echo '(' . (int)($a['assignment_hours_left'] * -1) . ' hours ago)';
									} else if ($a['assignment_hours_left'] * -1 / 24 < 30) {
										echo '(' . (int)($a['assignment_hours_left'] * -1 / 24) . ' days ago)';
									} else {
										echo '(' . (int)($a['assignment_hours_left'] * -1 / 24 / 30) . ' months ago)';
									}
								}
								?>
							</td>
							<td><?php echo $a['assignment_points']; ?></td>
						</tr>
						<?php } ?>
				</table>
				<?php if ($template['faculty']) { ?>
				<a href="/assignment/create/<?php echo $template['course']['course_id']; ?>">+ Assignment</a>
				<?php } ?>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
