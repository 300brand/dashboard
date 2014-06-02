<!DOCTYPE html>
<html lang="en" ng-app="dashboardApp">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<style type="text/css">
	.nav-tabs>li>a, .nav-pills>li>a {
		cursor:pointer;
	}
	.supervisor-log {
		padding-top:0.5em;
	}
	.supervisor-row {
		padding-bottom:0.25em;
		padding-top:0.25em;
		margin-bottom:2px;
	}
	.glyphicon-spin {
		        transform-origin:50% 50%;
		    -ms-transform-origin:50% 50%; /* IE 9 */
		-webkit-transform-origin:50% 50%;
		        animation:spin 1s infinite linear;
		     -o-animation:spin 1s infinite linear;
		   -moz-animation:spin 1s infinite linear;
		-webkit-animation:spin 1s infinite linear;
	}
	@-moz-keyframes spin {
		from {
			-moz-transform: rotate(0deg);
		}
		to {
			-moz-transform: rotate(360deg);
		}
	}

	@-webkit-keyframes spin {
		from {
			-webkit-transform: rotate(0deg);
		}
		to {
			-webkit-transform: rotate(360deg);
		}
	}

	@keyframes spin {
		from {
			transform: rotate(0deg);
		}
		to {
			transform: rotate(360deg);
		}
	}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-route.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap-tpls.min.js"></script>
	<script src="//cdn.jsdelivr.net/momentjs/2.6.0/moment.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.moment/0.7.1/angular-moment.min.js"></script>
	<script src="js/app.js"></script>
</head>
<body>

<header ng-controller="HeaderController">
	<nav class="navbar navbar-inverse" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header hidden-xs">
				<a class="navbar-brand" href="#/">Ocular8.net</a>
			</div>
			<ul class="nav navbar-nav">
				<li ng-class="{ active: isActive('/') }"><a href="#/">Dashboard</a></li>
				<li ng-class="{ active: isActive('/machines') }"><a href="#/machines">Machines</a></li>
				<li ng-class="{ active: isActive('/supervisors') }"><a href="#/supervisors">Supervisors</a></li>
				<li ng-class="{ active: isActive('/processes') }"><a href="#/processes">Processes</a></li>
			</ul>
		</div>
	</nav>
</header>

<div class="container-fluid" ng-view></div>

</body>
</html>
