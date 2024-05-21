<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':

    break;
  case 'POST':
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);

      $section_name = $json_data['name'];
      $year_level_id = $json_data['year_level_id'];
      $strand_id = $json_data['strand_id'];
      $section_id = str_replace(" ", "-", strtolower($section_name));

      $pdo->beginTransaction(); 

      $stmt = $pdo->prepare(
        "
        INSERT INTO sections (id, name)
        VALUES (?, ?)
        "
      );
      $exec = $stmt->execute([$section_id, $section_name]);

      if(!$exec){
        throw new Exception("Failed to create section.", 500);
      }

      $stmt = $pdo->prepare(
        "
        INSERT INTO section_levels (id, section_id, year_level_id, strand_id)
        VALUES (uuid(), ?, ?, ?)
        "
      );
      $exec = $stmt->execute([$section_id, $year_level_id, $strand_id]);

      if(!$exec){
        throw new Exception("Failed to create section level.", 500);
      }

      $pdo->commit();

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
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
