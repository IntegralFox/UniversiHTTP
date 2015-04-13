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
			<div class="page-header">
					<h3>Course Grade Statistics</h3>
			</div>
			<table class="table table-condensed" summary="All Course Grade Statistics">
				<thead>
					<tr>
						<th>Course</th>
						<th>Min %</th>
						<th>Mean %</th>
						<th>Max %</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($template['course'] as $c) { ?>
					<tr>
						<td><a href="/report/grades/course/<?php echo $c['course_id']; ?>"><?php echo $c['course_number'] . ' ' . $c['course_title']; ?></a></td>
						<td><?php echo number_format($c['grade_min'], 2); ?></td>
						<td><?php echo number_format($c['grade_mean'], 2); ?></td>
						<td><?php echo number_format($c['grade_max'], 2); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
