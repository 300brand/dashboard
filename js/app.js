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
				controller:   'SupervisorsController',
			})
			.when('/supervisors', {
				templateUrl:  'pages/supervisors.html',
				controller:   'SupervisorsController',
			})
			.when('/spider/config', {
				templateUrl:  'pages/spider-config.html',
				controller:   'SpiderConfigController',
			})
			.when('/spider/config/add', {
				templateUrl:  'pages/spider-config-form.html',
				controller:   'SpiderRuleAddFormController',
			})
			.when('/spider/config/edit/:id', {
				templateUrl:  'pages/spider-config-form.html',
				controller:   'SpiderRuleEditFormController',
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
		allProcesses: {
			isArray: true,
			url:     'proxy/:machine/:container/supervisor.getAllProcessInfo'
		},
		processInfo: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.getProcessInfo?name=:name'
		},
		start: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.startProcess?name=:name&wait=true'
		},
		state: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.getState'
		},
		stderrLogs: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.tailProcessStderrLog?name=:name&offset=0&length=16384',
			transformResponse: logTransform
		},
		stdoutLogs: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.tailProcessStdoutLog?name=:name&offset=0&length=16384',
			transformResponse: logTransform
		},
		stop: {
			isArray: false,
			url:     'proxy/:machine/:container/supervisor.stopProcess?name=:name&wait=true'
		}
	}, { // options
	})
}])

dashboardApp.service('SpiderConfiguratorService', ['$resource', function($resource) {
	return $resource('configurator/spider', { //paramDefaults
	}, { // actions
		all: {
			isArray: false,
			url:     'configurator/spider/rule/all',
		},
		'delete': {
			isArray: false,
			url:     'configurator/spider/rule/delete/:id',
		},
		update: {
			isArray: false,
			url:     'configurator/spider/rule/update',
			method:  'POST',
		},
		create: {
			isArray: false,
			url:     'configurator/spider/rule/create',
			method:  'POST',
		},
		one: {
			isArray: false,
			url:     'configurator/spider/rule/:id',
		},
		test: {
			isArray: false,
			url:     'configurator/spider/rule/test',
			method:  'POST',
		},
		validate: {
			isArray: false,
			url:     'configurator/spider/rule/validate',
			method:  'POST',
		},
	}, { // options
	})
}])

dashboardApp.controller('NavigationController', [
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
								var args = {
									machine:   m.name,
									container: c.Id,
									name:      s.name
								}
								s.cmd = {
									activate: function(what) {
										s.active = s.active || {}
										s.active[what] = true
									},
									deactivate: function(what) {
										s.active = s.active || {}
										s.active[what] = false
									},
									toggleLogs: function() {
										if (s.log) {
											s.log = null
											return
										}
										s.cmd.activate('toggleLogs')
										s.log = {}
										Supervisor.stdoutLogs(args, function(l) {
											if (s.log) {
												s.cmd.deactivate('toggleLogs')
											}
											s.log.stdout = l.text
										})
										Supervisor.stderrLogs(args, function(l) {
											if (s.log) {
												s.cmd.deactivate('toggleLogs')
											}
											s.log.stderr = l.text
										})
									},
									stop: function() {
										s.cmd.activate('stop')
										Supervisor.stop(args, function() {
											Supervisor.processInfo(args, function(data) {
												angular.extend(s, data)
												s.cmd.deactivate('stop')
											})
										})
									},
									start: function() {
										s.cmd.activate('start')
										Supervisor.start(args, function() {
											Supervisor.processInfo(args, function(data) {
												angular.extend(s, data)
												s.cmd.deactivate('start')
											})
										})
									},
									restart: function() {
										s.cmd.activate('restart')
										Supervisor.stop(args, function() {
											Supervisor.start(args, function() {
												Supervisor.processInfo(args, function(data) {
													angular.extend(s, data)
													s.cmd.deactivate('restart')
												})
											})
										})
									}
								}
							})
						})
					})
				})
			})
		})
	}
])

dashboardApp.controller('SpiderConfigController', [
	'$scope',
	'$modal',
	'SpiderConfiguratorService',
	function($scope, $modal, SpiderConfiguratorService) {
		$scope.rules = []
		SpiderConfiguratorService.all(function(data) {
			$scope.rules = data.Response
		})
	}
])

