<?php
declare(strict_types=1);

use App\Config;
use App\Database;
use App\Action\HomeAction;
use App\Action\LoginShowAction;
use App\Action\LoginSubmitAction;
use App\Action\LogoutAction;
use App\Action\TodoAddAction;
use App\Action\TodoToggleAction;
use App\Action\TodoDeleteAction;
use App\Action\TodoEditShowAction;
use App\Action\TodoEditSubmitAction;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Basic session start
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.save_handler', 'redis');
    ini_set('session.save_path', 'tcp://127.0.0.1:6379');
    session_start();
}

// Application configuration
$config = Config::create();

// Create containerless Slim app
$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Dependency: Database (store in $container-like registry)
$database = new Database(
    $config->dbHost(),
    $config->dbPort(),
    $config->dbName(),
    $config->dbUser(),
    $config->dbPass()
);

// Ensure schema exists (creates DB if needed, tables and default user)
$database->initialize();

// Routes using Action classes
$app->get('/login', new LoginShowAction($database));
$app->post('/login', new LoginSubmitAction($database));
$app->get('/logout', new LogoutAction($database));
$app->get('/', new HomeAction($database));
$app->post('/todo/add', new TodoAddAction($database));
$app->post('/todo/{id}/toggle', new TodoToggleAction($database));
$app->post('/todo/{id}/delete', new TodoDeleteAction($database));
$app->get('/todo/{id}/edit', new TodoEditShowAction($database));
$app->post('/todo/{id}/edit', new TodoEditSubmitAction($database));

// Run app
$app->run();
