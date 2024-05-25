<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM report_cards
        "
      );

      $stmt->execute();
      $report_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched report cards.",
        'data' => [
          'report_cards' => $report_cards,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch report cards."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $report_card_url = $json_data['report_card_url'];
    $enrollment_id = $json_data['enrollment_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO report_cards (report_card_url, enrollment_id)
        VALUES (?, ?)
        "
      );

      $stmt->execute([$report_card_url, $enrollment_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully inserted previous report card."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to insert previous report card."]);
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
