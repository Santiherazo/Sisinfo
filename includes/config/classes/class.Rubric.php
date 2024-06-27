<?php

class Rubric {
    private $db;
    private $user;

    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }

    public function sendRubric() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->sendResponse(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            $this->sendResponse(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
            return;
        }

        if (empty($this->user)) {
            $this->sendResponse(['success' => false, 'error' => 'El usuario no está definido']);
            return;
        }

        $requiredFields = [
            'proyecto_id', 'titleProject', 'introduction',
            'problemStatement', 'justify', 'targets',
            'theorical', 'methodology', 'mainResults',
            'support', 'rating', 'generalComments'
        ];

        if (!$this->validateData($data, $requiredFields)) {
            $this->sendResponse(['success' => false, 'error' => 'Alguno de los datos requeridos es nulo']);
            return;
        }

        try {
            $this->db->beginTransaction();

            $insertStmt = $this->db->prepare('INSERT INTO calificaciones 
                (idProject, assessor, titleProject, feedProject, introduction, feedIntroduction, 
                problemStatement, feedStatement, justify, feedJustify, targets, feedTargets, theorical, 
                feedTheorical, methodology, feedMethodology, mainResults, feedMainresults, support, 
                feedSupport, rating, generalComments) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

            $insertSuccess = $insertStmt->execute([
                $data['proyecto_id'], $this->user, $data['titleProject'], $data['feedProject'] ?? null,
                $data['introduction'], $data['feedIntroduction'] ?? null, $data['problemStatement'],
                $data['feedStatement'] ?? null, $data['justify'], $data['feedJustify'] ?? null, $data['targets'],
                $data['feedTargets'] ?? null, $data['theorical'], $data['feedTheorical'] ?? null, $data['methodology'],
                $data['feedMethodology'] ?? null, $data['mainResults'], $data['feedMainresults'] ?? null, $data['support'],
                $data['feedSupport'] ?? null, $data['rating'], $data['generalComments']
            ]);

            if (!$insertSuccess) {
                $this->db->rollBack();
                $this->sendResponse(['success' => false, 'error' => 'Error al insertar los datos']);
                return;
            }

            $updateStmt = $this->db->prepare('UPDATE proyectos SET calificado = calificado + 1 WHERE id = ?');
            $updateSuccess = $updateStmt->execute([$data['proyecto_id']]);

            if (!$updateSuccess) {
                $this->db->rollBack();
                $this->sendResponse(['success' => false, 'error' => 'Error al actualizar el proyecto']);
                return;
            }

            $this->db->commit();
            $this->sendResponse(['success' => true]);
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->sendResponse(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function validateData($data, $fields) {
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }

    private function sendResponse($response) {
        echo json_encode($response);
    }
}