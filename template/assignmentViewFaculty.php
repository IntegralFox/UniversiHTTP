<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['assignment']['assignment_name'] . ' - UniversiHTTP';
			require('head.php');
		?>
		<style>
			td>img {
				height: 1em;
			}
		</style>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<a href="/assignment/edit/<?php echo $template['assignment']['assignment_id']; ?>" class="pull-right">Edit Assignment</a>
					<h3 class="page-header"><?php echo htmlentities($template['assignment']['assignment_name']); ?></h3>
				</header>
			</section>
			<section>
				<h4>Submissions</h4>
				<table class="table table-condensed" summary="Submissions">
					<thead>
						<tr>
							<th>Student</th>
							<th>Last Modified</th>
							<th class="text-right">Files</th>
							<th class="text-center">Graded</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($template['submission'] as $s) { ?>
						<tr>
							<td><a href="/assignment/<?php echo $template['assignment']['assignment_id']; ?>/user/<?php echo $s['user_id']; ?>">
								<?php echo $s['user_name_last'] . ', ' . $s['user_name_first'] . (empty($s['user_name_middle']) ? '' : ' ' . substr($s['user_name_middle'], 0, 1)); ?>
							</a></td>
							<td>
								<?php echo empty($s['file_modified']) ? 'Never' : utcToLocal($s['file_modified']); ?>
								<?php
								if ($s['file_overdue']) {
									if ($s['file_hours'] < 24) {
										echo '(' . (int)($s['file_hours']) . ' hours overdue)';
									} else if ($s['file_hours'] / 24 < 30) {
										echo '(' . (int)($s['file_hours'] / 24) . ' days overdue)';
									} else {
										echo '(' . (int)($s['file_hours'] / 24 / 30) . ' months overdue)';
									}
								}
								?>
							</td>
							<td class="text-right"><?php echo $s['file_count']; ?></td>
							<td class="text-center"><img src="/static/img/<?php echo is_null($s['grade_points']) ? 'glyphicons-208-remove-2.png' : 'glyphicons-207-ok-2.png'; ?>"></td>
						</tr>
						<?php } ?>
				</table>
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
