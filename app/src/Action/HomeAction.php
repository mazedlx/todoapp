<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HomeAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if (!$this->isLoggedIn()) {
            return $this->requireLogin($response);
        }
        $todos = $this->db->getTodosByUserId((int)$_SESSION['user']['id']);
        return $this->render($response, 'todos.php', compact('todos'));
    }
}
