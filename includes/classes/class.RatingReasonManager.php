<?php
class RatingReasonManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(int $criterio_id, float $rango_min, float $rango_max, string $razon): bool {
        $stmt = $this->db->prepare("
            INSERT INTO " . TABLE_RATING_REASONS . " 
            (" . RATING_REASONS_CRITERIO_ID . ", " . RATING_REASONS_RANGO_MIN . ", " . RATING_REASONS_RANGO_MAX . ", " . RATING_REASONS_RAZON . ")
            VALUES (:criterio_id, :rango_min, :rango_max, :razon)
        ");
        return $stmt->execute([
            ':criterio_id' => $criterio_id,
            ':rango_min' => $rango_min,
            ':rango_max' => $rango_max,
            ':razon' => $razon
        ]);
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM " . TABLE_RATING_REASONS . " ORDER BY " . RATING_REASONS_CRITERIO_ID . ", " . RATING_REASONS_RANGO_MIN);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM " . TABLE_RATING_REASONS . " WHERE " . RATING_REASONS_ID . " = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByCriterio(int $criterio_id): array {
        $stmt = $this->db->prepare("SELECT * FROM " . TABLE_RATING_REASONS . " WHERE " . RATING_REASONS_CRITERIO_ID . " = :criterio_id ORDER BY " . RATING_REASONS_RANGO_MIN);
        $stmt->execute([':criterio_id' => $criterio_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findReason(int $criterio_id, float $calificacion): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_REASONS . "
            WHERE " . RATING_REASONS_CRITERIO_ID . " = :criterio_id AND :calificacion BETWEEN " . RATING_REASONS_RANGO_MIN . " AND " . RATING_REASONS_RANGO_MAX . "
            LIMIT 1
        ");
        $stmt->execute([
            ':criterio_id' => $criterio_id,
            ':calificacion' => $calificacion
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM " . TABLE_RATING_REASONS . " WHERE " . RATING_REASONS_ID . " = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function update(int $id, float $rango_min, float $rango_max, string $razon): bool {
        $stmt = $this->db->prepare("
            UPDATE " . TABLE_RATING_REASONS . "
            SET " . RATING_REASONS_RANGO_MIN . " = :rango_min, " . RATING_REASONS_RANGO_MAX . " = :rango_max, " . RATING_REASONS_RAZON . " = :razon
            WHERE " . RATING_REASONS_ID . " = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':rango_min' => $rango_min,
            ':rango_max' => $rango_max,
            ':razon' => $razon
        ]);
    }
}