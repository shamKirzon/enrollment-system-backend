<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM tuition_plan_levels
        "
      );

      $stmt->execute();
      $tuition_plan_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched tuition plan levels.",
        'data' => [
          'tuition_plan_levels' => $tuition_plan_levels,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch tuition plan levels."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $down_payment_amount = $json_data['down_payment_amount'];
    $monthly_payment_amount = $json_data['monthly_payment_amount'];
    $tuition_plan_id = $json_data['tuition_plan_id'];
    $year_level_id = $json_data['year_level_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO tuition_plan_levels (id, down_payment_amount, monthly_payment_amount, tuition_plan_id, year_level_id)
        VALUES (uuid(), ?, ?, ?, ?)
        "
      );

      $stmt->execute([$down_payment_amount, $monthly_payment_amount, $tuition_plan_id, $year_level_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created tuition plan level."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create tuition plan level."]);
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
