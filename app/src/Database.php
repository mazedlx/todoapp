<?php
declare(strict_types=1);

namespace App;

use PDO;

class Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly string $host,
        private readonly int $port,
        private readonly string $dbName,
        private readonly string $user,
        private readonly string $pass,
    ) {}

    public function initialize(): void
    {
        // 1) Ensure database exists
        $dsnBase = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $this->host, $this->port);
        $pdoBase = new PDO($dsnBase, $this->user, $this->pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $dbNameQuoted = str_replace('`', '``', $this->dbName);
        $pdoBase->exec("CREATE DATABASE IF NOT EXISTS `{$dbNameQuoted}` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");

        // 2) Connect to app DB
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $this->host, $this->port, $this->dbName);
        $this->pdo = new PDO($dsn, $this->user, $this->pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // 3) Ensure tables
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(64) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS todos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            notes TEXT NULL,
            is_done TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_todos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

        // 4) Ensure default user
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute(['user']);
        if (!$stmt->fetch()) {
            $hash = password_hash('secure', PASSWORD_DEFAULT);
            $ins = $this->pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
            $ins->execute(['user', $hash]);
        }
    }

    private function pdo(): PDO
    {
        if (!$this->pdo) {
            $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $this->host, $this->port, $this->dbName);
            $this->pdo = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return $this->pdo;
    }

    // User
    public function getUserByUsername(string $username): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Todos
    public function getTodosByUserId(int $userId): array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM todos WHERE user_id = ? ORDER BY is_done ASC, created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function addTodo(int $userId, string $title, ?string $notes): void
    {
        $stmt = $this->pdo()->prepare('INSERT INTO todos (user_id, title, notes) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $title, $notes]);
    }

    public function getTodo(int $userId, int $id): ?array
    {
        $stmt = $this->pdo()->prepare('SELECT * FROM todos WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateTodo(int $userId, int $id, string $title, ?string $notes): void
    {
        $stmt = $this->pdo()->prepare('UPDATE todos SET title = ?, notes = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$title, $notes, $id, $userId]);
    }

    public function toggleTodo(int $userId, int $id): void
    {
        $todo = $this->getTodo($userId, $id);
        if ($todo) {
            $new = $todo['is_done'] ? 0 : 1;
            $stmt = $this->pdo()->prepare('UPDATE todos SET is_done = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$new, $id, $userId]);
        }
    }

    public function deleteTodo(int $userId, int $id): void
    {
        $stmt = $this->pdo()->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }
}
