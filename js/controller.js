angular.module('silexApp', [])
  .config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
  })
  .controller('silexCtrl', function($scope, $http) {
    $scope.word = "Waiting for input";

    $scope.tasks = {};

    $scope.getTasks = function() {
      $http.get('/api/tasks')
        .success(function(data) {
          $scope.tasks = data;
          $scope.word = "Tasks received";
        })
        .error(function(data) {
          $scope.word = "Failed to get tasks";
        });
    };

    $scope.getTask = function(task) {
      if(task) {
        $http.get('/api/tasks/' + task)
          .success(function(data) {
            if(Object.keys(data).length === 0) {
              $scope.word = "Task not found!";
              $scope.tasks = {};
            } else {
              $scope.word = "Task found";
              $scope.tasks = data;
            }
          });
      } else {
        $scope.word = "Task field blank!";
      }
    };

    $scope.addTask = function(task) {
      $http.post('/api/tasks', {task: task})
        .success(function(data) {
          $scope.tasks = data;
          $scope.word = "Added task to array";
        })
        .error(function(data) {
          $scope.word = "Failed to add task";
        });
    };
  });
