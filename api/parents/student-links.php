<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM parent_student_links
        "
      );

      $stmt->execute();
      $parent_student_links = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched parent student links.",
        'data' => [
          'parent_student_links' => $parent_student_links,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch parent student links."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $parent_id = $json_data['parent_id'];
    $student_id = $json_data['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO parent_student_links (id, parent_id, student_id)
        VALUES (uuid(), ?, ?)
        "
      );

      $stmt->execute([$parent_id, $student_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created parent student link."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create parent student link."]);
    }
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
