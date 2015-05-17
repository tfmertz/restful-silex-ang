<?php

    require_once __DIR__.'/vendor/autoload.php';
    require_once __DIR__.'/src/Task.php';

    $app = new Silex\Application();

    // Register twig
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views'
    ));

    // Register monolog to display errors
    $app->register(new Silex\Provider\MonologServiceProvider(), array(
        'monolog.logfile' => 'php://stderr'
    ));

    // Get the database information from heroku
    $dbopts = parse_url(getenv('DATABASE_URL'));

    $dsn = 'pgsql:dbname='.ltrim($dbopts["path"],'/').';host='.$dbopts["host"];

    $db_config = array(
        "port" => $dbopts['port']
    );

    //Create pdo object
    $DB = new PDO($dsn, $dbopts['user'], $dbopts['pass'], $db_config);

    //enable patch and delete requests
    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get('/', function() use ($app) {
        $app['monolog']->addDebug('logging output');
        return $app['twig']->render('index.twig');
    });

    $app->get('/api/tasks', function() use ($app) {
        //grab tasks from the database
        return $app->json(Task::getAll());
    });

    $app->get('/api/tasks/{id}', function($id) use ($app) {
        //get a single task from the database
        //returns false if none are found
        return $app->json(Task::findById($id));
    });

    //create a single new task
    $app->post('/api/tasks', function() use ($app) {
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);

        //create the new task and add it to the database
        $task = new Task($arr['id']);
        $task->save();

        //return the task added
        return $app->json(Task::findById($task->getId()));
    });

    //delete the completed tasks
    $app->delete('/api/tasks', function() use ($app) {
        return Task::deleteComplete();
    });

    //update a task to completed or incomplete
    $app->patch('/api/tasks/{id}', function($id, Request $req) use ($app) {
        //grab info, $params comes from the query string, everything else
        //is from angular's $http data
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);
        $params = $req->query->all();

        //check if we are completing the task
        if($params['complete'] == "true") {
            Task::completeTask($id);
        } else if($params['complete'] == "false") {
            Task::markIncomplete($id);
        }
        return $app->json(Task::findById($id));
    });

    //delete a single task
    $app->delete('/api/tasks/{id}', function($id) use ($app) {
        //get the info
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);
        Task::deleteTask($id);

        return $app->json(array("id" => $id));
    });

    $app->run();

?>
