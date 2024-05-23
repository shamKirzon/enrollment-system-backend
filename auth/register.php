<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405); // Method Not Allowed
  echo json_encode(['message' => "Unsupported request method."]);
  exit;
}

$json_data = json_decode(file_get_contents('php://input'), true);

$first_name = $json_data['first_name'];
$middle_name = $json_data['middle_name'];
$last_name = $json_data['last_name'];
$email = $json_data['email'];
$contact_number = $json_data['contact_number'];
$password = $json_data['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
  $stmt = $pdo->prepare(
    "
    INSERT INTO users (id, first_name, middle_name, last_name, email, contact_number, password) 
    VALUES (uuid(), :first_name, :middle_name, :last_name, :email, :contact_number, :password)
    "
  );

  $user_data = [
    'first_name' => $first_name,
    'middle_name' => $middle_name,
    'last_name' => $last_name,
    'email' => $email,
    'contact_number' => $contact_number,
    'password' => $hashed_password
  ];

  $stmt->execute($user_data);
} catch (\PDOException $th) {
  http_response_code(409);
  echo json_encode(['message' => "Failed to register user."]);
}

http_response_code(201);
echo json_encode(['message' => "Successfully registered user."]);
?>
