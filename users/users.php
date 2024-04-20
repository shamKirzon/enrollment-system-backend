<?php
require_once "../db.php";

$pdo = getConnection();

$users = $pdo->query(
  "
  SELECT id, first_name, middle_name, last_name, email, contact_number, role, created_at, updated_at 
  FROM users
  "
)->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if (!$users) {
  http_response_code(400);
  echo json_encode(['message' => "Failed to fetch users."]);
  exit;
}

http_response_code(200);

echo json_encode(['message' => "Successfully fetched users.", 'data' => ['users' => $users]]);
?>
