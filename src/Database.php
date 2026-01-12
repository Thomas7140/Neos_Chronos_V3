<?php
/**
 * Chronos V3 - Database Connection Class
 * PHP 8+ with PDO for MariaDB 11 compatibility
 */

namespace Chronos;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private array $config;

    private function __construct(array $config)
    {
        $this->config = $config;
        $this->connect();
    }

    /**
     * Get singleton instance of Database
     */
    public static function getInstance(array $config = null): Database
    {
        if (self::$instance === null) {
            if ($config === null) {
                throw new \RuntimeException('Database configuration required for first initialization');
            }
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * Establish database connection using PDO
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['port'] ?? 3306,
                $this->config['name'],
                $this->config['charset'] ?? 'utf8mb4'
            );

            $this->connection = new PDO(
                $dsn,
                $this->config['user'],
                $this->config['pass'],
                $this->config['options'] ?? []
            );
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Execute a query and return all results
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute a query and return single row
     */
    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Execute an insert/update/delete query
     */
    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Prevent cloning of singleton
     */
    private function __clone() {}

    /**
     * Prevent unserialization of singleton
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
