<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM subject_strands
        "
      );

      $stmt->execute();
      $subject_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched subject strands.",
        'data' => [
          'subject_strands' => $subject_strands,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch subject_strands."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $subject_level_id = $json_data['subject_level_id'];
    $strand_ids = $json_data['strand_ids'];

      $pdo->beginTransaction();

      for ($i=0; $i < count($strand_ids); $i++) { 
        try {
          $strand_id = $strand_ids[$i];
          $subject_strand_id = $subject_level_id . "-" . $strand_id;

          $stmt = $pdo->prepare(
            "
            INSERT INTO subject_strands (id, subject_level_id, strand_id)
            VALUES (?, ?, ?)
            "
          );

          $stmt->execute([$subject_strand_id, $subject_level_id, $strand_id]);
        } catch (\Throwable $th) {
          throw new Exception("Failed to create subject strands.", 500);
        }
      }

      $pdo->commit();

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created subject strands."]);
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
