<?php
require_once "../db.php";
require_once "../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $date_order = isset($_GET['date_order']) ? $_GET['date_order'] : 'asc';
    $student_id = $_GET['student_id'] ?? null;
    $parent_id = $_GET['parent_id'] ?? null;
    $payment_method = $_GET['payment_method'] ?? null;

    $pdo->beginTransaction(); 

    if(isset($student_id)) {
      try {
        $count_sql = "
          SELECT COUNT(*) AS count
          FROM transactions t
          JOIN payment_modes pm ON pm.id = t.payment_mode_id
          JOIN enrollment_transactions et ON et.transaction_id = t.id
          JOIN enrollments e ON e.id = et.enrollment_id
          WHERE e.student_id = ?
        ";

        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute([$student_id]);
        $count = $count_stmt->fetchColumn();

        if ($count === false) {
          throw new PDOException("Failed to get transactions count.", 404);
        }

        $transactions = get_transactions($pdo, $student_id, $limit, $offset, $date_order);

        if($transactions === false) {
          throw new PDOException("Failed to fetch transactions.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched transactions.",
            'data' => [
              'transactions' => $transactions,
              "count" => $count
            ]
        ]);

      } catch (\Throwable $th) {
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
      }

      $pdo->commit(); 

      exit;
    }

    if(isset($parent_id)) {
      try {
        $transactions = get_all_children_transactions($pdo, $parent_id, $limit, $offset, $date_order);

        if($transactions === false) {
          throw new PDOException("Failed to fetch transactions.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched transactions.",
            'data' => [
              'transactions' => $transactions,
              "count" => $count
            ]
        ]);
      } catch (\Throwable $th) {
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
      }
      $pdo->commit(); 

      exit;
    }


    $count_sql = "
      SELECT COUNT(t.id) AS count
      FROM transactions t
      JOIN payment_modes pm ON pm.id = t.payment_mode_id
      JOIN enrollment_transactions et ON et.transaction_id = t.id
      JOIN enrollments e ON e.id = et.enrollment_id
    ";


    $conditions = array();
    $params = array();

    if($payment_method !== null) {
      $conditions[] = "t.payment_method = ?";
      $params[] = $payment_method;
    }

    if (!empty($conditions)) {
      $count_sql .= " WHERE " . implode(" AND ", $conditions) . " ";
    }

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $count = $count_stmt->fetchColumn();

    if ($count === false) {
      $pdo->rollBack(); 
      http_response_code(404);
      echo json_encode(['message' => "No transactions found."]);
      exit;
    }

    $server_url = get_server_url();

    $sql = "
      SELECT 
        t.id AS transaction_id, 
        t.created_at, 
        t.transaction_number, 
        t.payment_amount, 
        t.payment_method,
        CONCAT('$server_url', t.payment_receipt_url) AS payment_receipt_url, 
        t.payment_mode_id,
        pm.payment_channel
      FROM transactions t
      JOIN payment_modes pm ON pm.id = t.payment_mode_id
      JOIN enrollment_transactions et ON et.transaction_id = t.id
      JOIN enrollments e ON e.id = et.enrollment_id
    ";

    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(" AND ", $conditions) . " ";
    }

    $sql .= "
      ORDER BY t.created_at $date_order
      LIMIT $limit OFFSET $offset
      ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($transactions === false) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch payment transactions."]);
      exit;
    }

    $pdo->commit(); 

    http_response_code(200);

    echo json_encode([
      'message' => "Successfully fetched payment transactions.",
      'data' => [
        'transactions' => $transactions,
        'count' => $count
      ]
    ]);
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $transaction_number = $json_data['transaction_number'];
    $payment_amount = $json_data['payment_amount'];
    $payment_method = $json_data['payment_method'];
    $payment_receipt_url = $json_data['payment_receipt_url'];
    $payment_mode_id = $json_data['payment_mode_id'];

    $pdo->beginTransaction();

    $stmt = $pdo->query("SELECT uuid()");
    $transaction_id = $stmt->fetchColumn();

    $sql = "
      INSERT INTO transactions (id, transaction_number, payment_amount, payment_method, payment_receipt_url, payment_mode_id)
      VALUES (?, ?, ?, ?, ?, ?)
    ";

    try {
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$transaction_id, $transaction_number, $payment_amount, $payment_method, $payment_receipt_url, $payment_mode_id]);

      $pdo->commit();
      http_response_code(201); 
      echo json_encode([
        'message' => "Successfully created payment transaction.", 
        "data" => [
          "transaction_id" => $transaction_id
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create payment transaction."]);
    }

    break;
  case 'PATCH':

    break;

  case 'DELETE':
    try {
      $ids = json_decode(file_get_contents('php://input'), true);

      $sql = "
        DELETE FROM transactions 
        WHERE id IN (
      ";

      if(!empty($ids)) {
        $placeholders = array_fill(0, count($ids), '?');
        $sql .= implode(', ', $placeholders);
      }

      $sql .= ")";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($ids);

      http_response_code(200); 
      echo json_encode(['message' => "Successfully deleted transactions."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;
  
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}

function get_transactions(PDO $pdo, string $student_id, int $limit, int $offset, string $order) {
  $server_url = get_server_url();

  $sql = "
    SELECT 
      t.id AS transaction_id, 
      t.created_at, 
      t.transaction_number, 
      t.payment_amount, 
      t.payment_method,
      CONCAT('$server_url', t.payment_receipt_url) AS payment_receipt_url, 
      t.payment_mode_id,
      pm.payment_channel
    FROM transactions t
  JOIN payment_modes pm ON pm.id = t.payment_mode_id
  JOIN enrollment_transactions et ON et.transaction_id = t.id
  JOIN enrollments e ON e.id = et.enrollment_id
  WHERE e.student_id = ?
  ORDER BY t.created_at $order
  LIMIT $limit OFFSET $offset
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$student_id]);
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $transactions;
}

function get_all_children_transactions(PDO $pdo, string $parent_id, int $limit, int $offset, string $order) {
  $server_url = get_server_url();

  $sql = "
    SELECT 
      t.id AS transaction_id, 
      t.created_at, 
      t.transaction_number, 
      t.payment_amount, 
      t.payment_method,
      CONCAT('$server_url', t.payment_receipt_url) AS payment_receipt_url, 
      t.payment_mode_id,
      pm.payment_channel
    FROM transactions t
  JOIN payment_modes pm ON pm.id = t.payment_mode_id
  JOIN enrollment_transactions et ON et.transaction_id = t.id
  JOIN enrollments e ON e.id = et.enrollment_id
  JOIN parent_student_links psl ON psl.student_id = e.student_id
  WHERE psl.parent_id = ?
  ORDER BY t.created_at $order
  LIMIT $limit OFFSET $offset
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$parent_id]);
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $transactions;
}

?>
