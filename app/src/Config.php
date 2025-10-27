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
        $env = parse_ini_file('../.env');
        return new self(
            $env['DB_HOST'] ?? 'localhost',
            isset($env['DB_PORT']) ? (int)$env['DB_PORT'] : 3306,
            $env['DB_NAME'] ?? 'app_db',
            $env['DB_USER'] ?? 'app_user',
            $env['DB_PASS'] ?? 'secret_password'
        );
    }

    public function dbHost(): string { return $this->dbHost; }
    public function dbPort(): int { return $this->dbPort; }
    public function dbName(): string { return $this->dbName; }
    public function dbUser(): string { return $this->dbUser; }
    public function dbPass(): string { return $this->dbPass; }
}
