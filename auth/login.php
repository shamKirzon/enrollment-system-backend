<?php
require_once "../db.php";

session_start();

$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  exit;
}

$json_data = json_decode(file_get_contents('php://input'), true);

$email = $json_data['email'];
$password = $json_data['password'];

$stmt = $pdo->prepare(
  "
  SELECT id, first_name, middle_name, last_name, email, contact_number, role, created_at, updated_at
  FROM users 
  WHERE email = :email
  "
);

$user_cred = [
  'email' => $email
];

$stmt->execute($user_cred);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

if (!$user && !password_verify($password, $user['password'])) {
  http_response_code(500);
  echo json_encode(['message' => "Failed to log in."]);
  exit;
}

http_response_code(200);
echo json_encode(['message' => "Successfully logged in.", 'user' => $user]);
?>
