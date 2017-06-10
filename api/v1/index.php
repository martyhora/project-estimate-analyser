<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';

$app = new \Slim\App;

$app->get('/pdf/{projectName}/{description}/{tasks}', function (Request $request, Response $response, $args) {
    $mpdf = new mPDF();

    $html = "<div>";

    $projectName = htmlspecialchars($args['projectName']);

    $html .= "<h2>{$projectName} project analysis</h2>";

    $taskDescription = htmlspecialchars($args['description']);

    $html .= "<p><b>Description: </b>{$taskDescription}</p>";

    $html .= "<table width='100%' cellspacing='0'>";

    $html .= "<tr><th align='left' style='padding: 5px 0px; border-bottom: 1px solid #000'>Task name</th><th align='right' style='padding: 5px 0;  border-bottom: 1px solid #000'>Time estimate [h]</th></tr>";

    $total = 0;

    foreach (json_decode($args['tasks']) as $task) {
        $html .= "<tr>
                  <th align='left' style='border-bottom: 1px solid #000; padding: 5px 0'>
                      {$task['title']}
                  </th>
                  <th align='right' style='border-bottom: 1px solid #000; padding: 5px 0'>
                      {$task['finalEstimate']}
                  </th>
              </tr>";

        foreach ($task['subTasks'] as $subTask) {
            $html .= "<tr>
                      <td align='left' style='padding: 5px 25px; border-bottom: 1px solid #000; '>
                          {$subTask['title']}
                      </td>
                      <td align='right' style='padding: 5px 0; border-bottom: 1px solid #000; '>
                          {$subTask['estimate']}
                      </td>
                  </tr>";

            $total += $subTask['estimate'];
        }

        if (count($task['subTasks']) === 0) {
            $total += $task['finalEstimate'];
        }
    }

    $html .= "<tr><th align='left' style='padding: 5px 0px; border-top: 1px solid #000;'>Total time</th><th align='right' style='padding: 5px 0;  border-top: 1px solid #000;'>{$total}</th></tr>";

    $html .= "</table>";

    $html .= "</div>";

    $mpdf->WriteHTML($html);

    $pdf = $mpdf->Output("{$projectName} analysis.pdf", "S");

    return $response->withHeader('Content-type', 'application/pdf')
                    ->withHeader('Content-disposition', 'attachment; filename=' . "{$projectName} analysis.pdf")
                    ->write($pdf);
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

