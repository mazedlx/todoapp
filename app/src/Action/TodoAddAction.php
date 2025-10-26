<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TodoAddAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        if (!$this->isLoggedIn()) { return $this->requireLogin($response); }
        if (!$this->csrfCheck($request)) { return $this->redirect($response, '/?error=csrf'); }

        $data = (array)$request->getParsedBody();
        $title = trim((string)($data['title'] ?? ''));
        $notes = trim((string)($data['notes'] ?? ''));
        if ($title !== '') {
            $this->db->addTodo((int)$_SESSION['user']['id'], $title, $notes !== '' ? $notes : null);
        }
        return $this->redirect($response, '/');
    }
}
