<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $academic_year_id = $_GET['year'];

    $sql = "
      SELECT e.*, u.first_name, u.middle_name, u.last_name, ay.start_at AS ay_start_at, ay.end_at AS ay_end_at
      FROM enrollments e
      JOIN users u ON e.student_id = u.id
      JOIN academic_years ay ON e.academic_year_id = ay.id
      GROUP BY 
        e.id, e.enrolled_at, e.level, e.section, e.tuition_plan, e.status, e.payment_receipt_url, e.student_id, e.academic_year_id, 
        ay.id, ay.start_at, ay.end_at, ay.status
      ORDER BY e.enrolled_at
    ";

    if (isset($academic_year_id)) {
      $sql = "
        SELECT e.*, u.first_name, u.middle_name, u.last_name, ay.start_at AS ay_start_at, ay.end_at AS ay_end_at
        FROM enrollments e
        JOIN users u ON e.student_id = u.id
        JOIN academic_years ay ON e.academic_year_id = ay.id
        WHERE e.academic_year_id = ?
        GROUP BY 
          e.id, e.enrolled_at, e.level, e.section, e.tuition_plan, e.status, e.payment_receipt_url, e.student_id, e.academic_year_id, 
          ay.id, ay.start_at, ay.end_at, ay.status
        ORDER BY e.enrolled_at
      ";
    }

    $stmt = $pdo->prepare($sql);

    if(isset($academic_year_id)) {
      $stmt->execute([$academic_year_id]);
    } else {
      $stmt->execute();
    }

    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$enrollments) {
      http_response_code(400);
      echo json_encode(['message' => "Failed to fetch enrollments."]);
      exit;
    }

    http_response_code(200);

    echo json_encode(['message' => "Successfully fetched enrollments.", 'data' => ['enrollments' => $enrollments]]);
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
