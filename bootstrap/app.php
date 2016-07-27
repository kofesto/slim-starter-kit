<?php 
// Start PHP session
session_start();

use Slim\App;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

use Illuminate\Database\Capsule\Manager as Capsule;

// set timezone for timestamps etc
date_default_timezone_set('UTC');

if(file_exists(BASE.'/.env'))	{
	$dotenv = new Dotenv(BASE);
	$dotenv->load();
}

// Create and configure Slim app
$configuration = [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'addContentLengthHeader'            => false,
        'displayErrorDetails'               => (getenv('APP_DEBUG') == 'false') ? 0 : 1,

        'logger' => [
            'name'  => 'slim-app',
            'level' => Monolog\Logger::DEBUG,
            'path'  => BASE . '/storage/logs/slim.log',
        ],

		'db' => [
            'driver'    => getenv('DB_CONNECTION'),
            'host'      => getenv('DB_HOST'),
            'database'  => getenv('DB_DATABASE'),
            'username'  => getenv('DB_USERNAME'),
            'password'  => getenv('DB_PASSWORD'),
            'port'  	=> getenv('DB_PORT'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],
];
$container = new \Slim\Container($configuration);

$capsule = new capsule;
$capsule->addConnection($container['settings']['db']);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {

    return $capsule;
};

$container['csrf'] = function ($c) {
    return new Guard;
};

$container['flash'] = function () {
    return new Messages();
};


$container['logger'] = function ($c) {
    
    $settings   = $c->get('settings')['logger'];

    $dateFormat = "Y n j, g:i a";
    $output     = "%datetime% > %level_name% > %message% %context% %extra%\n";
    $formatter  = new LineFormatter($output, $dateFormat);

    $stream = new StreamHandler($settings['path'], $settings['level']);
    $stream->setFormatter($formatter);

    $logger = new Logger($settings['name']);
    $logger->pushHandler($stream);

    return $logger;
};

// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(BASE . '/resources/views', [
        'cache' => BASE . '/storage/framework/views'
    ]);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

// Prepare app
$app = new App($container);

// Add middlewares to the application
$app->add($container->get('csrf'));

require_once BASE . '/routes/web.php';

return $app;