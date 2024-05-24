<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':

    break;
  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $section_name = $json_data['name'];
    $section_id = str_replace(" ", "-", strtolower($section_name));

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO sections (id, name)
        VALUES (?, ?)
        "
      );
      $stmt->execute([$section_id, $section_name]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Check if section $section_id already exists."]);
    }
    break;

  case 'DELETE': 
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

  break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;

}
?>
