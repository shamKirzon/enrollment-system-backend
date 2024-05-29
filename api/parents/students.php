<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $parent_id = $_GET['parent_id'];
    // $student_id = $_GET['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        SELECT 
          u.id, 
          u.created_at, 
          u.first_name, 
          u.middle_name, 
          u.last_name, 
          u.suffix_name, 
          u.email, 
          u.contact_number, 
          u.role, 
          u.avatar_url
        FROM users u
        JOIN parent_student_links psl ON psl.student_id = u.id
        WHERE psl.parent_id = ?
        "
      );

      $stmt->execute([$parent_id]);
      $student = $stmt->fetch(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched student.",
        'data' => [
          'student' => $student,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch student."]);
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
