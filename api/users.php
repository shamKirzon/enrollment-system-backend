<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $role = isset($_GET['role']) ? $_GET['role'] : null;
    $query = isset($_GET['q']) ? $_GET['q'] : null;
    $offset = ($page - 1) * $limit;

    $user_id = $_GET['id'];

    if ($user_id !== null) {
      try {
        $user = get_user($pdo, $user_id);

        if($user === false) {
          throw new PDOException("Failed to fetch user.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched user.",
            'data' => [
                'user' => $user,
            ]
        ]);

      } catch (\Throwable $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
      }

      exit;
    }

    try {
        $pdo->beginTransaction();

        $conditions = [];
        $params = [];

        if ($role !== null) {
            $conditions[] = "role = ?";
            $params[] = $role;
        }

        if ($query !== null) {
            $conditions[] = "(first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $query . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $where_clause = "";

        if (!empty($conditions)) {
            $where_clause = " WHERE " . implode(" AND ", $conditions);
        }

        $count_sql = "
            SELECT COUNT(*) FROM users
            $where_clause
        ";

        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute($params);
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
            $where_clause
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users === false) {
            $pdo->rollBack();
            throw new PDOException("Failed to fetch users.", 400);
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
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
    }

  break;
  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $first_name = $json_data['first_name'];
    $middle_name = $json_data['middle_name'];
    $last_name = $json_data['last_name'];
    $suffix_name = $json_data['suffix_name'];
    $email = $json_data['email'];
    $contact_number = $json_data['contact_number'];
    $role = $json_data['role'];
    $avatar_url = $json_data['avatar_url'];
    $password = $json_data['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->query("SELECT uuid()");
    $user_id = $stmt->fetchColumn();

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO users (id, first_name, middle_name, last_name, suffix_name, email, contact_number, role, avatar_url, password)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
      );

      $stmt->execute([$user_id, $first_name, $middle_name, $last_name, $suffix_name, $email, $contact_number, $role, $avatar_url, $hashed_password]);

      http_response_code(201); 
      echo json_encode([
        'message' => "Successfully created user.", 
        "data" => [
          "user_id" => $user_id
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create user."]);
    }
  break;
  case 'PATCH':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $id = $_GET['id'];
    $first_name = $json_data['first_name'];
    $middle_name = $json_data['middle_name'];
    $last_name = $json_data['last_name'];
    $suffix_name = $json_data['suffix_name'];
    $email = $json_data['email'];
    $contact_number = $json_data['contact_number'];
    $role = $json_data['role'];
    $avatar_url = $json_data['avatar_url'];
    $password = $json_data['password'];
    $hashed_password = $password === '' ? null : password_hash($password, PASSWORD_DEFAULT);

    try {
      $stmt = $pdo->prepare(
        "
        UPDATE users
        SET 
          first_name = ?,
          middle_name = ?,
          last_name = ?,
          suffix_name = ?,
          email = ?,
          contact_number = ?,
          role = ?,
          avatar_url = ?,
          password = COALESCE(?, password)
        WHERE id = ?
        "
      );

      $stmt->execute([$first_name, $middle_name, $last_name, $suffix_name, $email, $contact_number, $role, $avatar_url, $hashed_password, $id]);

      http_response_code(200); 
      echo json_encode(['message' => "Successfully updated user."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to update user."]);
    }
  break;
  case 'DELETE':
    $ids = json_decode(file_get_contents('php://input'), true);

    $sql = "
      DELETE FROM users 
      WHERE id IN (
    ";

    if(!empty($ids)) {
      $placeholders = array_fill(0, count($ids), '?');
      $sql .= implode(', ', $placeholders);
    }

    $sql .= ")";

    try {
      $stmt = $pdo->prepare($sql);

      $stmt->execute($ids);

      http_response_code(200); 
      echo json_encode(['message' => "Successfully deleted users."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to delete users."]);
    }
  break;
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
  break;
}

function get_user(PDO $pdo, string $user_id) {
  $sql = "
    SELECT id, created_at, first_name, middle_name, last_name, suffix_name, email, contact_number, role, avatar_url
    FROM users
    WHERE id = ?
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  return $user;
}

?>
