<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $pdo->beginTransaction(); 

    $count_sql = "
      SELECT
        role,
        (SELECT COUNT(*) FROM users WHERE users.role = u.role) AS value
      FROM users u
      GROUP BY role;
    ";

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute();
    $count = $count_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$count) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "No users found."]);
      exit;
    }

    $sql = "
      SELECT id, first_name, middle_name, last_name, email, contact_number, role, created_at, updated_at 
      FROM users
    ";

    $users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if (!$users) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch users."]);
      exit;
    }

    $pdo->commit(); 

    http_response_code(200);

    echo json_encode([
      'message' => "Successfully fetched academic years.",
      'data' => [
        'users' => $users,
        'count' => $count
      ]
    ]);
  break;
  case 'POST':
  break;
  case 'PATCH':
  break;
  case 'DELETE':
  break;
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
  break;
}

?>
