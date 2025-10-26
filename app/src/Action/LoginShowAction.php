<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LoginShowAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if ($this->isLoggedIn()) {
            return $this->redirect($response, '/');
        }
        $this->ensureCsrfToken();
        $query = $request->getQueryParams();
        $error = $query['error'] ?? null;
        return $this->render($response, 'login.php', compact('error'));
    }
}
