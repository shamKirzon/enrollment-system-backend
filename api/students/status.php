<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = $_GET['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        SELECT
          CASE
            WHEN EXISTS (
              SELECT 1
              FROM enrollments sub_e
              WHERE sub_e.status = 'done' AND sub_e.student_id = ?
              ORDER BY sub_e.enrolled_at DESC
            ) THEN 'old'
            ELSE 'new'
          END AS student_status
        "
      );

      $stmt->execute([$student_id]);
      $student_status = $stmt->fetch(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched student status.",
        'data' => 
          $student_status,
        
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch student status."]);
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
