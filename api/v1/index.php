<?php

use ProjectAnalyser\EstimateReport;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';

$app = new \Slim\App;

$estimateReport = new EstimateReport();

$app->get('/pdf/{projectName}/{description}/{tasks}', function (Request $request, Response $response, $args) use ($estimateReport) {
    return $response->withHeader('Content-type', 'application/pdf')
                    ->withHeader('Content-disposition', 'attachment; filename=' . "{$args['projectName']} analysis.pdf")
                    ->write($estimateReport->createPdf($args['projectName'], $args['description'], json_decode(urldecode($args['tasks']), true)));
});

$app->get('/save-json/{projectName}/{description}/{tasks}', function (Request $request, Response $response, $args) {
    $tasks = json_decode($args['tasks'], true);

    $result = [
        'projectName' => $args['projectName'],
        'taskDescription' => $args['description'],
        'tasks' => $tasks,
    ];

    return $response->withHeader('Content-type', 'application/json')
                    ->withHeader('Content-Disposition', 'attachment; filename=' . "{$args['projectName']}-project-analysis.json")
                    ->write(json_encode($result));
});

$app->run();

