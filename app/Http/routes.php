<?php 

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function(Request $request, Response $response) {

    return $this->view->render($response, 'home.twig', [
		'title'      => 'Home'
    ]);
});

// Define named route
$app->get('/hello/{name}', function ($request, $response, $args) {
    
    return $this->view->render($response, 'profile.twig', [
        'name' => $args['name']
    ]);
})->setName('profile');