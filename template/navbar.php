<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">UniversiTTP</a>
		</div> <!-- navbar-header end -->
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="/">Home</a></li>
				<li><a href="/report/grades">Grades</a></li>
				<?php if ($_SESSION['faculty']) { ?>
				<li class="dropdown">
					<a href="#tools" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Instructor tools <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/course/create">Create Course</a></li>
						<li><a href="/account/create">Create Student</a></li>
					</ul>
				</li>
				<?php } ?>
			</ul>
			<p class="navbar-text navbar-right"><?php echo $template['user_name_first'] . ' ' . $template['user_name_last']; ?>&nbsp;&nbsp;&nbsp;<a href="/logout" class="btn-sm btn-default">Logout</a></p>
		</div><!-- nav-collapse end-->
	</div><!-- container end -->
</nav>
