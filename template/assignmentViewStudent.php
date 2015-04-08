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
			var fetched;

			$(function() {
				fetchContents();
			});

			function fetchContents() {
				fetched = false;
				$.get("/folder/json/" + assignment, function(data) {
					folder = data;
					if (fetched) $('#files').empty().append(recurseGenerate(null));
					else fetched = true;
				});
				$.get("/file/json/" + assignment, function(data) {
					file = data;
					if (fetched) $('#files').empty().append(recurseGenerate(null));
					else fetched = true;
				});
			}

			function recurseGenerate(parentId) {
				var $ul = $('<ul>');
				$.each(folder, function(index, node) {
					if (node.folder_parent_id == parentId) {
						var $li = $('<li>').attr('id', 'f_' + node.folder_id)
							.attr('class', 'folder');
						$li.append(node.folder_name).append(recurseGenerate(node.folder_id));
						$ul.append($li);
					}
				});
				$.each(file, function(index, node) {
					if (node.folder_id == parentId)
						var $li = $('<li>').attr('id', 'f_' + node.file_id).attr('class', 'file')
							.append(node.file_name).appendTo($ul);
				});
				return $ul;
			}
		</script>
		<style>
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
				<form action="/file/upload" id="drop" class="dropzone">
					<input type="hidden" id="assignment" name="assignment" value="<?php echo $template['assignment']['assignment_id']; ?>">
					<input type="hidden" id="parent" name="parent" value="0">
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
