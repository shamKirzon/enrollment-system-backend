<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM enrollment_transactions
        "
      );

      $stmt->execute();
      $tuition_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched tuition plans.",
        'data' => [
          'enrollment_transactions' => $tuition_plans,
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
    $transaction_id = $json_data['transaction_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO enrollment_transactions (enrollment_id, transaction_id)
        VALUES (?, ?)
        "
      );

      $stmt->execute([$enrollment_id, $transaction_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created enrollment transaction."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create enrollment transaction."]);
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
