<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $year_level = isset($_GET['level']) ? $_GET['level'] : null;
    $sql = "SELECT * FROM year_levels";

    $conditions = array();
    $params = array();

    if ($year_level !== null) {
        $conditions[] = "id = ?";
        $params[] = $year_level;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions) . " ";
    }

    $sql .= " ORDER BY CAST(SUBSTRING(id, 2) AS UNSIGNED)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $year_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$year_levels) {
        http_response_code(400);
        echo json_encode(['message' => "Failed to fetch year levels."]);
        exit;
    }

    http_response_code(200);
    echo json_encode(['message' => "Successfully fetched year levels.", 'data' => ['year_levels' => $year_levels]]);
    break;

  case 'POST':
    break;
  case 'PATCH':
    break;

  case 'DELETE':
    break;
  
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
