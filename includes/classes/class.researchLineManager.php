<?php

class researchLineManager {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . DB_RESEARCH_LINES . " ORDER BY " . DB_RESEARCH_LINE_ID . " ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllActivas(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . DB_RESEARCH_LINES . " WHERE " . DB_RESEARCH_LINE_ESTADO . " = 'activo'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM " . DB_RESEARCH_LINES . " WHERE " . DB_RESEARCH_LINE_ID . " = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ?: null;
    }

    public function create(string $nombre): bool {
        $stmt = $this->pdo->prepare("INSERT INTO " . DB_RESEARCH_LINES . " (" . DB_RESEARCH_LINE_NOMBRE . ") VALUES (:nombre)");
        return $stmt->execute([':nombre' => $nombre]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM " . DB_RESEARCH_LINES . " WHERE " . DB_RESEARCH_LINE_ID . " = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function update(int $id, string $nombre): bool {
        $stmt = $this->pdo->prepare("UPDATE " . DB_RESEARCH_LINES . " SET " . DB_RESEARCH_LINE_NOMBRE . " = :nombre WHERE " . DB_RESEARCH_LINE_ID . " = :id");
        return $stmt->execute([':id' => $id, ':nombre' => $nombre]);
    }

    public function deactivate(int $id): bool {
            $stmt = $this->pdo->prepare("UPDATE " . DB_RESEARCH_LINES . " SET " . DB_RESEARCH_LINE_ESTADO . " = 'inactivo' WHERE " . DB_RESEARCH_LINE_ID . " = :id");
            return $stmt->execute([':id' => $id]);
        }

    public function restore(int $id): bool {
        $stmt = $this->pdo->prepare("UPDATE " . DB_RESEARCH_LINES . " SET " . DB_RESEARCH_LINE_ESTADO . " = 'activo' WHERE " . DB_RESEARCH_LINE_ID . " = :id");
        return $stmt->execute([':id' => $id]);
    }
}