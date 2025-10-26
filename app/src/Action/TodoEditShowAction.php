<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TodoEditShowAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if (!$this->isLoggedIn()) { return $this->requireLogin($response); }
        $id = (int)($args['id'] ?? 0);
        if ($id <= 0) { return $this->redirect($response, '/'); }
        $todo = $this->db->getTodo((int)$_SESSION['user']['id'], $id);
        if (!$todo) { return $this->redirect($response, '/'); }
        return $this->render($response, 'todo_form.php', compact('todo'));
    }
}
