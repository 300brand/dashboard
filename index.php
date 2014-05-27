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
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-route.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js"></script>
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap.js"></script>
	<!-- <script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap.min.js"></script> -->
	<script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap-tpls.js"></script>
	<!-- <script src="//cdn.jsdelivr.net/angular.bootstrap/0.11.0/ui-bootstrap-tpls.min.js"></script> -->
	<script src="js/app.js"></script>
	<!--
	<script src="js/services.js"></script>
	<script src="js/controllers.js"></script>
	-->
</head>
<body>

<div class="container">
	<div class="row">
		<header class="col-md-12">
			<h1>Ocular8.net</h1>
		</header>
	</div>

	<div class="row" ng-controller="MachineTabsController">
		<div class="col-md-4" ng-repeat="machine in machines">
			<div class="row"><div class="col-md-12"><h2>{{machine.title}}</h2></div></div>

			<div class="row">
				<div class="col-md-12"><h3>Docker Info</h3></div>

				<div class="col-md-12">
					<div class="row"><div class="col-md-4">Kernel Version</div><div class="col-md-8">{{machine.info.KernelVersion}}</div></div>
					<div class="row"><div class="col-md-4">Memory Limit</div><div class="col-md-8">{{machine.info.MemoryLimit}}</div></div>
					<div class="row"><div class="col-md-4">Swap Limit</div><div class="col-md-8">{{machine.info.SwapLimit}}</div></div>
					<div class="row"><div class="col-md-4">File Descriptors</div><div class="col-md-8">{{machine.info.NFd}}</div></div>
					<div class="row"><div class="col-md-4">Go Routines</div><div class="col-md-8">{{machine.info.NGoroutines}}</div></div>
					<div class="row"><div class="col-md-4">Images</div><div class="col-md-8">{{machine.info.Images}}</div></div>
					<div class="row"><div class="col-md-4">Containers</div><div class="col-md-8">{{machine.info.Containers}}</div></div>
				</div>
			</div>

			<div class="row" ng-repeat="container in machine.containers">

				<div class="col-md-12"><h4>{{container.Names[0]}}</h4></div>

				<div class="col-md-12">
					<div class="row"><div class="col-md-4">Status</div><div class="col-md-8">{{container.Status}}</div></div>
					<div class="row"><div class="col-md-12">Portmapping</div></div>
					<div class="row" ng-repeat="port in container.Ports">
						<div class="col-md-2">{{port.Type}}</div>
						<div class="col-md-6">{{port.IP}}:{{port.PublicPort}}</div>
						<div class="col-md-4">{{port.PrivatePort}}</div>
					</div>
				</div>
			</div>

			
		</div>
	</div>


<?php /*
	<nav ng-controller="MachineTabsController">
		<tabset justified="false" type="tabs" vertical="false">
			<tab ng-repeat="machine in machines" active="machine.active" disabled="machine.disabled">
				<tab-heading>
					<div class="text-left">
						<span class="glyphicon glyphicon-bell"></span> {{machine.title}}
					</div>
				</tab-heading>

				<div class="row">
					<div class="col-md-4">
					</div>
					<div class="col-md-4">
					</div>
					<div class="col-md-4">
						<p><strong>Docker Info</strong></p>
					</div>
				</div>

				<div class="row">
					<div class="col-md-4">
						<div class="col-md-4">
					</div>
					<div class="col-md-4">
					</div>
					<div class="col-md-4">
						<div class="row"><div class="col-md-4">Kernel Version</div><div class="col-md-8">{{machine.info.KernelVersion}}</div></div>
						<div class="row"><div class="col-md-4">Memory Limit</div><div class="col-md-8">{{machine.info.MemoryLimit}}</div></div>
						<div class="row"><div class="col-md-4">Swap Limit</div><div class="col-md-8">{{machine.info.SwapLimit}}</div></div>
						<div class="row"><div class="col-md-4">File Descriptors</div><div class="col-md-8">{{machine.info.NFd}}</div></div>
						<div class="row"><div class="col-md-4">Go Routines</div><div class="col-md-8">{{machine.info.NGoroutines}}</div></div>
						<div class="row"><div class="col-md-4">Images</div><div class="col-md-8">{{machine.info.Images}}</div></div>
						<div class="row"><div class="col-md-4">Containers</div><div class="col-md-8">{{machine.info.Containers}}</div></div>
					</div>
				</div>
			</tab>
		</tabset>
	</nav>
*/ ?>

</div>

</body>
</html>
