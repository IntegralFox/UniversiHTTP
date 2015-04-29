<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = 'Create Course - UniversiHTTP';
			require('head.php');
		?>
		<style>
			.center-section {
			max-width: 800px;
			}
			#enroll {
				margin-top: 3em;
			}
		</style>
		<script>
			$(function() {
				$('#enroll').click(function() {
					$('#notEnrolled :selected').appendTo('#enrolled').prop('selected', false);
					sortSelect('#enrolled');
				});
				$('#unenroll').click(function() {
					$('#enrolled :selected').appendTo('#notEnrolled').prop('selected', false);
					sortSelect('#notEnrolled');
				});
				$('form').submit(function(e) {
					if ($('#enrolled option').length == 0) {
						e.preventDefault();
						alert('You must add at least one student to the roster.');
					} else {
							$('#enrolled option').prop('selected', true);
					}
				});
				$('#delete').click(function(e) {
					answer = confirm('Are you sure you want to delete this course?\nAll data including assignments, grades, and files will be permanently deleted.');
					if (!answer) e.preventDefault();
				});
			});
			function sortSelect(selector) {
				$(selector + ' option').sort(function(a, b) {
					return $(a).text() > $(b).text();
				}).appendTo(selector);
			}
		</script>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section"> <!-- This is where page content will be added -->
			<section>
				<header>
					<h2 class="page-header"><?php echo isset($template['editing']) ? 'Edit' : 'Create New'; ?> Course</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
						<label class="control-label col-sm-4">Course Number:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="courseNumber" pattern="^[A-Z]{2,4} [0-9A-Z]{4}-[0-9]{2}$" placeholder="DD CCCC-SS" required <?php if (isset($template['editing'])) echo "value=\"{$template['course']['course_number']}\""; ?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Title:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="courseTitle" pattern="^[a-zA-Z0-9][a-zA-Z0-9\-&amp; ]{0,99}$" title="Can be a-zA-Z0-9 up to 100 characters." required <?php if (isset($template['editing'])) echo "value=\"{$template['course']['course_title']}\""; ?>>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Term:</label>
						<div class="col-sm-7">
							<select class="form-control" name="courseTerm" >
								<?php foreach ($template['term'] as $t) { ?>
								<option value="<?php echo $t['term_id']; ?>" <?php if (isset($template['editing']) && $t['term_id'] == $template['course']['term_id']) echo 'selected'; ?>><?php echo $t['term_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Year:</label>
						<div class="col-sm-7">
							<select class="form-control" name="courseYear">
								<?php foreach ($template['year'] as $y) { ?>
								<option value="<?php echo $y; ?>" <?php if (isset($template['editing']) && $y == $template['course']['course_year']) echo 'selected'; ?>><?php echo $y; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Description:</label>
						<div class="col-sm-7">
							<textarea class="form-control" name="courseDescription" pattern="^[a-zA-Z0-9 .,?/\\|;:’”!@#$%&amp;*(){}[\]<>]{0,1000}$" title="Can contain a-zA-Z0-9 .,?/\|;:’”!@#$%&amp;*(){}[]<> up to 1000 characters." rows="6" required><?php if (isset($template['editing'])) echo htmlentities($template['course']['course_description']); ?></textarea>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-5">
							<label for="notEnrolled">Not Enrolled</label>
							<select size="15" id="notEnrolled" class="form-control" multiple>
								<?php foreach ($template['student'] as $s) { ?>
								<option value="<?php echo $s["user_id"]; ?>"><?php echo "{$s['user_name_last']}, {$s['user_name_first']} {$s['user_name_middle']}"; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-sm-2">
							<input type="button" id="enroll" value="&rtrif;" class="form-control"><br>
							<input type="button" id="unenroll" value="&ltrif;" class="form-control">
						</div>
						<div class="col-sm-5">
							<label for="enrolled">Enrolled Students</label>
							<select size="15" id="enrolled" class="form-control" name="courseEnrolled[]" multiple>
								<?php if (isset($template['editing'])) foreach ($template['enrolled'] as $s) { ?>
								<option value="<?php echo $s["user_id"]; ?>"><?php echo "{$s['user_name_last']}, {$s['user_name_first']} {$s['user_name_middle']}"; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 text-right">
							<?php if (isset($template['editing'])) { ?>
							<input type="submit" id="delete" name="delete" class="btn btn-danger" value="Delete Course">
							<?php } ?>
							<input type="submit" id="submit" class="btn btn-success" value="<?php echo isset($template['editing']) ? 'Save Changes' : 'Create Course'; ?>">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
