<!DOCTYPE html>
<html lang="en" ng-app="dashboardApp">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
	<!-- Custom CSS from http://startbootstrap.com/template-overviews/sb-admin-2/ -->
	<link rel="stylesheet" href="css/sb-admin-2.css">
	<style type="text/css">
	.supervisor-log {
		padding-top:0.5em;
	}
	.supervisor-row {
		padding-bottom:0.25em;
		padding-top:0.25em;
		margin-bottom:2px;
	}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.22/angular.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.22/angular-route.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.22/angular-resource.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap-tpls.min.js"></script>
	<script src="//cdn.jsdelivr.net/momentjs/2.6.0/moment.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.moment/0.7.1/angular-moment.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="js/app.js"></script>
	<script src="js/sb-admin-2.js"></script>
</head>
<body>

<div id="wrapper">
	<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0" ng-controller="NavigationController">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.html">Ocular8 Control Panel</a>
		</div><!-- /.navbar-header -->

		<ul class="nav navbar-top-links navbar-right">
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
					<li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
					<li class="divider"></li>
					<li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
					</li>
				</ul><!-- /.dropdown-user -->
			</li><!-- /.dropdown -->
		</ul><!-- /.navbar-top-links -->

		<div class="navbar-default sidebar" role="navigation">
			<div class="sidebar-nav navbar-collapse">
				<ul class="nav" id="side-menu">
					<li>
						<a ng-class="{ active: isActive('/') }" href="#/"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
					</li>
					<li>
						<a ng-class="{ active: isActive('/machines') }" href="#/machines"><i class="fa fa-linux fa-fw"></i> Machines</a>
					</li>
					<li>
						<a ng-class="{ active: isActive('/supervisors') }" href="#/supervisors"><i class="fa fa-gears fa-fw"></i> Processes</a>
					</li>
					<li class="active">
						<a ng-class="{ active: isActive('/spider') }" href="#/processes"><i class="fa fa-sitemap fa-fw"></i> Spider<span class="fa arrow"></span></a>
						<ul class="nav nav-second-level">
							<li>
								<a href="#/spider/stats">Statistics</a>
							</li>
							<li>
								<a href="#/spider/config">Configuration</a>
							</li>
						</ul>
					</li>
				</ul>
			</div><!-- /.sidebar-collapse -->
		</div><!-- /.navbar-static-side -->
	</nav>

	<!-- Page Content -->
	<div id="page-wrapper">
		<div class="row">
			<div class="col-lg-12" ng-view>
				<h1 class="page-header">Wuh-Oh</h1>
				<p>Something bad happened, might need to check the console</p>
			</div><!-- /.col-lg-12 -->
		</div><!-- /.row -->
	</div><!-- /#page-wrapper -->

</div>

</body>
</html>
