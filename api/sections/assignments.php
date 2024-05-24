<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM section_assignments
        "
      );

      $stmt->execute();
      $section_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched section assignments.",
        'data' => [
          'section_assignments' => $section_assignments,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch section assignments."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $enrollment_id = $json_data['enrollment_id'];
    $section_level_id = $json_data['section_level_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO section_assignments (id, enrollment_id, section_level_id)
        VALUES (uuid(), ?, ?)
        "
      );

      $stmt->execute([$enrollment_id, $section_level_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section assignment."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create section assignment."]);
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
