<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $academic_year_id = isset($_GET['year']) ? $_GET['year'] : null;
    $enrollment_status = isset($_GET['status']) ? $_GET['status'] : null;
    $year_level_id = isset($_GET['level']) ? $_GET['level'] : null;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $pdo->beginTransaction(); 

    $countSql = "
      SELECT COUNT(*) AS count
      FROM enrollments e
    ";

    $countConditions = array();
    $countParams = array();

    if ($academic_year_id !== null) {
      $countConditions[] = "e.academic_year_id = ?";
      $countParams[] = $academic_year_id;
    }

    if ($enrollment_status !== null) {
      $countConditions[] = "e.status = ?";
      $countParams[] = $enrollment_status;
    }

    if ($year_level_id !== null) {
      $countConditions[] = "e.year_level_id = ?";
      $countParams[] = $year_level_id;
    }

    if (!empty($countConditions)) {
      $countSql .= " WHERE " . implode(" AND ", $countConditions) . " ";
    }

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $count = $countStmt->fetchColumn();

    if (!$count) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "No enrollments found."]);
      exit;
    }

    $sql = "
      SELECT e.*, u.first_name, u.middle_name, u.last_name, 
        ay.start_at AS ay_start_at, 
        ay.end_at AS ay_end_at, 
        yl.name AS level,
        CASE
          WHEN EXISTS (
              SELECT 1
              FROM enrollments sub_e
              WHERE sub_e.student_id = u.id AND sub_e.id < e.id
          ) THEN 'old'
          ELSE 'new'
        END AS student_status
      FROM enrollments e
      JOIN users u ON e.student_id = u.id
      JOIN academic_years ay ON e.academic_year_id = ay.id
      JOIN year_levels yl ON e.year_level_id = yl.id
    ";

    $conditions = array();
    $params = array();

    if ($academic_year_id !== null) {
        $conditions[] = "e.academic_year_id = ?";
        $params[] = $academic_year_id;
    }

    if ($enrollment_status !== null) {
        $conditions[] = "e.status = ?";
        $params[] = $enrollment_status;
    }

    if($year_level_id !== null) {
        $conditions[] = "e.year_level_id = ?";
        $params[] = $year_level_id;
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions) . " ";
    }

    $sql .= "
      GROUP BY
        e.id, e.enrolled_at, e.section, e.tuition_plan, e.status, e.payment_receipt_url, e.student_id, e.academic_year_id, e.year_level_id,
        ay.id, ay.start_at, ay.end_at, ay.status
      ORDER BY e.enrolled_at
    ";
    $sql .= " LIMIT " . $limit;
    $sql .= " OFFSET " . $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$enrollments) {
      $pdo->rollBack(); 
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch enrollments."]);
      exit;
    }

    $pdo->commit(); 

    http_response_code(200);
    echo json_encode([
      'message' => "Successfully fetched enrollments.",
      'data' => [
        'enrollments' => $enrollments,
        'count' => $count,
      ],
    ]);
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $tuition_plan = $json_data['tuition_plan'];
    $academic_year_id = $json_data['academic_year_id'];
    $year_level_id = $json_data['year_level_id'];
    $student_id = $json_data['student_id'];
    $amount = $json_data['amount'];
    $payment_receipt = $_FILES['payment_receipt'];

    $pdo->beginTransaction(); 

    $transaction_sql = "
    INSERT INTO transactions (id, amount, payment_receipt_url)
    VALUES (uuid(), ?, ?)
    ";

    $stmt = $pdo->prepare($transaction_sql);
    $stmt->execute([$amount, 'hello/world']);
    $transaction = $stmt->fetch();

    $enrollment_sql = "
    INSERT INTO enrollments (tuition_plan, payment_receipt_url, student_id, academic_year_id, year_level_id, transaction_id) 
    VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($enrollment_sql);
    $stmt->execute([$tuition_plan, 'hello/world', $student_id, $academic_year_id, $year_level_id, $transaction['id']]);

    $pdo->commit(); 

    http_response_code(201); 
    echo json_encode(['message' => "Successfully submitted enrollment."]);

    break;
  case 'PATCH':
    $id = $_GET['id'];

    $json_data = json_decode(file_get_contents('php://input'), true);

    $status = $json_data['status'];

    $stmt = $pdo->prepare(
      "
      UPDATE enrollments
      SET status = ?
      WHERE id = ?
      "
    );

    $exec = $stmt->execute([$status, $id]);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to update enrollment."]);
      exit;
    }

    http_response_code(200); 
    echo json_encode(['message' => "Successfully updated enrollment."]);

    break;

  case 'DELETE':
    $id = $_GET['id'];
    $ids = json_decode(file_get_contents('php://input'), true);

    $sql = "
    DELETE FROM enrollments 
    WHERE id IN (
    ";

    if(!empty($ids)) {
      $placeholders = array_fill(0, count($ids), '?');
      $sql .= implode(', ', $placeholders);
    }

    $sql .= ")";

    $stmt = $pdo->prepare($sql);

    $exec = $stmt->execute($ids);

    if(!$exec){
      http_response_code(500);
      echo json_encode(['message' => "Failed to delete enrollment."]);
      exit;
    }

    http_response_code(200); 
    echo json_encode(['message' => "Successfully deleted enrollment."]);
    break;
  
  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
