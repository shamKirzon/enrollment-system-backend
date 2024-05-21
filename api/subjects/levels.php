<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');
 
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $page = isset($_GET['page']) ? $_GET['page'] : 1;
      $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
      $offset = ($page - 1) * $limit;

      $pdo->beginTransaction(); 

      $count_sql = "
        SELECT COUNT(*) AS count
        FROM subject_levels
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute();
      $count = $count_stmt->fetchColumn();

      if (!$count) {
        $pdo->rollBack(); 
        throw new Exception("No subject levels found.", 404);
      }

      $conditions = [];
      $params = [];

      $sql = "
        SELECT 
          subl.id, subl.subject_id, subl.year_level_id, subl.strand_id,
          sub.name AS subject_name,
          yl.name AS year_level_name,
          st.name AS strand_name
        FROM subject_levels subl
        JOIN subjects sub ON sub.id = subl.subject_id
        JOIN year_levels yl ON yl.id = subl.year_level_id
        LEFT JOIN strands st ON st.id = subl.strand_id
      ";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $subject_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if(!$subject_levels) {
        $pdo->rollBack(); 
        throw new Exception("Failed to fetch subject levels.", 400);
      }

      $pdo->commit(); 

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched subject levels.",
        'data' => [
          'subject_levels' => $subject_levels,
          'count' => $count
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
  case 'POST':
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);

      $subject_id = $json_data['subject_id'];
      $year_level_id = $json_data['year_level_id'];
      $strand_id = $json_data['strand_id'];

      $stmt = $pdo->prepare(
        "
        INSERT INTO subject_levels (id, subject_id, year_level_id, strand_id)
        VALUES (uuid(), ?, ?, ?)
        "
      );
      $exec = $stmt->execute([$subject_id, $year_level_id, $strand_id]);

      if(!$exec){
        throw new Exception("Failed to create subject level.", 500);
      }

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created subject level."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;
  case 'PATCH':
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);

      $subject_id = $json_data['subject_id'];
      $year_level_id = $json_data['year_level_id'];
      $strand_id = $json_data['strand_id'];

      $stmt = $pdo->prepare(
        "
        UPDATE subject_levels
        SET subject_id = ?, year_level_id = ?, strand_id = ?
        WHERE id = ?
        "
      );
      $exec = $stmt->execute([$subject_id, $year_level_id, $strand_id]);

      if(!$exec){
        throw new Exception("Failed to update subject level.", 500);
      }

      http_response_code(200); 
      echo json_encode(['message' => "Successfully updated subject level."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
  
  case "DELETE":
    try {
      $ids = json_decode(file_get_contents('php://input'), true);

      $sql = "
        DELETE FROM subject_levels
        WHERE id IN (
      ";

      if(!empty($ids)) {
        $placeholders = array_fill(0, count($ids), '?');
        $sql .= implode(', ', $placeholders);
      }

      $sql .= ")";

      $stmt = $pdo->prepare($sql);
      $exec = $stmt->execute($ids);

      if(!$exec){
        throw new Exception("Failed to delete subject levels.", 500);
      }

      http_response_code(200); 
      echo json_encode(['message' => "Successfully deleted subject levels."]);
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
