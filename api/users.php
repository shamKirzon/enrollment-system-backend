<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $role = isset($_GET['role']) ? $_GET['role'] : null;
    $offset = ($page - 1) * $limit;

    try {
      $pdo->beginTransaction(); 

      $count_sql = "
        SELECT COUNT(*) FROM users
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute();
      $count = $count_stmt->fetchColumn();

      if ($count === false) {
        $pdo->rollBack(); 
        throw new Exception("Failed to fetch user count.", 400);
      }

      $role_count_sql = "
        SELECT
          role,
          (SELECT COUNT(*) FROM users WHERE users.role = u.role) AS value
        FROM users u
        GROUP BY u.role;
      ";

      $role_count_stmt = $pdo->prepare($role_count_sql);
      $role_count_stmt->execute();
      $role_count = $role_count_stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($role_count === false) {
        $pdo->rollBack(); 
        throw new Exception("Failed to fetch user role count.", 400);
      }

      $sql = "
        SELECT id, created_at, first_name, middle_name, last_name, suffix_name, email, contact_number, role, avatar_url
        FROM users
        LIMIT $limit OFFSET $offset
      ";

      $users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

      if ($users === false) {
        $pdo->rollBack(); 
        throw new Exception("Failed to fetch users.", 400);
      }

      $pdo->commit(); 

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched users.",
        'data' => [
          'users' => $users,
          'role_count' => $role_count,
          'count' => $count
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

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
