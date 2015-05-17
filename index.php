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
        $task = new Task($arr['task']);
        $task->save();

        return $app->json($app->tasks);
    });

    $app->run();

?>
