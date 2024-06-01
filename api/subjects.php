<?php
require_once "../db.php";

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
        FROM subjects
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute();
      $count = $count_stmt->fetchColumn();

      if ($count === false) {
        $pdo->rollBack(); 
        throw new Exception("No subjects found.", 404);
      }

      $conditions = [];
      $params = [];

      $where_clause = "";

      $sql = "
        SELECT s.*,
          (SELECT COUNT(*) FROM subject_levels WHERE subject_id = s.id) AS year_level_count
        FROM subjects s
        LIMIT $limit OFFSET $offset
      ";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if($subjects === false) {
        $pdo->rollBack(); 
        throw new PDOException("Failed to fetch subjects.", 400);
      }

      $pdo->commit(); 

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched subjects.",
        'data' => [
          'subjects' => $subjects,
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

      $subject_id = $json_data['id'];
      $subject_name = $json_data['name'];

      try {
        $stmt = $pdo->prepare(
          "
          INSERT INTO subjects (id, name)
          VALUES (?, ?)
          "
          );

        $stmt->execute([$subject_id, $subject_name]);
      } catch (PDOException $th) {
        http_response_code(409);
        echo json_encode(['message' => "Subject $subject_id already exists."]);
        break;
      }

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created subject."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;
  case 'PATCH':
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);

      $subject_id = $json_data['id'];
      $subject_name = $json_data['name'];

      $stmt = $pdo->prepare(
        "
        UPDATE subjects 
        SET name = ?
        WHERE id = ?
        "
      );
      $exec = $stmt->execute([$subject_name, $subject_id]);

      if(!$exec){
        throw new Exception("Failed to update subject.", 500);
      }

      http_response_code(200); 
      echo json_encode(['message' => "Successfully updated subject."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
  case "DELETE":
    try {
      $ids = json_decode(file_get_contents('php://input'), true);

      $sql = "
        DELETE FROM subjects 
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
        throw new Exception("Failed to delete subject.", 500);
      }

      http_response_code(200); 
      echo json_encode(['message' => "Successfully deleted subject."]);
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
