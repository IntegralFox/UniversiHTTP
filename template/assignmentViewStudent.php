<!DOCTYPE html>
<html lang="en">
	<head>
		<?php
			$template['title'] = $template['assignment']['assignment_name'] . ' - UniversiHTTP';
			require('head.php');
		?>
		<link rel="stylesheet" href="/static/css/dropzone.css">
		<script src="/static/js/dropzone.js"></script>
		<script>
			var assignment = "<?php echo $template['assignment']['assignment_id']; ?>";
			var folder;
			var file;
			var ctrl = false;

			$(function() {
				fetchContents();

				// File Selections
				$('#files').on('click', 'li.file, li.folder', function(e) {
					if (!ctrl) $('#files li').not(e.target).removeClass('bg-primary').removeClass('selected');
					$(e.target).toggleClass('bg-primary').toggleClass('selected');
					setButtonState();
					updateDropzoneFolder();
				});

				// Listen for ctrl keypresses
				$(document.body)
					.on('keydown', function(e) {
						if (e.ctrlKey) ctrl = true;
					})
					.on('keyup', function(e) {
						if (!e.ctrlKey) ctrl = false;
					});

				$('#createFolderButton').click(function() {
					var folder = '0';
					if ($('#files .selected.folder').length) folder = $('#files .selected').attr('id').substring(2);
					var name = prompt('Enter a name');

					if (name) {
						$.post('/folder/create', {
							assignment: assignment,
							parent: folder,
							name: name
						}, fetchContents);
					}
				});

				$('#deleteButton').click(function() {
					var count = $('#files .selected').length;
					if (confirm('Are you sure you want to recursively delete the ' + count + ' selected items?')) {
						$('#files .selected.file').each(function(index, node) {
							$.post('/file/delete', {
								file: node.id.substring(2)
							}, function() {
								count -= 1;
								if (count == 0) fetchContents();
							});
						});
						$('#files .selected.folder').each(function(index, node) {
							$.post('/folder/delete', {
								folder: node.id.substring(2)
							}, function() {
								count -= 1;
								if (count == 0) fetchContents();
							});
						});
					}
				});

				$('#renameButton').click(function() {
					var name = prompt('Enter a name');
					if (name) {
						if ($('#files .selected.folder').length) {
							$.post('/folder/rename', {
								folder: $('#files .selected.folder').attr('id').substring(2),
								name: name
							}, fetchContents);
						} else {
							$.post('/file/rename', {
								file: $('#files .selected.file').attr('id').substring(2),
								name: name
							}, fetchContents);
						}
					}
				});

				$('#serveButton').click(function() {
					var location = '/serve/assignment/' + assignment + '/';
					$selected = $('#files .selected');
					if ($selected.length) {
						location += recurseFilePath($selected);
					}
					open(location, '_blank');
				});

				Dropzone.options.drop = {
					init: function() {
						var dropzone = this;
						this.on("queuecomplete", function(file) {
							fetchContents();
							setTimeout(function() {
								dropzone.removeAllFiles();
							}, 1000);
						});
					}
				};
			});

			function fetchContents() {
				var fetched = false;
				$.get("/folder/json/assignment/" + assignment, function(data) {
					folder = data;
					if (fetched) {
						$('#files').empty().append(recurseGenerate(null));
						setButtonState();
					}
					else fetched = true;
				});
				$.get("/file/json/assignment/" + assignment, function(data) {
					file = data;
					if (fetched) {
						$('#files').empty().append(recurseGenerate(null));
						setButtonState();
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

			// Enable/Disable buttons based on Selections
			function setButtonState() {
				var count = $('#files .selected').length;
				if (count == 1) {
					$('#renameButton').prop('disabled', false);
				} else {
					$('#renameButton').prop('disabled', true);
				}
				if (count > 0) {
					$('#deleteButton').prop('disabled', false);
				} else {
					$('#deleteButton').prop('disabled', true);
				}
				if (count < 2) {
					$('#serveButton').prop('disabled', false);
				} else {
					$('#serveButton').prop('disabled', true);
				}
				if (count < 2 && $('#files .selected.folder').length == 1) {
					$('#createFolderButton').prop('disabled', false);
				} else {
					$('#createFolderButton').prop('disabled', true);
				}
			}

			// Updates the upload's folder target to the selected item
			function updateDropzoneFolder() {
				var folder = '0';
				if ($('#files .selected.folder').length) folder = $('#files .selected').attr('id').substring(2);
				$('#parentInput').val(folder);
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

			#drop {
				border: 5px dashed gray;
			}
		</style>
	</head>
	<body>
		<?php require('navbar.php'); ?>
		<div class="center-section">
			<section>
				<header>
					<h3 class="page-header"><?php echo htmlentities($template['assignment']['assignment_name']); ?></h3>
				</header>
			</section>
			<section id="files">
			</section>
			<section>
				<button type="button" id="serveButton" class="btn btn-default pull-right"><img src="/static/img/glyphicons-194-circle-ok.png"> Serve</button>
				<button type="button" id="createFolderButton" class="btn btn-default"><img src="/static/img/glyphicons-191-circle-plus.png"> Create Folder</button>
				<button type="button" id="renameButton" class="btn btn-default" disabled><img src="/static/img/glyphicons-151-edit.png"> Rename</button>
				<button type="button" id="deleteButton" class="btn btn-default" disabled><img src="/static/img/glyphicons-193-circle-remove.png"> Delete</button>
			</section>
			<section>
				<form action="/file/upload" id="drop" class="dropzone">
					<input type="hidden" id="assignmentInput" name="assignment" value="<?php echo $template['assignment']['assignment_id']; ?>">
					<input type="hidden" id="parentInput" name="parent" value="0">
					<div class="dz-message">Drop files here or click to upload to the selected folder</div>
				</form>
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
