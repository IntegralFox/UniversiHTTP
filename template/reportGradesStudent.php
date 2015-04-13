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
			<div>
					<h3 class="page-header">Student Grade Statistics</h3>
					<h4><?php echo $template['course']['course_number'] . ' ' . $template['course']['course_title']; ?></h4>
					<h4><?php echo $template['student']['user_name_last'] . ', '. $template['student']['user_name_first'] . (empty($template['student']['user_name_middle']) ? '' : ' ' . substr($template['student']['user_name_middle'], 0, 1)); ?></h4>
			</div>
			<table class="table table-condensed" summary="Assignment Statistics">
				<thead>
					<tr>
						<th>Assignment</th>
						<th class="text-right">Grade</th>
						<th class="text-right">Worth</th>
						<th class="text-right">% Credit</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($template['assignment'] as $a) { ?>
					<tr>
						<td><a href="/assignment/<?php echo $a['assignment_id']; if ($template['faculty']) echo '/user/' . $template['student']['user_id']; ?>"><?php echo $a['assignment_name']; ?></a></td>
						<td class="text-right"><?php echo (is_null($a['grade_points']) ? '-' : number_format($a['grade_points'], 2)); ?></td>
						<td class="text-right"><?php echo (is_null($a['assignment_points']) ? '-' : number_format($a['assignment_points'], 2)); ?></td>
						<td class="text-right"><?php echo (is_null($a['grade_percent']) ? '-' : number_format($a['grade_percent'], 2)); ?></td>
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr class="active">
						<td>Total</td>
						<td class="text-right"><?php echo (is_null($template['calculation']['grade_points']) ? '-' : number_format($template['calculation']['grade_points'], 2)); ?></td>
						<td class="text-right"><?php echo (is_null($template['calculation']['assignment_points']) ? '-' : number_format($template['calculation']['assignment_points'], 2)); ?></td>
						<td class="text-right"><?php echo (is_null($template['calculation']['grade_percent']) ? '-' : number_format($template['calculation']['grade_percent'], 2)); ?></td>
					</tr>
			</table>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
