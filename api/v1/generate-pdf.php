<?php
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new mPDF();

$html = "<div>";

$data = json_decode($_GET['data'], true);

$projectName = htmlspecialchars($data['projectName']);

$html .= "<h2>{$projectName} project analysis</h2>";

$taskDescription = htmlspecialchars($data['taskDescription']);

$html .= "<p><b>Description: </b>{$taskDescription}</p>";

$html .= "<table width='100%' cellspacing='0'>";

$html .= "<tr><th align='left' style='padding: 5px 0px; border-bottom: 1px solid #000'>Task name</th><th align='right' style='padding: 5px 0;  border-bottom: 1px solid #000'>Time estimate [h]</th></tr>";

$total = 0;

foreach ($data['tasks'] as $task) {
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

$mpdf->Output("{$projectName} analysis.pdf", "D");