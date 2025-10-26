<?php declare(strict_types=1);
namespace App;

/**
 * Simple configuration provider for the app.
 */
class Config {
    private function __construct(
        private readonly string $dbHost,
        private readonly int $dbPort,
        private readonly string $dbName,
        private readonly string $dbUser,
        private readonly string $dbPass
    ) {}

    public static function create(): self {
        return new self(
            '127.0.0.1',
            3306,
            'todo_demo',
            'root',
            ''
        );
    }

    public function dbHost(): string { return $this->dbHost; }
    public function dbPort(): int { return $this->dbPort; }
    public function dbName(): string { return $this->dbName; }
    public function dbUser(): string { return $this->dbUser; }
    public function dbPass(): string { return $this->dbPass; }
}
