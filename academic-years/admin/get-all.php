<?php
require_once "../../db.php";

$pdo = getConnection();

$academic_years = $pdo->query(
  "
  SELECT ay.*, COUNT(e.id) AS student_count
  FROM academic_years ay
  LEFT JOIN enrollments e ON ay.id = e.academic_year_id
  GROUP BY ay.id, ay.year, ay.start_at, ay.end_at, ay.status
  ORDER BY ay.start_at DESC
  LIMIT 25
  "
)->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if (!$academic_years) {
  http_response_code(400);
  echo json_encode(['message' => "Failed to fetch academic years."]);
  exit;
}

http_response_code(200);

echo json_encode(['message' => "Successfully fetched academic years.", 'data' => ['academic_years' => $academic_years]]);
?>
