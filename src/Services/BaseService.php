<?php
namespace Aurlucef\GestionStock\Services;

abstract class BaseService {
    protected \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    protected function beginTransaction(): void {
        $this->pdo->beginTransaction();
    }

    protected function commit(): void {
        $this->pdo->commit();
    }

    protected function rollback(): void {
        $this->pdo->rollBack();
    }

    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    protected function execute(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    protected function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }
}
