<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $pdo->beginTransaction(); 

    $count_sql = "
      SELECT COUNT(*) AS count
      FROM academic_years
    ";

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute();
    $count = $count_stmt->fetchColumn();

    if (!$count) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "No enrollments found."]);
      exit;
    }

    $sql = "
      SELECT ay.*, COUNT(e.id) AS student_count
      FROM academic_years ay
      LEFT JOIN enrollments e ON ay.id = e.academic_year_id AND e.status = 'done'
      GROUP BY ay.id, ay.start_at, ay.end_at, ay.status
      ORDER BY ay.start_at DESC
    ";

    $sql .= " LIMIT " . $limit;
    $sql .= " OFFSET " . $offset;

    $academic_years = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if (!$academic_years) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch academic years."]);
      exit;
    }

    $pdo->commit(); 

    http_response_code(200);

    echo json_encode([
      'message' => "Successfully fetched academic years.",
      'data' => [
        'academic_years' => $academic_years,
        'count' => $count
      ]
    ]);
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $start_at = $json_data['start_at'];
    $end_at = $json_data['end_at'];
    $status = $json_data['status'];

    $stmt = $pdo->prepare(
      "
      INSERT INTO academic_years (start_at, end_at, status)
      VALUES (?, ?, ?)
      "
    );

    $exec = $stmt->execute([$start_at, $end_at, $status]);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to insert academic year."]);
      exit;
    }

    http_response_code(201); 
    echo json_encode(['message' => "Successfully inserted academic year."]);
    break;
  case 'PATCH':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $start_at = $json_data['start_at'];
    $end_at = $json_data['end_at'];
    $status = $json_data['status'];
    $id = $_GET['id'];

    $stmt = $pdo->prepare(
      "
      UPDATE academic_years
      SET
        start_at = ?,
        end_at = ?,
        status = ?
      WHERE id = ?
      "
    );

    $exec = $stmt->execute([$start_at, $end_at, $status, $id]);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to update academic year."]);
      exit;
    }

    http_response_code(200); 
    echo json_encode(['message' => "Successfully updated academic year."]);
    break;

  case 'DELETE':
    $id = $_GET['id'];

    $stmt = $pdo->prepare(
      "
      DELETE FROM academic_years 
      WHERE id = ?
      "
    );

    $exec = $stmt->execute([$id]);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to delete academic year."]);
      exit;
    }

    http_response_code(200); 
    echo json_encode(['message' => "Successfully deleted academic year."]);
    break;
  
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
