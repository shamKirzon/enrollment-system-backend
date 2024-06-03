<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM enrollment_strands
        "
      );

      $stmt->execute();
      $enrollment_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched tuition plans.",
        'data' => [
          'enrollment_strands' => $enrollment_strands,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch tuition plans."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $enrollment_id = $json_data['enrollment_id'];
    $strand_id = $json_data['strand_id'];
    $enrollment_strand_id = $strand_id . "-" . $enrollment_id;

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO enrollment_strands (id, enrollment_id, strand_id)
        VALUES (?, ?, ?)
        "
      );

      $stmt->execute([$enrollment_strand_id, $enrollment_id, $strand_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created enrollment strand."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create enrollment strand."]);
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
