'use strict'

var logTransform = function(data, headers) {
	var o =JSON.parse(data)
	data = { text: o[0] }
	return data
}

var dashboardApp = angular.module('dashboardApp', [
	'angularMoment',
	'ngResource',
	'ngRoute',
	'ui.bootstrap'
])

dashboardApp.config([
	'$routeProvider', 
	'$locationProvider',
	function($routeProvider, $locationProvider) {
		$routeProvider
			.when('/', {
				templateUrl:  'pages/dashboard.html',
			})
			.when('/machines', {
				templateUrl:  'pages/machines.html',
				controller:   'MachinesController',
			})
			.when('/processes', {
				templateUrl:  'pages/processes.html',
				controller:   'ProcessesController',
			})
			.when('/supervisors', {
				templateUrl:  'pages/supervisors.html',
				controller:   'SupervisorsController',
			})
		// To remove the need for the hash (#)
		// $locationProvider.html5Mode(true)
	}
])

dashboardApp.service('InfoService', ['$resource', function($resource) {
	return $resource('proxy', { // paramDefaults
	}, { // actions
		machines:     { isArray: true,  url: 'proxy/_machines' }
	}, { // options
	})
}])

dashboardApp.service('DockerService', ['$resource', function($resource) {
	return $resource('proxy/:machine', { // paramDefaults
	}, { // actions
		info:         { isArray: false, url: 'proxy/:machine/0/info' },
		container:    { isArray: false, url: 'proxy/:machine/0/containers/:container/json' },
		containers:   { isArray: true,  url: 'proxy/:machine/0/containers/json' },
		top:          { isArray: false, url: 'proxy/:machine/0/containers/:container/top?ps_args=aux' }
	}, { // options
	})
}])

dashboardApp.service('SupervisorService', ['$resource', function($resource) {
	return $resource('proxy/:machine/:container', { // paramDefaults
	}, { // actions
		allProcesses: { isArray: true,  url: 'proxy/:machine/:container/supervisor.getAllProcessInfo' },
		state:        { isArray: false, url: 'proxy/:machine/:container/supervisor.getState' },
		stderrLogs:   {
			isArray: false,
			url: 'proxy/:machine/:container/supervisor.tailProcessStderrLog?name=:name&offset=0&length=16384',
			transformResponse: logTransform
		},
		stdoutLogs:   {
			isArray: false,
			url: 'proxy/:machine/:container/supervisor.tailProcessStdoutLog?name=:name&offset=0&length=16384',
			transformResponse: logTransform
		}
	}, { // options
	})
}])

dashboardApp.controller('HeaderController', [
	'$scope',
	'$route',
	'$routeParams',
	'$location',
	function($scope, $route, $routeParams, $location) {
		$scope.isActive = function(viewLocation) {
			return viewLocation === $location.path()
		}
	}
])

dashboardApp.controller('MachinesController', [
	'$scope',
	'DockerService',
	'InfoService',
	function($scope, Docker, Info) {
		$scope.machines = Info.machines(function(machines) {
			angular.forEach(machines, function(m) {
				m.info = Docker.info({machine: m.name})
				m.containers = Docker.containers({machine: m.name}, function(containers) {
					angular.forEach(containers, function(c) {
						c.detail = Docker.container({machine: m.name, container: c.Id})
					})
				})
			})
		})
	}
])

dashboardApp.controller('ProcessesController', [
	'$scope',
	'DockerService',
	'InfoService',
	function($scope, Docker, Info) {
		$scope.machines = Info.machines(function(machines) {
			angular.forEach(machines, function(m) {
				m.containers = Docker.containers({machine: m.name}, function(containers) {
					angular.forEach(containers, function(c) {
						c.top = Docker.top({machine: m.name, container: c.Id})
					})
				})
			})
		})
	}
])

dashboardApp.controller('SupervisorsController', [
	'$scope',
	'DockerService',
	'InfoService',
	'SupervisorService',
	function($scope, Docker, Info, Supervisor) {
		$scope.machines = Info.machines(function(machines) {
			angular.forEach(machines, function(m) {
				m.containers = Docker.containers({machine: m.name}, function(containers) {
					angular.forEach(containers, function(c) {
						c.supervisors = Supervisor.allProcesses({machine: m.name, container: c.Id}, function(supervisors) {
							angular.forEach(supervisors, function(s) {
								s.toggleLogs = function() {
									if (s.log) {
										s.log = null
										return
									}
									s.log = {}
									var args = {
										machine:   m.name,
										container: c.Id,
										name:      s.name
									}
									Supervisor.stdoutLogs(args, function(l) { s.log.stdout = l.text })
									Supervisor.stderrLogs(args, function(l) { s.log.stderr = l.text })
								}
							})
						})
					})
				})
			})
		})
	}
])

