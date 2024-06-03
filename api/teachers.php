<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM teachers
        "
      );

      $stmt->execute();
      $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched teachers.",
        'data' => [
          'teachers' => $teachers,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch teachers."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $first_name = $json_data['first_name'];
    $middle_name = $json_data['middle_name'];
    $last_name = $json_data['last_name'];
    $suffix_name = $json_data['suffix_name'];
    $sex = $json_data['sex'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO teachers (id, first_name, middle_name, last_name, suffix_name, sex)
        VALUES (uuid(), ?, ?, ?, ?, ?)
        "
      );

      $stmt->execute([$first_name, $middle_name, $last_name, $suffix_name, $sex]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created teacher."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create teacher."]);
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
