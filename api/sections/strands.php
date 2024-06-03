<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM section_strands
        "
      );

      $stmt->execute();
      $section_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched section strands.",
        'data' => [
          'section_strands' => $section_strands,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch section strands."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $section_level_id = $json_data['section_level_id'];
    $strand_id = $json_data['strand_id'];
    $id = $section_level_id . "-" . $strand_id;

    try {
      $section_strand_id = insert($pdo, $json_data);

      if($section_strand_id === false) {
        throw new Exception("Failed to insert section strand.", 409);
      }

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section strand.", 
        "data" => [
          "section_strand_id" => $section_strand_id
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode);
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  case 'PATCH':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $section_level_id = $json_data['section_level_id'];
    $strand_id = $json_data['strand_id'];
    $id = $section_level_id . "-" . $strand_id;

    $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM section_strands WHERE section_level_id = ?");
    $check_stmt->execute([$section_level_id]);
    $count = $check_stmt->fetchColumn();

    if ($count > 0) {
      try {
        $stmt = $pdo->prepare(
          "
          UPDATE section_strands
          SET strand_id = ?
          WHERE section_level_id = ?
          "
        );
        $stmt->execute([$strand_id, $section_level_id]);

        http_response_code(200); 
        echo json_encode(['message' => "Successfully updated section strand."]);
      } catch (\PDOException $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => "Failed to update section strand."]);
      }
    } else {
      try {
        $insert = insert($pdo, $json_data);

        if($insert === false) {
          throw new Exception("Failed to update section strand.", 409);
        }

        http_response_code(200); 
        echo json_encode(['message' => "Successfully updated section strand."]);
      } catch (\Throwable $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => "Failed to update section strand."]);
      }
    }
    break;

  case 'DELETE':
    break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}

function insert(PDO $pdo, mixed $json_data): string|false {
  $section_level_id = $json_data['section_level_id'];
  $strand_id = $json_data['strand_id'];
  $id = $section_level_id . "-" . $strand_id;

  try {
    $stmt = $pdo->prepare(
      "
      INSERT INTO section_strands (id, section_level_id, strand_id)
      VALUES (?, ?, ?)
      "
    );

    $stmt->execute([$id, $section_level_id, $strand_id]);

    return $id;
  } catch (\Throwable $th) {
    http_response_code($th->getCode());
  }

  return false;
}

?>
