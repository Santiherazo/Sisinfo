<?php 

class Rubric {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function sendRubric() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $data = json_decode(file_get_contents("php://input"), true);

            if ($data) {
                $requiredFields = [
                    'proyecto_id', 'assessor', 'titleProject', 'feedProject', 'introduction', 'feedIntroduction', 
                    'problemStatement', 'feedStatement', 'justify', 'feedJustify', 'targets', 'feedTargets', 
                    'theorical', 'feedTheorical', 'methodology', 'feedMethodology', 'mainResults', 'feedMainresults', 
                    'support', 'feedSupport', 'rating', 'generalComments'
                ];

                if ($this->validateData($data, $requiredFields)) {
                    try {
                        $this->db->beginTransaction();
                        $insertStmt = $this->db->prepare('INSERT INTO calificaciones 
                            (idProject, assessor, titleProject, feedProject, introduction, feedIntroduction, 
                            problemStatement, feedStatement, justify, feedJustify, targets, feedTargets, theorical, 
                            feedTheorical, methodology, feedMethodology, mainResults, feedMainresults, support, 
                            feedSupport, rating, generalComments) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

                        $insertSuccess = $insertStmt->execute([
                            $data['proyecto_id'], $data['assessor'], $data['titleProject'], $data['feedProject'], 
                            $data['introduction'], $data['feedIntroduction'], $data['problemStatement'], 
                            $data['feedStatement'], $data['justify'], $data['feedJustify'], $data['targets'], 
                            $data['feedTargets'], $data['theorical'], $data['feedTheorical'], $data['methodology'], 
                            $data['feedMethodology'], $data['mainResults'], $data['feedMainresults'], $data['support'], 
                            $data['feedSupport'], $data['rating'], $data['generalComments']
                        ]);

                        if ($insertSuccess) {
                            $updateStmt = $this->db->prepare('UPDATE proyectos SET calificacion = ?, calificado = ? WHERE id = ?');
                            $updateSuccess = $updateStmt->execute([$data['rating'], 1, $data['proyecto_id']]);

                            if ($updateSuccess) {
                                $this->db->commit();
                                echo json_encode(['success' => true]);
                                return;
                            } else {
                                $this->db->rollBack();
                                echo json_encode(['success' => false, 'error' => 'Error al actualizar el proyecto']);
                                return;
                            }
                        } else {
                            $this->db->rollBack();
                            echo json_encode(['success' => false, 'error' => 'Error al insertar los datos']);
                            return;
                        }
                    } catch (Exception $e) {
                        $this->db->rollBack();
                        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'error' => 'Alguno de los datos recibidos es nulo']);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al decodificar los datos JSON']);
                return;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Esta página solo acepta solicitudes POST']);
            return;
        }
    }

    private function validateData($data, $fields) {
        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }
        return true;
    }
}
