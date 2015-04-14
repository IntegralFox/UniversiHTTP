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
						alert('You must add at least one student to the selection.');
					} else {
						$('#enrolled option').prop('selected', true);
					}
				});
				$('#delete').click(function(e) {
					answer = confirm('Deleting the selected student accounts will remove them from all enrolled courses and permanently delete any files uploaded by them.\n\nContinue?');
					if (!answer) e.preventDefault();
				})
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
					<h2 class="page-header">Modify Student Accounts</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group row">
						<div class="col-sm-5">
							<label for="notEnrolled">All Students</label>
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
							<label for="enrolled">Modification Selection</label>
							<select size="15" id="enrolled" class="form-control" name="courseEnrolled[]" multiple></select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4 text-right">
							<input type="submit" name="delete" id="delete" class="btn btn-danger" value="Delete Selected">
							<input type="submit" name="reset" id="submit" class="btn btn-default" value="Reset Selected Passwords">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
