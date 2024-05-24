<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM enrolled_tuition_plans
        "
      );

      $stmt->execute();
      $enrolled_tuition_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched enrolled tuition plans.",
        'data' => [
          'enrolled_tuition_plans' => $enrolled_tuition_plans,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch enrolled tuition plans."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $enrollment_id = $json_data['enrollment_id'];
    $tuition_plan_id = $json_data['tuition_plan_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO enrolled_tuition_plans (id, enrollment_id, tuition_plan_id)
        VALUES (uuid(), ?, ?)
        "
      );

      $stmt->execute([$enrollment_id, $tuition_plan_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created enrolled tuition plan."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create enrolled tuition plan."]);
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