dashboardApp.controller('SpiderRuleAddFormController', [
	'$scope',
	'$location',
	'SpiderConfiguratorService',
	function($scope, $location, SpiderConfiguratorService) {
		// Add New
		$scope.goUrlToString = goUrlToString
		$scope.formName = "Add New Rule"
		$scope.host = ''
		$scope.json = angular.toJson({
			Ident:       "uniqueIdentifier",
			Start:       "http://www.example.com/startpage",
			CSSLinks:    "a[href]",
			CSSTitle:    "title",
			MaxDepth:    1,
			RestartMins: 30,
			Accept:      [ "^/news/articles/.*.php" ],
			Reject:      [ "^/news/articles/list.php" ]
		}, true)
		$scope.validate = function(form) {
			SpiderConfiguratorService.validate({ json: $scope.json}, function(data) {
				form.json.$setValidity("json", data.Success)
			})
		}
		$scope.submit = function() {
			SpiderConfiguratorService.create({ host: $scope.host, json: $scope.json }, function(data) {
				if (data.Success) {
					$location.path('/spider/config')
				} else {
					console.log("Submission failed:", data)
				}
			})
		}
		$scope.test = function(form) {
			SpiderConfiguratorService.test({json: $scope.json}, function(data) {
				$scope.testData = data
			})
		}
	}
])

dashboardApp.controller('SpiderRuleEditFormController', [
	'$scope',
	'$route',
	'$location',
	'SpiderConfiguratorService',
	function($scope, $route, $location, SpiderConfiguratorService) {
		// Edit
		$scope.goUrlToString = goUrlToString
		$scope.formName = "Edit Rule"
		$scope.id = $route.current.params.id

		SpiderConfiguratorService.one({ id: $scope.id }, function(data) {
			if (data.Success) {
				$scope.host = data.Response.Host
				$scope.json = data.Response.RuleStr
			} else {
				console.log("One failed:", data)
			}
		})

		$scope.validate = function(form) {
			SpiderConfiguratorService.validate({ json: $scope.json}, function(data) {
				form.json.$setValidity("json", data.Success)
			})
		}
		$scope.submit = function() {
			SpiderConfiguratorService.update({ id: $scope.id, host: $scope.host, json: $scope.json }, function(data) {
				if (data.Success) {
					$location.path('/spider/config')
				} else {
					console.log("Submission failed:", data)
				}
			})
		}
		$scope.test = function(form) {
			SpiderConfiguratorService.test({json: $scope.json}, function(data) {
				$scope.testData = data
			})
		}
	}
])

dashboardApp.controller('SpiderRuleDeleteController', [
	'$scope',
	'$route',
	'$modal',
	'SpiderConfiguratorService',
	function($scope, $route, $modal, SpiderConfiguratorService) {
		$scope.confirmDelete = function(id) {
			var modalInstance = $modal.open({
				templateUrl:  'ModalDelete.html',
				controller: SpiderRuleModalController,
				size:       '',
				resolve:    {
					id: function() { return id }
				}
			})
			modalInstance.result.then(
				// Closed
				function(id) {
					SpiderConfiguratorService.delete({id: id}, function(data) {
						if (data.Success) {
							$route.reload()
						} else {
							console.log("Deletion failed!", data)
						}
					})
				},
				// Dismissed
				function(reason) {
					console.log("Dismissed: %s", reason)
					// NOOP
				}
			)
		}
	}
])

var SpiderRuleModalController = [
	'$scope',
	'$modalInstance',
	'id',
	function($scope, $modalInstance, id) {
		$scope.ok = function() {
			$modalInstance.close(id)
		}
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel')
		}
	}
]

var goUrlToString = function(u) {
	var buf = ""
	if (u.Scheme != "") {
		buf += u.Scheme
		buf += ':'
	}
	if (u.Opaque != "") {
		buf += u.Opaque
	} else {
		if (u.Scheme != "" || u.Host != "") {
			buf += "//"
			if (u.Host != "") {
				buf += u.Host
			}
		}
		if (u.Path != "" && u.Path[0] != '/' && u.Host != "") {
			buf += '/'
		}
		buf += escape(u.Path)
	}
	if (u.RawQuery != "") {
		buf += '?'
		buf += u.RawQuery
	}
	if (u.Fragment != "") {
		buf += '#'
		buf += escape(u.Fragment)
	}
	return buf
}
