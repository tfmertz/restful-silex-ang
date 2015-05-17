angular.module('silexApp', [])
  .config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
  })
  .controller('silexCtrl', function($scope, $http) {
    $scope.word = "Waiting for input";

    $scope.tasks = [];

    //get all of the tasks
    $scope.getTasks = function() {
      $http.get('/api/tasks')
        .success(function(data) {
          $scope.tasks = data;
          $scope.word = "Tasks received";
          $scope.response = data;
          console.log(data);
        })
        .error(function(data) {
          $scope.word = "Failed to get tasks";
          $scope.response = data;
        });
    };

    //get a single task
    $scope.getTask = function(search_id) {
      if(search_id) {
        $http.get('/api/tasks/' + search_id)
          .success(function(data) {
            if(!data) {
              $scope.word = "Task not found!";
              $scope.tasks = [];
              $scope.response = data;
            } else {
              $scope.word = "Task found";
              $scope.tasks = [data];
              $scope.response = data;
              console.log(data);
            }
          });
      } else {
        $scope.word = "Search field blank!";
        $scope.response = "";
      }
    };

    //add a single task
    $scope.addTask = function(id) {
      $http.post('/api/tasks', {id: id})
        .success(function(data) {
          $scope.addtask = null;
          $scope.tasks.push(data);
          $scope.response = data;
          $scope.word = "Added task to array";
        })
        .error(function(data) {
          $scope.word = "Failed to add task";
          $scope.response = data;
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
          //remove the task from angulars array
          $scope.removeFromTasks(task);
          //readd it with the changes in the database
          $scope.tasks.push(data);
          $scope.response = data;
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be completed";
          $scope.response = data;
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
          $scope.removeFromTasks(task);
          $scope.tasks.push(data);
          $scope.response = data;
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be marked incomplete";
          $scope.response = data;
        });
    };

    //delete a single tasks from the database
    $scope.deleteTask = function(task) {
      $http.delete('/api/tasks/' + task.id)
        .success(function(data) {
          console.log(data);
          $scope.word = "Task deleted";
          $scope.response = data;
          //remove it from the array so it disappears immediately
          $scope.removeFromTasks(task);
        })
        .error(function(data) {
          $scope.word = "Error, task couldn't be deleted";
          $scope.response = data;
        });
    };

    //delete all completed tasks
    $scope.deleteAllComplete = function(completedTasks) {
      $http.delete('/api/tasks')
        .success(function(data) {
          console.log(data);
          $scope.word = "Completed tasks removed";
          $scope.response = data;
          //remove the completed tasks from the array
          completedTasks.forEach(function(element) {
            if(element.complete) {
              $scope.removeFromTasks(element);
            }
          });
          console.log(completedTasks);
        });
    };

    //snips a single task out of the tasks object
    $scope.removeFromTasks = function(task) {
      var index = $scope.tasks.indexOf(task);
      $scope.tasks.splice(index, 1);
    };
  });
