<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['assignment']['assignment_name'] . ' - UniversiHTTP';
			require('head.php');
		?>
		<script>
			var assignment = "<?php echo $template['assignment']['assignment_id']; ?>";
			var user = "<?php echo $template['user']['user_id']; ?>";
			var folder;
			var file;

			$(function() {
				fetchContents();

				// File Selections
				$('#files').on('click', 'li.file, li.folder', function(e) {
					$('#files li').not(e.target).removeClass('bg-primary').removeClass('selected');
					$(e.target).toggleClass('bg-primary').toggleClass('selected');
				});

				$('#serveButton').click(function() {
					var location = '/serve/assignment/' + assignment + '/user/' + user + '/';
					$selected = $('#files .selected');
					if ($selected.length) {
						location += recurseFilePath($selected);
					}
					open(location, '_blank');
				});

				$('#gradeForm').submit(function(e) {
					e.preventDefault();
					if ($('#gradeInput').prop('disabled') == true) {
						$('#gradeInput, #commentTextarea').prop('disabled', false);
						$('#gradeButton').val('Grade');
					} else {
						$('#gradeInput, #commentTextarea, #gradeButton').prop('disabled', true);
						$.post('/assignment/grade', {
							assignment: assignment,
							user: user,
							grade: $('#gradeInput').val(),
							comment: $('#commentTextarea').val()
						}, function() {
							$('#gradeButton').val('Edit').prop('disabled', false);
						});
					}
				});
			});

			function fetchContents() {
				var fetched = false;
				$.get("/folder/json/assignment/" + assignment + '/user/' + user, function(data) {
					folder = data;
					if (fetched) {
						$('#files').empty().append(recurseGenerate(null));
					}
					else fetched = true;
				});
				$.get("/file/json/assignment/" + assignment + '/user/' + user, function(data) {
					file = data;
					if (fetched) {
						$('#files').empty().append(recurseGenerate(null));
					}
					else fetched = true;
				});
			}

			// Recursively generate an unordered list of the filesystem
			function recurseGenerate(parentId) {
				var $ul = $('<ul>');
				$.each(folder, function(index, node) {
					if (node.folder_parent_id == parentId) {
						var $li = $('<li>').attr('id', 'f_' + node.folder_id)
							.attr('class', 'folder');
						$li.append(node.folder_name);
						$ul.append($li);
						$('<li>').append(recurseGenerate(node.folder_id)).appendTo($ul);
					}
				});
				$.each(file, function(index, node) {
					if (node.folder_id == parentId)
						var $li = $('<li>').attr('id', 'f_' + node.file_id).attr('class', 'file')
							.append(node.file_name).appendTo($ul);
				});

				if ($ul.children().length == 0) {
					$ul.append('<li>Folder is Empty</li>');
				}

				return $ul;
			}

			function recurseFilePath($selected) {
				var path = $selected.text();
				if ($selected.hasClass('folder')) path += '/';
				if ($selected.parent().parent().is('li'))
					path = recurseFilePath($selected.parent().parent().prev()) + path;
				return path;
			}
		</script>
		<style>
			#files {
				height: 30em;
				overflow-y: scroll;
			}

			#files li.folder::before {
				content: '';
				display: inline-block;
				height: 1em;
				width: 2em;
				background-image: url(/static/img/glyphicons-145-folder-open.png);
				background-size: contain;
				background-repeat: no-repeat;
				background-position: left center;
			}

			#files li.file::before {
				content: '';
				display: inline-block;
				height: 1em;
				width: 2em;
				background-image: url(/static/img/glyphicons-37-file.png);
				background-size: contain;
				background-repeat: no-repeat;
				background-position: left center;
			}

			#files li {
				cursor: pointer;
			}

			#files li.file:hover, #files li.folder:hover {
				text-decoration: underline;
			}

			button img {
				height: 1em;
			}

			section {
				margin-top: 2em;
				margin-bottom: 2em;
			}

			#gradeInputDiv {
				width: 10em;
			}

			#commentTextarea {
				min-height: 10em;
				resize: vertical;
			}
		</style>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<h3 class="page-header"><?php echo htmlentities($template['assignment']['assignment_name']); ?></h3>
					<h4><?php echo htmlentities($template['user']['user_name_last'] . ', ' . $template['user']['user_name_first'] . (empty($template['user']['user_name_middle']) ? '' : ' ' . substr($template['user']['user_name_middle'], 0, 1))); ?></h4>
				</header>
			</section>
			<section id="files">
			</section>
			<section>
				<button type="button" id="serveButton" class="btn btn-default pull-right"><img src="/static/img/glyphicons-390-new-window-alt.png"> Serve</button>
				<form id="gradeForm">
				<div class="form-group">
					<div id="gradeInputDiv" class="input-group">
						<input type="number" min="0" step="1" id="gradeInput" class="form-control" placeholder="Grade" aria-describedby="basic-addon2" value="<?php echo $template['grade']['grade_points']; ?>" <?php if (!is_null($template['grade']['grade_points'])) echo 'disabled'; ?> required>
						<span class="input-group-addon" id="basic-addon2">/ <?php echo $template['assignment']['assignment_points']; ?></span>
					</div>
				</div>
				<div class="form-group">
					<textarea id="commentTextarea" class="form-control" placeholder="Enter feedback" pattern="^[a-zA-Z0-9 .,?/\\|;:’”!@#$%&amp;*(){}[\]<>]{0,1000}$" title="Can contain a-zA-Z0-9.,?/\|;:’”!@#$%&amp;*(){}[]<> up to 1000 characters." <?php if (!is_null($template['grade']['grade_points'])) echo 'disabled'; ?>><?php echo htmlentities($template['grade']['grade_comment']); ?></textarea><br>
				</div>
				<div class="form-group">
					<input type="submit" id="gradeButton" class="btn btn-default" value="<?php echo is_null($template['grade']['grade_points']) ? 'Grade' : 'Edit'; ?>">
				</div>
				</form>
			</section>
		</div>
		<?php require('footer.php'); ?>
	</body>
</html>
