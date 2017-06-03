<?php
header("Content-type: application/json");
header("Content-Disposition: attachment; filename=project-analysis.json");

echo $_GET['data'];