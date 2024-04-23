<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $academic_year_id = isset($_GET['year']) ? $_GET['year'] : null;
    $enrollment_status = isset($_GET['status']) ? $_GET['status'] : null;
    $year_level_id = isset($_GET['level']) ? $_GET['level'] : null;

    $sql = "
        SELECT e.*, u.first_name, u.middle_name, u.last_name, ay.start_at AS ay_start_at, ay.end_at AS ay_end_at, yl.name AS level
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

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
