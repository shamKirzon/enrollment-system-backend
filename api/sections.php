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
      echo json_encode(['message' => "Successfully created section.", 
        "data" => [
          "section_id" => $section_id
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Check if section $section_id already exists."]);
    }
    break;
  
  case 'PATCH':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $section_id = $json_data['id'];
    $section_name = $json_data['name'];

    try {
      $stmt = $pdo->prepare(
        "
        UPDATE sections 
        SET name = ?
        WHERE id = ?
        "
      );
      $stmt->execute([$section_name, $section_id]);

      http_response_code(200); 
      echo json_encode(['message' => "Successfully updated section."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to update section."]);
    }
    break;

  case 'DELETE': 
    $ids = json_decode(file_get_contents('php://input'), true);

    $sql = "
      DELETE FROM sections 
      WHERE id IN (
    ";

    if(!empty($ids)) {
      $placeholders = array_fill(0, count($ids), '?');
      $sql .= implode(', ', $placeholders);
    }

    $sql .= ")";

    try {
      $stmt = $pdo->prepare($sql);

      $stmt->execute($ids);

      http_response_code(200); 
      echo json_encode(['message' => "Successfully deleted sections."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to delete sections."]);
    }

  break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;

}
?>
