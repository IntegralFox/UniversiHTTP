<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Create New Assignment - UniversiHTTP';
			require('head.php');
		?>
		<style>
			.center-section {
				max-width: 800px;
			}
			textarea {
				resize: vertical;
			}
		</style>
		<script>
			$(function() {
				$('#delete').click(function(e) {
					answer = confirm('Are you sure you want to delete this assignment?\nAll data associated with it including files and grades will be permanently deleted.');
					if (!answer) e.preventDefault();
				});
			});
		</script>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<h2 class="page-header"><?php echo isset($template['editing']) ? 'Edit' : 'Create New'; ?> Assignment</h2>
					<h4>Course: <?php echo htmlentities($template['course']['course_number'] . ' ' . $template['course']['course_title']); ?></h4>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
						<label class="control-label col-sm-4">Assignment Name:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="assignmentName" pattern="^[a-zA-Z0-9&amp;#\-!()[\]{}. ]{1,99}$" title="Can contain a-zA-Z0-9&amp;#-!()[]{}. up to 100 characters." required <?php if (isset($template['editing'])) echo "value=\"{$template['assignment']['assignment_name']}\""; ?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Assignment Description:</label>
						<div class="col-sm-7">
							<textarea class="form-control" name="assignmentDescription" pattern="^[a-zA-Z0-9 .,?/\\|;:’”!@#$%&amp;*(){}[\]<>]{0,1000}$" title="Can contain a-zA-Z0-9 .,?/\|;:’”!@#$%&amp;*(){}[]<> up to 1000 characters." rows="6" required><?php if (isset($template['editing'])) echo htmlentities($template['assignment']['assignment_description']); ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Assignment Due Date:</label>
						<div class="col-sm-7">
							<input type="datetime" class="form-control" name="assignmentDueDate" placeholder="YYYY/MM/DD HH:MM:SS" required <?php if (isset($template['editing'])) echo "value=\"{$template['assignment']['assignment_due']}\""; ?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Assignment Points:</label>
						<div class="col-sm-7">
							<input type="number" class="form-control" name="assignmentPoints" min="0" step="1" required <?php if (isset($template['editing'])) echo "value=\"{$template['assignment']['assignment_points']}\""; ?>>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 text-right">
							<?php if (isset($template['editing'])) { ?>
							<input type="submit" name="delete" id="delete" class="btn btn-danger" value="Delete Assignment">
							<?php } ?>
							<input type="submit" class="btn btn-success" value="<?php echo isset($template['editing']) ? 'Save Changes' : 'Create Assignment'; ?>">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
