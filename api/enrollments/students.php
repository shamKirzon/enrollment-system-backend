<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    $offset = ($page - 1) * $limit;

    try {
      $pdo->beginTransaction(); 

      $count_sql = "
        SELECT COUNT(*) AS count
        FROM academic_years ay
        LEFT JOIN enrollments e ON ay.id = e.academic_year_id AND e.student_id = ?
        WHERE ay.status = 'open'
        ORDER BY ay.start_at DESC
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute([$student_id]);
      $count = $count_stmt->fetchColumn();

      if ($count === false) {
        $pdo->rollBack(); 
        http_response_code(400);
        echo json_encode(['message' => "No enrolled academic years found."]);
        exit;
      }

      $sql = "
        SELECT 
          ay.id AS academic_year_id, 
          ay.start_at AS academic_year_start_at, 
          ay.end_at AS academic_year_end_at, 
          ay.status AS academic_year_status, 
          e.id AS enrollment_id, 
          e.enrolled_at, 
          e.status AS enrollment_status, 
          yl.name AS year_level_name,
          s.name AS section_name,
          str.id AS strand_id,
          str.name AS strand_name
        FROM academic_years ay
        LEFT JOIN enrollments e ON ay.id = e.academic_year_id AND e.student_id = ?
        LEFT JOIN year_levels yl ON yl.id = e.year_level_id
        LEFT JOIN section_assignments sa ON sa.enrollment_id = e.id
        LEFT JOIN section_levels sl ON sl.id = sa.section_level_id
        LEFT JOIN sections s ON s.id = sl.section_id
        LEFT JOIN enrollment_strands es ON es.enrollment_id = e.id
        LEFT JOIN strands str ON str.id = es.strand_id
        WHERE ay.status = 'open'
        ORDER BY ay.start_at DESC
      ";

      $sql .= " LIMIT " . $limit . " OFFSET " . $offset;

      $stmt = $pdo->prepare($sql);
      $stmt->execute([$student_id]);
      $academic_year_enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($academic_year_enrollments === false) {
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
          'academic_year_enrollments' => $academic_year_enrollments,
          'count' => $count
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $student_id = $json_data['student_id'];
    $academic_year_id = $json_data['academic_year_id'];
    $year_level_id = $json_data['year_level_id'];

    $sql = "
      INSERT INTO enrollments (id, student_id, academic_year_id, year_level_id) 
      VALUES (?, ?, ?, ?)
    ";

    try {
      $stmt = $pdo->query("SELECT uuid()");
      $enrollment_id = $stmt->fetchColumn();

      $stmt = $pdo->prepare($sql);
      $stmt->execute([$enrollment_id, $student_id, $academic_year_id, $year_level_id]);


      http_response_code(201); 
      echo json_encode([
        'message' => "Successfully submitted enrollment.",
        'data' => [
          "enrollment_id" => $enrollment_id
        ]
      ]);
    } catch (\Throwable $th) {
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
