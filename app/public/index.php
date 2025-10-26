<?php
declare(strict_types=1);

use App\Config;
use App\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Basic session start
if (session_status() === PHP_SESSION_NONE) {
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

// Simple helpers
function isLoggedIn(): bool { return isset($_SESSION['user']); }
function requireLogin(Response $response): Response {
    return $response
        ->withHeader('Location', '/login')
        ->withStatus(302);
}
function redirect(Response $response, string $to): Response {
    return $response->withHeader('Location', $to)->withStatus(302);
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}
function csrf_check(Request $request): bool {
    $parsed = (array)$request->getParsedBody();
    return isset($parsed['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $parsed['csrf_token']);
}

// Static assets route hint (web server should serve directly)

// Routes
$app->get('/login', function (Request $request, Response $response) {
    if (isLoggedIn()) {
        return redirect($response, '/');
    }
    ob_start();
    $error = $_GET['error'] ?? null;
    include __DIR__ . '/../src/views/login.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/login', function (Request $request, Response $response) use ($database) {
    $data = (array)$request->getParsedBody();
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    if (!csrf_check($request)) {
        return redirect($response, '/login?error=Invalid+session');
    }
    $user = $database->getUserByUsername($username);
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username']
        ];
        return redirect($response, '/');
    }
    return redirect($response, '/login?error=Invalid+credentials');
});

$app->get('/logout', function (Request $request, Response $response) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    return redirect($response, '/login');
});

$app->get('/', function (Request $request, Response $response) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    $todos = $database->getTodosByUserId($_SESSION['user']['id']);
    ob_start();
    include __DIR__ . '/../src/views/todos.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/todo/add', function (Request $request, Response $response) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    if (!csrf_check($request)) { return redirect($response, '/?error=csrf'); }
    $data = (array)$request->getParsedBody();
    $title = trim($data['title'] ?? '');
    $notes = trim($data['notes'] ?? '');
    if ($title !== '') {
        $database->addTodo($_SESSION['user']['id'], $title, $notes);
    }
    return redirect($response, '/');
});

$app->post('/todo/{id}/toggle', function (Request $request, Response $response, array $args) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    if (!csrf_check($request)) { return redirect($response, '/?error=csrf'); }
    $id = (int)$args['id'];
    $database->toggleTodo($_SESSION['user']['id'], $id);
    return redirect($response, '/');
});

$app->post('/todo/{id}/delete', function (Request $request, Response $response, array $args) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    if (!csrf_check($request)) { return redirect($response, '/?error=csrf'); }
    $id = (int)$args['id'];
    $database->deleteTodo($_SESSION['user']['id'], $id);
    return redirect($response, '/');
});

$app->get('/todo/{id}/edit', function (Request $request, Response $response, array $args) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    $id = (int)$args['id'];
    $todo = $database->getTodo($_SESSION['user']['id'], $id);
    if (!$todo) { return redirect($response, '/'); }
    ob_start();
    include __DIR__ . '/../src/views/todo_form.php';
    $response->getBody()->write(ob_get_clean());
    return $response;
});

$app->post('/todo/{id}/edit', function (Request $request, Response $response, array $args) use ($database) {
    if (!isLoggedIn()) { return requireLogin($response); }
    if (!csrf_check($request)) { return redirect($response, '/?error=csrf'); }
    $id = (int)$args['id'];
    $data = (array)$request->getParsedBody();
    $title = trim($data['title'] ?? '');
    $notes = trim($data['notes'] ?? '');
    $database->updateTodo($_SESSION['user']['id'], $id, $title, $notes);
    return redirect($response, '/');
});

// Run app
$app->run();
