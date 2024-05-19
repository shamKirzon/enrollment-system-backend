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
      http_response_code(400);
      echo json_encode(['message' => "No transactions found."]);
      exit;
    }

    $server_url = get_server_url();

    $sql = "
      SELECT 
        id, amount, 
        CONCAT('$server_url', payment_receipt_url) AS payment_receipt_url,
        created_at
      FROM transactions
    ";

    $sql .= " LIMIT " . $limit;
    $sql .= " OFFSET " . $offset;

    $transactions = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    if (!$transactions) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch transactions."]);
      exit;
    }

    $pdo->commit(); 

    http_response_code(200);

    echo json_encode([
      'message' => "Successfully fetched transactions.",
      'data' => [
        'transactions' => $transactions,
        'count' => $count
      ]
    ]);
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $amount = $json_data['amount'];
    $payment_receipt_url = $json_data['payment_receipt_url'];

    $sql = "
      INSERT INTO transactions (id, amount, payment_receipt_url)
      VALUES (uuid(), ?, ?)
      ";

    $stmt = $pdo->prepare($sql);

    $exec = $stmt->execute([$amount, $payment_receipt_url]);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to insert transaction."]);
      exit;
    }

    http_response_code(201); 
    echo json_encode(['message' => "Successfully inserted transaction."]);

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
