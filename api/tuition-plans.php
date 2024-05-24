<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM tuition_plans
        "
      );

      $stmt->execute();
      $tuition_plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched tuition plans.",
        'data' => [
          'tuition_plans' => $tuition_plans,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch tuition plans."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $name = $json_data['name'];
    $id = strtolower($name);

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO tuition_plans (id, name)
        VALUES (?, ?)
        "
      );

      $stmt->execute([$id, $name]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created tuition plan."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Check if tuition plan $name already exists."]);
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
