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
		</style>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section"> <!-- This is where page content will be added -->
			<section>
				<header>
					<h2 class="page-header">Create New Course</h2>
				</header>
				<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
					<div class="form-group">
						<label class="control-label col-sm-4">Course Number:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="courseNumber" placeholder="XX XXXX-XX">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Title:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="courseTitle">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Term:</label>
						<div class="col-sm-7">
							<select class="form-control" name="courseTerm">
								<?php foreach ($template['term'] as $t) { ?>
								<option value="<?php echo $t['term_id']; ?>"><?php echo $t['term_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-4">Course Year:</label>
						<div class="col-sm-7">
							<select class="form-control" name="courseYear">
								<?php foreach ($template['year'] as $y) { ?>
								<option value="<?php echo $y; ?>"><?php echo $y; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-4">
							<input type="submit" class="btn btn-default" value="Create">
						</div>
					</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
