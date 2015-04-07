<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['assignment']['assignment_name'] . ' - UniversiHTTP';
			require('head.php');
		?>
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
							<th>Files</th>
							<th>Serve</th>
						</tr>
					</thead>
					<tbody>
						<?php //foreach ($template['submission'] as $s) { ?>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?php //} ?>
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
