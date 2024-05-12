<?php
require_once "../db.php";
require_once "../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
    $offset = ($page - 1) * $limit;

    try {
      $pdo->beginTransaction(); 

      $count_sql = "
        SELECT COUNT(*) AS count
        FROM academic_years ay
        LEFT JOIN enrollments e ON ay.id = e.academic_year_id 
        WHERE (ay.status = 'finished' AND e.status = 'done' AND e.student_id = ?) OR ay.status = 'open'
        ORDER BY ay.start_at DESC
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute([$user_id]);
      $count = $count_stmt->fetchColumn();

      if (!$count) {
        $pdo->rollBack(); 
        http_response_code(400);
        echo json_encode(['message' => "No enrolled academic years found."]);
        exit;
      }

      $sql = "
        SELECT 
          ay.id AS academic_year_id, ay.start_at, ay.end_at, ay.status AS academic_year_status, 
          e.tuition_plan, e.status AS enrollment_status, e.section, e.enrolled_at,
          yl.name AS year_level
        FROM academic_years ay
        LEFT JOIN enrollments e ON ay.id = e.academic_year_id 
        LEFT JOIN year_levels yl ON yl.id = e.year_level_id
        WHERE (ay.status = 'finished' AND e.status = 'done' AND e.student_id = ?) OR ay.status = 'open'
        ORDER BY ay.start_at DESC
      ";

      $sql .= " LIMIT " . $limit . " OFFSET " . $offset;

      $stmt = $pdo->prepare($sql);
      $stmt->execute([$user_id]);
      $academic_year_enrollment = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($academic_year_enrollment === false) {
        $pdo->rollBack(); 
        http_response_code(400);
        echo json_encode(['message' => "Failed to fetch enrolled academic years."]);
        exit;
      }

      $pdo->commit(); 

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched enrolled academic years.",
        'data' => [
          'academic_year_enrollment' => $academic_year_enrollment,
          'count' => $count
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;

  case 'POST':
    try {
      $json_data = json_decode(file_get_contents('php://input'), true);

      $tuition_plan = $json_data['tuition_plan']; // tuition_plan_id
      $academic_year_id = $json_data['academic_year_id'];
      $year_level_id = $json_data['year_level_id'];
      $student_id = $json_data['student_id'];
      $amount = $json_data['amount'];
      // $transaction_number = $json_data['transaction_number'];
      // $payment_receipt = $_FILES['payment_receipt'];
      //
      // $root_dir = get_root_dir();
      // move_uploaded_file($payment_receipt['tmp_name'], $root_dir);
      // $payment_receipt_url = $root_dir . pathinfo($payment_receipt['name'], PATHINFO_EXTENSION);

      $pdo->beginTransaction(); 

      $uuid_sql = "
      SELECT uuid()
      ";

      $stmt = $pdo->prepare($uuid_sql);
      $stmt->execute();
      $transaction_id = $stmt->fetchColumn();

      $transaction_sql = "
      INSERT INTO transactions (id, amount, payment_receipt_url)
      VALUES (?, ?, ?)
      ";

      $stmt = $pdo->prepare($transaction_sql);
      $stmt->execute([$transaction_id, $amount, 'hello/world']);
      // $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

      $enrollment_sql = "
      INSERT INTO enrollments (tuition_plan, student_id, academic_year_id, year_level_id, transaction_id) 
      VALUES (?, ?, ?, ?, ?)
      ";

      $stmt = $pdo->prepare($enrollment_sql);
      $stmt->execute([$tuition_plan, $student_id, $academic_year_id, $year_level_id, $transaction_id]);

      $pdo->commit(); 

      http_response_code(201); 
      echo json_encode(['message' => "Successfully submitted enrollment.", "foo" => $transaction_id]);
    } catch (\Throwable $th) {
      $pdo->rollBack();
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
  case "PATCH":
    echo get_root_dir();
  break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
