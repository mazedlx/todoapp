<?php
declare(strict_types=1);

namespace App\Action;

use App\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractAction
{
    public function __construct(protected readonly Database $db)
    {
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user']);
    }

    protected function requireLogin(Response $response): Response
    {
        return $this->redirect($response, '/login');
    }

    protected function redirect(Response $response, string $to): Response
    {
        return $response->withHeader('Location', $to)->withStatus(302);
    }

    protected function ensureCsrfToken(): void
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
    }

    protected function csrfCheck(Request $request): bool
    {
        $parsed = (array)$request->getParsedBody();
        return isset($parsed['csrf_token'], $_SESSION['csrf_token'])
            && hash_equals((string)$_SESSION['csrf_token'], (string)$parsed['csrf_token']);
    }

    /**
     * Render a PHP view file from src/views, passing variables.
     *
     * @param string $view Relative filename under src/views (e.g., 'login.php').
     * @param array<string,mixed> $vars Variables to be extracted for the view scope.
     */
    protected function render(Response $response, string $view, array $vars = []): Response
    {
        $this->ensureCsrfToken();
        $path = __DIR__ . '/../views/' . ltrim($view, '/');
        if (!is_file($path)) {
            $response->getBody()->write('View not found: ' . htmlspecialchars($view));
            return $response->withStatus(500);
        }
        extract($vars, EXTR_SKIP);
        ob_start();
        include $path;
        $response->getBody()->write((string)ob_get_clean());
        return $response;
    }
}
