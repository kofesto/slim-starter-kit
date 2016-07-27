<?php 

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function(Request $request, Response $response) {

	$this->logger->addInfo("Something interesting happened");
	
    return $this->view->render($response, 'home.twig', [
        'title' => 'Home'
    ]);
});

// Define named route
$app->get('/hello/{name}', function ($request, $response, $args) {
    
    return $this->view->render($response, 'profile.twig', [
        'name' => $args['name']
    ]);
})->setName('profile');