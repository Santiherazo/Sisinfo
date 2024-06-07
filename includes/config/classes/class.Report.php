<?php
class Report {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getResults() {
        $user_name = $_SESSION['user'];

        try {
            $this->db->beginTransaction();
            $queryGetInvestigatorsIds = "
                SELECT id, investigadores 
                FROM proyectos
            ";
            $stmtGetInvestigatorsIds = $this->db->prepare($queryGetInvestigatorsIds);
            $stmtGetInvestigatorsIds->execute();
            $investigatorsIds = $stmtGetInvestigatorsIds->fetchAll(PDO::FETCH_ASSOC);

            if ($investigatorsIds) {
                $allInvestigatorsIds = [];
                $projectIds = [];
                foreach ($investigatorsIds as $row) {
                    $ids = preg_split('/[\s,]+/', $row['investigadores']); // Separar por comas y espacios
                    $allInvestigatorsIds = array_merge($allInvestigatorsIds, $ids);
                    $projectIds[$row['id']] = $ids;
                }
                $allInvestigatorsIds = array_unique($allInvestigatorsIds);

                // Obtener los nombres de los investigadores basados en sus IDs
                $placeholders = implode(',', array_fill(0, count($allInvestigatorsIds), '?'));
                $queryGetInvestigatorsNames = "
                    SELECT id, nombre_completo 
                    FROM usuarios 
                    WHERE id IN ($placeholders)
                ";
                $stmtGetInvestigatorsNames = $this->db->prepare($queryGetInvestigatorsNames);
                foreach ($allInvestigatorsIds as $index => $investigatorId) {
                    $stmtGetInvestigatorsNames->bindValue(($index + 1), $investigatorId, PDO::PARAM_INT);
                }
                $stmtGetInvestigatorsNames->execute();
                $investigatorsNames = $stmtGetInvestigatorsNames->fetchAll(PDO::FETCH_ASSOC);

                // Crear un array asociativo para un acceso más fácil a los nombres de los investigadores
                $investigatorsMap = [];
                foreach ($investigatorsNames as $investigator) {
                    $investigatorsMap[$investigator['id']] = $investigator['nombre_completo'];
                }

                $queryValidateUser = "
                    SELECT id 
                    FROM usuarios 
                    WHERE nombre_completo = :user_name
                ";
                $stmtValidateUser = $this->db->prepare($queryValidateUser);
                $stmtValidateUser->bindParam(':user_name', $user_name, PDO::PARAM_STR);
                $stmtValidateUser->execute();
                $user = $stmtValidateUser->fetch(PDO::FETCH_ASSOC);

                if ($user && in_array($user['id'], $allInvestigatorsIds)) {
                    $relatedProjectIds = [];
                    foreach ($projectIds as $projectId => $investigatorIds) {
                        if (in_array($user['id'], $investigatorIds)) {
                            $relatedProjectIds[] = $projectId;
                        }
                    }

                    if (!empty($relatedProjectIds)) {
                        $placeholders = implode(',', array_fill(0, count($relatedProjectIds), '?'));
                        $queryGetProjects = "
                            SELECT 
                                p.id, 
                                p.titulo, 
                                p.descripcion,
                                p.investigadores, 
                                p.docentes,
                                p.linea,
                                p.evaluador,
                                p.fase,
                                c.titleProject,
                                c.feedProject,
                                c.introduction,
                                c.feedIntroduction,
                                c.problemStatement,
                                c.FeedStatement,
                                c.justify,
                                c.feedJustify,
                                c.targets,
                                c.feedTargets,
                                c.theorical,
                                c.feedTheorical,
                                c.methodology,
                                c.feedMethodology,
                                c.mainResults,
                                c.feedMainresults,
                                c.support,
                                c.feedSupport,
                                c.rating,
                                c.generalComments
                            FROM 
                                proyectos p
                                INNER JOIN calificaciones c ON p.id = c.idProject
                            WHERE 
                                p.id IN ($placeholders)
                        ";

                        $stmtGetProjects = $this->db->prepare($queryGetProjects);
                        foreach ($relatedProjectIds as $index => $projectId) {
                            $stmtGetProjects->bindValue(($index + 1), $projectId, PDO::PARAM_INT);
                        }
                        $stmtGetProjects->execute();
                        $results = $stmtGetProjects->fetchAll(PDO::FETCH_ASSOC);

                        if ($results) {
                            $reports = [];

                            foreach ($results as $row) {
                                // Convertir los IDs de los investigadores en nombres
                                $investigatorNames = array_map(function($id) use ($investigatorsMap) {
                                    return htmlspecialchars($investigatorsMap[$id]);
                                }, explode(",", $row['investigadores']));

                                $reports = [
                                    "id" => htmlspecialchars($row['id']),
                                    "titulo" => htmlspecialchars($row['titulo']),
                                    "descripcion" => htmlspecialchars($row['descripcion']),
                                    "estudiantes" => $investigatorNames,
                                    "Fase" => htmlspecialchars($row['fase']),
                                    "Linea" => htmlspecialchars($row['linea']),
                                    "docente" => htmlspecialchars($row['docentes']),
                                    "evaluador" => htmlspecialchars($row['evaluador']),
                                    "calificacion_general" => htmlspecialchars($row['rating']),
                                    "comentarioGeneral" => htmlspecialchars($row['generalComments']),
                                    "resultados" => [
                                        "titulo" => [
                                            "valor" => htmlspecialchars($row['titleProject']),
                                            "retroalimentación" => htmlspecialchars($row['feedProject'])
                                        ],
                                        "introduccion" => [
                                            "valor" => htmlspecialchars($row['introduction']),
                                            "retroalimentación" => htmlspecialchars($row['feedIntroduction'])
                                        ],
                                        "planteamiento" => [
                                            "valor" => htmlspecialchars($row['problemStatement']),
                                            "retroalimentación" => htmlspecialchars($row['FeedStatement'])
                                        ],
                                        "justificacion" => [
                                            "valor" => htmlspecialchars($row['justify']),
                                            "retroalimentación" => htmlspecialchars($row['feedJustify'])
                                        ],
                                        "objetivos" => [
                                            "valor" => htmlspecialchars($row['targets']),
                                            "retroalimentación" => htmlspecialchars($row['feedTargets'])
                                        ],
                                        "marcoTeorico" => [
                                            "valor" => htmlspecialchars($row['theorical']),
                                            "retroalimentación" => htmlspecialchars($row['feedTheorical'])
                                        ],
                                        "metodologia" => [
                                            "valor" => htmlspecialchars($row['methodology']),
                                            "retroalimentación" => htmlspecialchars($row['feedMethodology'])
                                        ],
                                        "resultadosIniciales" => [
                                            "valor" => htmlspecialchars($row['mainResults']),
                                            "retroalimentación" => htmlspecialchars($row['feedMainresults'])
                                        ],
                                        "sustentacion" => [
                                            "valor" => htmlspecialchars($row['support']),
                                            "retroalimentación" => htmlspecialchars($row['feedSupport'])
                                        ]
                                    ]
                                ];
                            }

                            $this->db->commit();
                            header('Content-Type: application/json');
                            echo json_encode($reports);
                        } else {
                            $this->db->rollBack();
                            header('Content-Type: application/json');
                            echo json_encode(["error" => "No se encontraron resultados para los proyectos."]);
                        }
                    } else {
                        $this->db->rollBack();
                        header('Content-Type: application/json');
                        echo json_encode(["error" => "No se encontraron proyectos relacionados con el usuario."]);
                    }
                } else {
                    $this->db->rollBack();
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "Usuario no encontrado en la tabla de usuarios o no está relacionado con ningún proyecto."]);
                }
            } else {
                $this->db->rollBack();
                header('Content-Type: application/json');
                echo json_encode(["error" => "No se encontraron investigadores en la tabla de proyectos."]);
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            header('Content-Type: application/json');
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}