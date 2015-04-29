<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Grades Summary - UniversiHTTP';
			require('head.php');
		?>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section theme-showcase" role="main">
			<section>
					<h3 class="page-header">Combined Grade Statistics</h3>
					<h4><?php echo $template['course']['course_number'] . ' ' . $template['course']['course_title']; ?></h4>
			</section>
			<section>
				<h4>Per Assignment</h4>
				<table class="table table-condensed" summary="Assignment Statistics">
					<thead>
						<tr>
							<th>Assignment</th>
							<th>Min %</th>
							<th>Mean %</th>
							<th>Max %</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($template['assignment'] as $a) { ?>
						<tr>
							<td><a href="/assignment/<?php echo $a['assignment_id']; ?>"><?php echo $a['assignment_name']; ?></a></td>
							<td><?php echo number_format($a['grade_min'], 2); ?></td>
							<td><?php echo number_format($a['grade_mean'], 2); ?></td>
							<td><?php echo number_format($a['grade_max'], 2); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</section>
			<section>
				<h4>Per Student</h4>
				<table class="table table-condensed" summary="Student Statistics">
					<thead>
						<tr>
							<th>Student</th>
							<th>Min %</th>
							<th>Mean %</th>
							<th>Max %</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($template['student'] as $s) { ?>
						<tr>
							<td><a href="/report/grades/course/<?php echo $template['course']['course_id']; ?>/student/<?php echo $s['user_id']; ?>"><?php echo $s['user_name_last'] . ', ' . $s['user_name_first'] . (empty($s['user_name_middle']) ? '' : ' ' . substr($s['user_name_middle'], 0, 1)); ?></a></td>
							<td><?php echo (is_null($s['grade_min']) ? '-' : number_format($s['grade_min'], 2)); ?></td>
							<td><?php echo (is_null($s['grade_mean']) ? '-' : number_format($s['grade_mean'], 2)); ?></td>
							<td><?php echo (is_null($s['grade_max']) ? '-' : number_format($s['grade_max'], 2)); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
