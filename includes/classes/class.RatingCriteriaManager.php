<?php

class RatingCriteriaManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Crear un nuevo criterio
    public function create(string $nombre, string $nivel_aplica, int $orden): bool {
        $stmt = $this->db->prepare("
            INSERT INTO " . TABLE_RATING_CRITERIA . " (nombre, nivel_aplica, orden)
            VALUES (:nombre, :nivel_aplica, :orden)
        ");
        return $stmt->execute([
            ':nombre' => $nombre,
            ':nivel_aplica' => $nivel_aplica,
            ':orden' => $orden
        ]);
    }

    // Obtener todos los criterios ordenados por el campo 'orden'
    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT * FROM " . TABLE_RATING_CRITERIA . " ORDER BY orden ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un criterio por su ID
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_CRITERIA . " WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Obtener criterios por nivel de aplicación
    public function getByNivel(string $nivel): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_CRITERIA . " WHERE nivel_aplica = :nivel_aplica ORDER BY orden ASC
        ");
        $stmt->execute([':nivel_aplica' => $nivel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar criterios por nombre (coincidencia parcial)
    public function searchByNombre(string $search): array {
        $stmt = $this->db->prepare("
            SELECT * FROM " . TABLE_RATING_CRITERIA . " WHERE nombre LIKE :search ORDER BY orden ASC
        ");
        $stmt->execute([':search' => "%$search%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar un criterio existente
    public function update(int $id, string $nombre, string $nivel_aplica, int $orden): bool {
        $stmt = $this->db->prepare("
            UPDATE " . TABLE_RATING_CRITERIA . "
            SET nombre = :nombre, nivel_aplica = :nivel_aplica, orden = :orden
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $nombre,
            ':nivel_aplica' => $nivel_aplica,
            ':orden' => $orden
        ]);
    }

    // Eliminar un criterio
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("
            DELETE FROM " . TABLE_RATING_CRITERIA . " WHERE id = :id
        ");
        return $stmt->execute([':id' => $id]);
    }

    // Obtener el siguiente valor posible para 'orden'
    public function getNextOrden(): int {
        $stmt = $this->db->query("
            SELECT MAX(orden) as max_orden FROM " . TABLE_RATING_CRITERIA
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['max_orden']) ? (int)$result['max_orden'] + 1 : 1;
    }

    // Verificar si un nombre ya existe (para validaciones únicas)
    public function existsByNombre(string $nombre): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM " . TABLE_RATING_CRITERIA . " WHERE nombre = :nombre
        ");
        $stmt->execute([':nombre' => $nombre]);
        return $stmt->fetchColumn() > 0;
    }
}