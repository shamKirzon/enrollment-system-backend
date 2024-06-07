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
$middle_name = $json_data['middle_name'] ?? null;
$last_name = $json_data['last_name'];
$suffix_name = $json_data['suffix_name'] ?? null;
$email = $json_data['email'];
$contact_number = $json_data['contact_number'];
$role = $json_data['role'];
$password = $json_data['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {

  if($role === 'admin') {
    throw new Exception(400, "You can't register an admin.");
  }

  $stmt = $pdo->prepare(
    "
    INSERT INTO users (id, first_name, middle_name, last_name, suffix_name, email, contact_number, role, password) 
    VALUES (uuid(), :first_name, :middle_name, :last_name, :suffix_name, :email, :contact_number, :role, :password)
    "
  );

  $user_data = [
    'first_name' => $first_name,
    'middle_name' => $middle_name,
    'last_name' => $last_name,
    'suffix_name' => $suffix_name,
    'email' => $email,
    'contact_number' => $contact_number,
    'role' => $role,
    'password' => $hashed_password
  ];

  $stmt->execute($user_data);

  http_response_code(201);
  echo json_encode(['message' => "Successfully registered user."]);
} catch (Throwable $th) {
  http_response_code(409);
  echo json_encode(['message' => "Failed to register user."]);
}

?>
