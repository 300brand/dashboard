'use strict'

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
		state:        { isArray: false, url: 'proxy/:machine/:container/supervisor.getState' },
		allProcesses: { isArray: true,  url: 'proxy/:machine/:container/supervisor.getAllProcessInfo' }
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
						c.supervisors = Supervisor.allProcesses({machine: m.name, container: c.Id})
					})
				})
			})
		})
	}
])

