<?php 

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function(Request $request, Response $response) {

	$users = User::all();

    return $this->view->render($response, 'home.twig', [
		'title' => 'Home',
		'users' =>	$users
    ]);
});

// Define named route
$app->get('/hello/{name}', function ($request, $response, $args) {
    
    return $this->view->render($response, 'profile.twig', [
        'name' => $args['name']
    ]);
})->setName('profile');

$app->get('/foo', function ($req, $res, $args) {
    // Set flash message for next request
    $this->flash->addMessage('Test', 'This is a message');

    // Redirect
    return $res->withStatus(302)->withHeader('Location', '/bar');
});

$app->get('/bar', function ($req, $res, $args) {
    // Get flash messages from previous request
    $messages = $this->flash->getMessages();
    print_r($messages);
});