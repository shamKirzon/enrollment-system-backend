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
        FROM subject_levels subl
        JOIN subjects sub ON sub.id = subl.subject_id
        JOIN year_levels yl ON yl.id = subl.year_level_id
        JOIN subject_strands subjstr ON subjstr.subject_level_id = subl.id
        JOIN strands str ON str.id = subjstr.strand_id
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
          subjstr.id AS subject_strand_id,
          subl.id AS subject_level_id, subl.subject_id, subl.year_level_id,
          sub.name AS subject_name,
          yl.name AS year_level_name,
          str.id AS strand_id,
          str.name AS strand_name
        FROM subject_levels subl
        JOIN subjects sub ON sub.id = subl.subject_id
        JOIN year_levels yl ON yl.id = subl.year_level_id
        JOIN subject_strands subjstr ON subjstr.subject_level_id = subl.id
        JOIN strands str ON str.id = subjstr.strand_id
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
      $year_level_ids = $json_data['year_level_ids'];

      $pdo->beginTransaction();

      $subject_level_ids = [];

      if(!empty($year_level_ids)) {
        foreach ($year_level_ids as $year_level_id) {
          $subject_level_id = $subject_id . "-" . $year_level_id;

          $subject_level_ids[] = $subject_level_id;

          try {
            $stmt = $pdo->prepare(
              "
              INSERT INTO subject_levels (id, subject_id, year_level_id)
              VALUES (?, ?, ?)
              "
            );
            $exec = $stmt->execute([$subject_level_id, $subject_id, $year_level_id]);
          } catch (\PDOException $th) {
            http_response_code(409);
            // echo json_encode(['message' => "Subject $subject_level_id already exists."]);
            // break;
          }
        }
      }

      $pdo->commit();

      http_response_code(201); 
      echo json_encode([
        'message' => "Successfully created subject level.", 
        "data" => [
          "subject_level_ids" => $subject_level_ids
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  case 'PATCH':
    try {
        $json_data = json_decode(file_get_contents('php://input'), true);

        $subject_id = $json_data['subject_id'];
        $new_year_level_ids = $json_data['year_level_ids'];

        $pdo->beginTransaction();

        // Fetch existing year_level_ids for the given subject_id
        $stmt = $pdo->prepare("
            SELECT year_level_id 
            FROM subject_levels 
            WHERE subject_id = ?
        ");
        $stmt->execute([$subject_id]);
        $existing_year_level_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Determine year_level_ids to be added and removed
        $year_level_ids_to_add = array_diff($new_year_level_ids, $existing_year_level_ids);
        $year_level_ids_to_remove = array_diff($existing_year_level_ids, $new_year_level_ids);

        // Prepare to store subject_level_ids to return
        $subject_level_ids = [];

        // Remove outdated subject levels
        if (!empty($year_level_ids_to_remove)) {
            $in  = str_repeat('?,', count($year_level_ids_to_remove) - 1) . '?';
            $stmt = $pdo->prepare("
                DELETE FROM subject_levels 
                WHERE subject_id = ? 
                AND year_level_id IN ($in)
            ");
            $stmt->execute(array_merge([$subject_id], $year_level_ids_to_remove));
        }

        // Insert new subject levels
        if (!empty($year_level_ids_to_add)) {
            foreach ($year_level_ids_to_add as $year_level_id) {
                $subject_level_id = $subject_id . "-" . $year_level_id;
                $subject_level_ids[] = $subject_level_id;

                $stmt = $pdo->prepare("
                    INSERT INTO subject_levels (id, subject_id, year_level_id)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$subject_level_id, $subject_id, $year_level_id]);
            }
        }

        // If no changes were made, fetch existing subject_level_ids to return
        if (empty($year_level_ids_to_add) && empty($year_level_ids_to_remove)) {
            $stmt = $pdo->prepare("
                SELECT id 
                FROM subject_levels 
                WHERE subject_id = ?
                AND year_level_id IN (" . str_repeat('?,', count($new_year_level_ids) - 1) . "?)
            ");
            $stmt->execute(array_merge([$subject_id], $new_year_level_ids));
            $subject_level_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $pdo->commit();

        http_response_code(200);
        echo json_encode([
            'message' => "Successfully updated subject levels.",
            "data" => [
                "subject_level_ids" => $subject_level_ids
            ]
        ]);
    } catch (\Throwable $th) {
        $pdo->rollBack();
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
