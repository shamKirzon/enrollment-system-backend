<?php
require_once "../../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':

    $year_level_id = $_GET['year_level_id'] ?? null;

    try {

      $sql = "
        SELECT 
        efl.id AS enrollment_fee_level_id,
        efl.amount,
        efl.enrollment_fee_id,
        efl.year_level_id,
        yl.name AS year_level_name,
        ef.name AS enrollment_fee_name
        FROM enrollment_fee_levels efl
        JOIN year_levels yl ON yl.id = efl.year_level_id
        JOIN enrollment_fees ef ON ef.id = efl.enrollment_fee_id
      ";

      $conditions = [];
      $params = [];

      if($year_level_id !== null) {
        $conditions[] = "yl.id = ?";
        $params[] = $year_level_id;
      }

      if (!empty($conditions)) {
          $sql .= " WHERE " . implode(" AND ", $conditions) . " ";
      }

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $enrollment_fee_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched enrollment fee levels.",
        'data' => [
          'enrollment_fee_levels' => $enrollment_fee_levels,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch enrollment fee levels."]);
    }
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
