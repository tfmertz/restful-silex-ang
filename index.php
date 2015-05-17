<?php

    require_once __DIR__.'/vendor/autoload.php';

    $app = new Silex\Application();

    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/views'
    ));

    $app->tasks = array(
        array("id" => "104", "task" => "Wash the Dog"),
        array("id" => "106", "task" => "Mow the lawn"),
        array("id" => "127", "task" => "Feed the chickens")
    );

    $app->get('/', function() use ($app) {
        return $app['twig']->render('index.twig');
    });

    $app->get('/api/tasks', function() use ($app) {

        return $app->json($app->tasks);
    });

    $app->get('/api/tasks/{id}', function($id) use ($app) {

        $tasks = [];
        foreach($app->tasks as $task) {
            if($task['id'] == $id) {
                array_push($tasks, $task);
            }
        }
        return $app->json($tasks);
    });

    $app->post('/api/tasks', function() use ($app) {
        $json = file_get_contents('php://input');
        $arr = json_decode($json, true);

        array_push($app->tasks, array("id" => rand(100,300), "task" => $arr['task']));

        return $app->json($app->tasks);
    });

    $app->run();

?>
