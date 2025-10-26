<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TodoToggleAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if (!$this->isLoggedIn()) { return $this->requireLogin($response); }
        if (!$this->csrfCheck($request)) { return $this->redirect($response, '/?error=csrf'); }
        $id = (int)($args['id'] ?? 0);
        if ($id > 0) {
            $this->db->toggleTodo((int)$_SESSION['user']['id'], $id);
        }
        return $this->redirect($response, '/');
    }
}
