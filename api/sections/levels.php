<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');
 
switch ($_SERVER['REQUEST_METHOD']) {
  case "GET":
    break;

  case "POST":
    $json_data = json_decode(file_get_contents('php://input'), true);

    $year_level_id = $json_data['year_level_id'];
    $strand_id = $json_data['strand_id'];
    $section_name = $json_data['name'];
    $section_id = str_replace(" ", "-", strtolower($section_name));

    $pdo->beginTransaction();

    $section_level_id = $section_id . "-" . $year_level_id;

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO section_levels (id, section_id, year_level_id)
        VALUES (?, ?, ?)
        "
      );
      $stmt->execute([$section_level_id, $section_id, $year_level_id]);
    } catch (\PDOException $th) {
      http_response_code(409);
      echo json_encode(['message' => "Check if section $section_level_id already exists."]);
      break;
    }

    if ($strand_id !== null) {
      $section_strand_id = $section_level_id . "-" . $strand_id;

      try {
        $stmt = $pdo->prepare(
          "
          INSERT INTO section_strands (id, section_level_id, strand_id)
          VALUES (?, ?, ?)
          "
        );
        $stmt->execute([$section_strand_id, $section_level_id, $strand_id]);
      } catch (\PDOException $th) {
        http_response_code(409);
        echo json_encode(['message' => "Section $section_strand_id already exists."]);
        break;
      }
    }

    $pdo->commit();

    http_response_code(201); 
    echo json_encode(['message' => "Successfully created section level."]);
  break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
