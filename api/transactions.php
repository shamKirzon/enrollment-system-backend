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
    $student_id = $_GET['student_id'];

    if(isset($student_id)) {
      try {
        $transactions = get_transactions($pdo, $student_id);

        if($transactions === false) {
          throw new PDOException("Failed to fetch transactions.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched transactions.",
            'data' => [
                'transactions' => $transactions,
            ]
        ]);

      } catch (\Throwable $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
      }

      exit;
    }

    $pdo->beginTransaction(); 

    $count_sql = "
      SELECT COUNT(*) AS count
      FROM transactions
    ";

    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute();
    $count = $count_stmt->fetchColumn();

    if (!$count) {
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
    ";

    $sql .= " LIMIT " . $limit . " OFFSET " . $offset;

    $transactions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if (!$transactions) {
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
    break;
  
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}

function get_transactions(PDO $pdo, string $student_id) {
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
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $transactions;
}
?>
