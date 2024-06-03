<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT 
          SUM(COALESCE(t.payment_amount, 0)) AS total_payment_amount, 
          ay.start_at AS academic_year_start_at,
          ay.end_at AS academic_year_end_at
        FROM academic_years ay
        LEFT JOIN enrollments e ON e.academic_year_id = ay.id
        LEFT JOIN enrollment_transactions et ON et.enrollment_id = e.id
        LEFT JOIN transactions t ON t.id = et.transaction_id
        GROUP BY ay.id
        ORDER BY ay.start_at
        "
      );

      $stmt->execute();
      $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched yearly transactions.",
        'data' => [
          'transactions' => $transactions,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch yearly transactions."]);
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
