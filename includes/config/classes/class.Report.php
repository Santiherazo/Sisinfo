<?php 
class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getResults() {
        $user_name = $_SESSION['user'];

        try {
            // Iniciar la transacción
            $this->db->beginTransaction();

            // Validar si el usuario existe en la tabla `proyectos`
            $queryValidateUser = "
                SELECT id 
                FROM `proyectos` 
                WHERE `investigadores` LIKE CONCAT('%', :user_name, '%')
                LIMIT 1
            ";
            $stmtValidateUser = $this->db->prepare($queryValidateUser);
            $stmtValidateUser->bindParam(':user_name', $user_name);
            $stmtValidateUser->execute();
            $project = $stmtValidateUser->fetch(PDO::FETCH_ASSOC);

            if ($project) {
                $projectId = $project['id'];

                $query = "
                    SELECT 
                        p.`id`, 
                        p.`titulo`, 
                        p.`descripcion`,
                        p.`investigadores`, 
                        p.`docentes`,
                        p.`linea`,
                        p.`evaluador`,
                        p.`fase`,
                        c.`titleProject`,
                        c.`feedProject`,
                        c.`introduction`,
                        c.`feedIntroduction`,
                        c.`problemStatement`,
                        c.`FeedStatement`,
                        c.`justify`,
                        c.`feedJustify`,
                        c.`targets`,
                        c.`feedTargets`,
                        c.`theorical`,
                        c.`feedTheorical`,
                        c.`methodology`,
                        c.`feedMethodology`,
                        c.`mainResults`,
                        c.`feedMainresults`,
                        c.`support`,
                        c.`feedSupport`,
                        c.`rating`,
                        c.`generalComments`
                    FROM 
                        `proyectos` p
                        INNER JOIN `calificaciones` c ON p.`id` = c.`idProject`
                    WHERE 
                        p.`id` = :project_id
                ";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':project_id', $projectId);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $report = [];
                foreach ($results as $row) {
                    $report[$row['id']] = [
                        "titulo" => strip_tags($row['titulo']),
                        "descripcion" => strip_tags($row['descripcion']),
                        "estudiantes" => explode(",", strip_tags($row['investigadores'])),
                        "Fase" => strip_tags($row['fase']),
                        "Linea" => strip_tags($row['linea']),
                        "docente" => strip_tags($row['docentes']),
                        "evaluador" => strip_tags($row['evaluador']),
                        "calificacion_general" => strip_tags($row['rating']),
                        "comentarioGeneral" => strip_tags($row['generalComments']),
                        "resultados" => [
                            "titulo" => [
                                "valor" => $row['titleProject'],
                                "retroalimentación" => strip_tags($row['feedProject'])
                            ],
                            "introduccion" => [
                                "valor" => $row['introduction'],
                                "retroalimentación" => strip_tags($row['feedIntroduction'])
                            ],
                            "planteamiento" => [
                                "valor" => $row['problemStatement'],
                                "retroalimentación" => strip_tags($row['FeedStatement'])
                            ],
                            "justificacion" => [
                                "valor" => $row['justify'],
                                "retroalimentación" => strip_tags($row['feedJustify'])
                            ],
                            "objetivos" => [
                                "valor" => $row['targets'],
                                "retroalimentación" => strip_tags($row['feedTargets'])
                            ],
                            "marcoTeorico" => [
                                "valor" => $row['theorical'],
                                "retroalimentación" => strip_tags($row['feedTheorical'])
                            ],
                            "metodologia" => [
                                "valor" => $row['methodology'],
                                "retroalimentación" => strip_tags($row['feedMethodology'])
                            ],
                            "resultadosIniciales" => [
                                "valor" => $row['mainResults'],
                                "retroalimentación" => strip_tags($row['feedMainresults'])
                            ],
                            "sustentacion" => [
                                "valor" => $row['support'],
                                "retroalimentación" => strip_tags($row['feedSupport'])
                            ]
                        ]
                    ];
                }

                // Confirmar la transacción
                $this->db->commit();

                return json_encode($report);
            } else {
                // Si no se encuentra el usuario, deshacer la transacción
                $this->db->rollBack();
                return json_encode(["error" => "Usuario no encontrado en la tabla de proyectos."]);
            }
        } catch (Exception $e) {
            // Si ocurre un error, deshacer la transacción
            $this->db->rollBack();
            return json_encode(["error" => $e->getMessage()]);
        }
    }
}
