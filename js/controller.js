angular.module('silexApp', [])
  .config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
  })
  .controller('silexCtrl', function($scope, $http) {
    $scope.word = "Waiting for input";

    $scope.tasks = {};

    //get all of the tasks
    $scope.getTasks = function() {
      $http.get('/api/tasks')
        .success(function(data) {
          $scope.tasks = data;
          $scope.word = "Tasks received";
          console.log(data);
        })
        .error(function(data) {
          $scope.word = "Failed to get tasks";
        });
    };

    //get a single task
    $scope.getTask = function(task) {
      if(task) {
        $http.get('/api/tasks/' + task)
          .success(function(data) {
            if(Object.keys(data).length === 0) {
              $scope.word = "Task not found!";
              $scope.tasks = {};
            } else {
              $scope.word = "Task found";
              $scope.tasks = {0: data};
              console.log(data);
            }
          });
      } else {
        $scope.word = "Task field blank!";
      }
    };

    //add a single task
    $scope.addTask = function(id) {
      $http.post('/api/tasks', {id: id})
        .success(function(data) {
          $scope.addtask = null;
          $scope.tasks = data;
          $scope.word = "Added task to array";
        })
        .error(function(data) {
          $scope.word = "Failed to add task";
        });
    };

    //check a task as complete
    $scope.completeTask = function(task) {
      //config object
      var config = {
        params: {
          complete: true
        }
      };
      $http.patch('/api/tasks/' + task.id, task, config)
        .success(function(data) {
          console.log(data);
          $scope.word = "Task completed";
          $scope.tasks = data;
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be completed";
        });
    };

    //removes a task from the completed list
    $scope.removeCompleteTask = function(task) {
      //config object
      var config = {
        params: {
          complete: false
        }
      };
      $http.patch('/api/tasks/' + task.id, task, config)
        .success(function(data) {
          console.log(data);
          $scope.word = "Task marked as incomplete";
          $scope.tasks = data;
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be marked incomplete";
        });
    };

    //delete a single tasks from the database
    $scope.deleteTask = function(task) {
      $http.delete('/api/tasks/' + task.id)
        .success(function(data) {
          console.log(data);
          $scope.word = "Task deleted";
          //remove it from the array so it disappears immediately
          $scope.removeFromTasks(task);
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be deleted";
        });
    };

    //snips a single task out of the tasks object
    $scope.removeFromTasks = function(task) {
      var index = $scope.tasks.indexOf(task);
      $scope.tasks.splice(index, 1);
    };
  });
