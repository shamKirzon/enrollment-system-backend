<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');
 
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $pdo->beginTransaction(); 

        // Fetch the total count of subjects
        $count_sql = "SELECT COUNT(*) AS count FROM subjects";
        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute();
        $count = $count_stmt->fetchColumn();

        if (!$count) {
            $pdo->rollBack(); 
            throw new Exception("No subjects found.", 404);
        }

        // Fetch the subject IDs for the current page
        $subject_ids_sql = "
            SELECT id
            FROM subjects
            LIMIT :limit OFFSET :offset
        ";
        $subject_ids_stmt = $pdo->prepare($subject_ids_sql);
        $subject_ids_stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $subject_ids_stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $subject_ids_stmt->execute();
        $subject_ids = $subject_ids_stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($subject_ids)) {
            $pdo->rollBack(); 
            throw new Exception("No subjects found for the current page.", 404);
        }

        // Fetch the detailed data for these subject IDs
        $subject_ids_placeholder = implode(',', array_fill(0, count($subject_ids), '?'));
        $sql = "
            SELECT 
                sub.id AS subject_id,
                sub.name AS subject_name,
                yl.id AS year_level_id,
                yl.name AS year_level_name,
                substr.strand_id AS strand_id,
                str.name AS strand_name
            FROM subjects sub
            LEFT JOIN subject_levels sublvl ON sublvl.subject_id = sub.id
            LEFT JOIN subject_strands substr ON substr.subject_level_id = sublvl.id
            LEFT JOIN year_levels yl ON yl.id = sublvl.year_level_id
            LEFT JOIN strands str ON str.id = substr.strand_id
            WHERE sub.id IN ($subject_ids_placeholder)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($subject_ids);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results === false) {
            $pdo->rollBack(); 
            throw new Exception("Failed to fetch subjects.", 400);
        }

        $subjects = [];

        foreach ($results as $row) {
            $subject_id = $row['subject_id'];
            
            if (!isset($subjects[$subject_id])) {
                $subjects[$subject_id] = [
                    'subject_id' => $subject_id,
                    'subject_name' => $row['subject_name'],
                    'year_levels' => [],
                    'strands' => [],
                ];
            }

            // Add year levels
            if ($row['year_level_id'] !== null && !array_column($subjects[$subject_id]['year_levels'], 'id', 'id')[$row['year_level_id']]) {
                $subjects[$subject_id]['year_levels'][] = [
                    'id' => $row['year_level_id'],
                    'name' => $row['year_level_name']
                ];
            }

            // Add strands
            if ($row['strand_id'] !== null && !array_column($subjects[$subject_id]['strands'], 'id', 'id')[$row['strand_id']]) {
                $subjects[$subject_id]['strands'][] = [
                    'id' => $row['strand_id'],
                    'name' => $row['strand_name']
                ];
            }
        }

        $subjects = array_values($subjects); // Re-index array to get rid of keys

        $pdo->commit(); 

        http_response_code(200);

        echo json_encode([
            'message' => "Successfully fetched subjects.",
            'data' => [
                'subjects' => $subjects,
                'count' => $count
            ]
        ]);
    } catch (\Throwable $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
    }
    break;
  case 'POST':
    break;
  case 'PATCH':
    break;
  case "DELETE":
    break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
