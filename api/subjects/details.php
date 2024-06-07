<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');
 
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $query = isset($_GET['q']) ? $_GET['q'] : null;
    $year_level_id = isset($_GET['year_level_id']) ? $_GET['year_level_id'] : null;
    $strand_id = isset($_GET['strand_id']) ? $_GET['strand_id'] : null;

    $pdo->beginTransaction();

    // Prepare the base count query
    $count_sql = "
        SELECT COUNT(DISTINCT sub.id) AS count 
        FROM subjects sub
        LEFT JOIN subject_levels sublvl ON sublvl.subject_id = sub.id
        LEFT JOIN subject_strands substr ON substr.subject_level_id = sublvl.id
    ";
    $count_params = [];

    // Modify count query if search query, year_level_id, or strand_id is provided
    if ($query || $year_level_id || $strand_id) {
        $count_sql .= " WHERE";
        $conditions = [];

        if ($query) {
            $conditions[] = "(sub.id LIKE :query OR sub.name LIKE :query)";
            $count_params['query'] = '%' . $query . '%';
        }

        if ($year_level_id) {
            $conditions[] = "sublvl.year_level_id = :year_level_id";
            $count_params['year_level_id'] = $year_level_id;
        }

        if ($strand_id) {
            $conditions[] = "substr.strand_id = :strand_id";
            $count_params['strand_id'] = $strand_id;
        }

        $count_sql .= ' ' . implode(' AND ', $conditions);
    }

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($count_params);
    $count = $count_stmt->fetchColumn();

    if ($count === false) {
        throw new Exception("Failed to fetch subject count.", 404);
    }

    // Prepare the base subject IDs query
    $subject_ids_sql = "
        SELECT DISTINCT sub.id 
        FROM subjects sub
        LEFT JOIN subject_levels sublvl ON sublvl.subject_id = sub.id
        LEFT JOIN subject_strands substr ON substr.subject_level_id = sublvl.id
    ";
    $subject_ids_params = [];

    // Modify subject IDs query if search query, year_level_id, or strand_id is provided
    if ($query || $year_level_id || $strand_id) {
        $subject_ids_sql .= " WHERE";
        $conditions = [];

        if ($query) {
            $conditions[] = "(sub.id LIKE :query OR sub.name LIKE :query)";
            $subject_ids_params['query'] = '%' . $query . '%';
        }

        if ($year_level_id) {
            $conditions[] = "sublvl.year_level_id = :year_level_id";
            $subject_ids_params['year_level_id'] = $year_level_id;
        }

        if ($strand_id) {
            $conditions[] = "substr.strand_id = :strand_id";
            $subject_ids_params['strand_id'] = $strand_id;
        }

        $subject_ids_sql .= ' ' . implode(' AND ', $conditions);
    }

    $subject_ids_sql .= " LIMIT :limit OFFSET :offset";
    $subject_ids_stmt = $pdo->prepare($subject_ids_sql);
    $subject_ids_stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $subject_ids_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Bind search query parameter if provided
    if ($query) {
        $subject_ids_stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    }

    // Bind year_level_id parameter if provided
    if ($year_level_id) {
        $subject_ids_stmt->bindValue(':year_level_id', $year_level_id, PDO::PARAM_STR);
    }

    // Bind strand_id parameter if provided
    if ($strand_id) {
        $subject_ids_stmt->bindValue(':strand_id', $strand_id, PDO::PARAM_STR);
    }

    $subject_ids_stmt->execute();
    $subject_ids = $subject_ids_stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($subject_ids)) {
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

        // Add year levels if not already added
        if ($row['year_level_id'] !== null && !in_array(['id' => $row['year_level_id'], 'name' => $row['year_level_name']], $subjects[$subject_id]['year_levels'])) {
            $subjects[$subject_id]['year_levels'][] = [
                'id' => $row['year_level_id'],
                'name' => $row['year_level_name']
            ];
        }

        // Add strands if not already added
        if ($row['strand_id'] !== null && !in_array(['id' => $row['strand_id'], 'name' => $row['strand_name']], $subjects[$subject_id]['strands'])) {
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
    $pdo->rollBack();
    http_response_code($th->getCode() ?: 500);
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
