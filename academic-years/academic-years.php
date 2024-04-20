<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $academic_years = $pdo->query(
    "
    SELECT ay.*, COUNT(e.id) AS student_count
    FROM academic_years ay
    LEFT JOIN enrollments e ON ay.id = e.academic_year_id
    GROUP BY ay.id, ay.start_at, ay.end_at, ay.status
    ORDER BY ay.start_at DESC
    LIMIT 25
    "
  )->fetchAll(PDO::FETCH_ASSOC);

  if (!$academic_years) {
    http_response_code(400);
    echo json_encode(['message' => "Failed to fetch academic years."]);
    exit;
  }

  http_response_code(200);

  echo json_encode(['message' => "Successfully fetched academic years.", 'data' => ['academic_years' => $academic_years]]);
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

  http_response_code(200); 
  echo json_encode(['message' => "Successfully inserted academic year."]);
} elseif ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
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
}
else {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['message' => "Unsupported request method."]);
  exit;
}
?>
