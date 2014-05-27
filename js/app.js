'use strict'

var dashboardApp = angular.module('dashboardApp', [
	'ngResource',
	'ui.bootstrap'
])

dashboardApp.service('DockerService', ['$resource', function($resource) {
	return $resource('proxy/:ident', {
		// paramDefaults
	}, {
		// actions
		info: {
			url:     'proxy/:ident/info',
			isArray: false
		},
		containers: {
			url:     'proxy/:ident/containers/json',
			isArray: true
		}
	}, {
		// options
	})
}])

dashboardApp.service('SupervisorService', ['$resource', function($resource) {
	return $resource('proxy/:ident', {
		// paramDefaults
	}, {
		// actions
		status: {
			url:     'proxy/:ident/supervisor.getState',
			isArray: false
		}
	}, {
		// options
	})
}])

dashboardApp.controller('MachineTabsController', ['$scope', 'DockerService', 'SupervisorService', function ($scope, Docker, Supervisor) {
	$scope.machines = [
		{
			title:'Campbeltown',
			ident:'campbeltown'
		},
		{
			title:'Highland',
			ident:'highland'
		},
		{
			title:'Island',
			ident:'island'
		}
	]

	for (var i = 0; i < $scope.machines.length; i++) {
		var ident = $scope.machines[i].ident
		$scope.machines[i].info = Docker.info({ident: ident})
		$scope.machines[i].containers = Docker.containers({ident: ident}, function() {
			for (var c = 0; c < $scope.machines[i].containers.length; c++) {
				var id = $scope.machines[i].containers[c].Id
				$scope.machines[i].containers[c].detail = Docker.container({ident: ident, id: id})
				$scope.machines[i].supervisors = Supervisor.status({ident: ident, id: id})
			}
		})
	}
}])

