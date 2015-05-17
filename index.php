<?php

    require_once __DIR__.'/vendor/autoload.php';
    require_once __DIR__.'/src/Task.php';

    $app = new Silex\Application();

    // Register twig
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views'
    ));

    //Create pdo object
    $DB = new PDO('pgsql:host=localhost;dbname=tommertz_tasks;', 'tom', '1234');

    //enable patch and delete requests
    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app->get('/', function() use ($app) {
        return $app['twig']->render('index.twig');
    });

    $app->get('/api/tasks', function() use ($app) {
        //grab tasks from the database
        return $app->json(Task::getAll());
    });

    $app->get('/api/tasks/{id}', function($id) use ($app) {
        //get a single task from the database
        return $app->json(Task::findById($id));
    });

    $app->post('/api/tasks', function() use ($app) {
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);

        //create the new task and add it to the database
        $task = new Task($arr['id']);
        $task->save();

        //return the task added
        return $app->json(Task::getAll());
    });

    $app->patch('/api/tasks/{id}', function($id, Request $req) use ($app) {
        //grab info, $params comes from the query string, everything else
        //is from angular's $http data
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);
        $params = $req->query->all();

        //check if we are completing the task
        if($params['complete']) {
            Task::completeTask($id);
        }
        return $app->json(Task::findById($id));
    });

    $app->delete('/api/tasks/{id}', function($id) use ($app) {
        //get the info
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);
        Task::deleteTask($id);

        return $id;
    });

    $app->run();

?>
