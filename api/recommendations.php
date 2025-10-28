<?php
require_once __DIR__ . '/../app/controllers/StatisticsController.php';

$controller = new StatisticsController();
$controller->getRecommendations();
?>