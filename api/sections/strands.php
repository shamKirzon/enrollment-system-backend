<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM section_strands
        "
      );

      $stmt->execute();
      $section_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched section strands.",
        'data' => [
          'section_strands' => $section_strands,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch section strands."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $section_level_id = $json_data['section_level_id'];
    $strand_id = $json_data['strand_id'];
    $id = $section_level_id . "-" . $strand_id;

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO section_strands (id, section_level_id, strand_id)
        VALUES (?, ?, ?)
        "
      );

      $stmt->execute([$id, $section_level_id, $strand_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section strand."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create section strand."]);
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
