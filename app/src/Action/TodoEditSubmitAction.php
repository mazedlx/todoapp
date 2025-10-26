<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TodoEditSubmitAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if (!$this->isLoggedIn()) { return $this->requireLogin($response); }
        if (!$this->csrfCheck($request)) { return $this->redirect($response, '/?error=csrf'); }

        $id = (int)($args['id'] ?? 0);
        if ($id <= 0) { return $this->redirect($response, '/'); }

        $data = (array)$request->getParsedBody();
        $title = trim((string)($data['title'] ?? ''));
        $notes = trim((string)($data['notes'] ?? ''));
        $this->db->updateTodo((int)$_SESSION['user']['id'], $id, $title, $notes !== '' ? $notes : null);
        return $this->redirect($response, '/');
    }
}
