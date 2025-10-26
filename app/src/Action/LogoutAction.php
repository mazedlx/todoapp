<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LogoutAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', (int)(time() - 42000),
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool)($params['secure'] ?? false),
                (bool)($params['httponly'] ?? false)
            );
        }
        session_destroy();
        return $this->redirect($response, '/login');
    }
}
