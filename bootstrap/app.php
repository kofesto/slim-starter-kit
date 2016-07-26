<?php 

$dotenv = new Dotenv\Dotenv(BASE);
$dotenv->load();

$app = new \Slim\App();

return $app;