<?php
declare(strict_types=1);

namespace App\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class LoginSubmitAction extends AbstractAction
{
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $data = (array)$request->getParsedBody();
        $username = trim((string)($data['username'] ?? ''));
        $password = (string)($data['password'] ?? '');

        if (!$this->csrfCheck($request)) {
            return $this->redirect($response, '/login?error=Invalid+session');
        }

        $user = $this->db->getUserByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];
            return $this->redirect($response, '/');
        }

        return $this->redirect($response, '/login?error=Invalid+credentials');
    }
}
