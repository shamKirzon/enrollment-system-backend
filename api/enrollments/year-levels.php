<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;

    try {

      $sql = "
        SELECT 
          ay.id AS academic_year_id, 
          ay.start_at AS academic_year_start_at, 
          ay.end_at AS academic_year_end_at, 
          yl.id AS year_level_id,
          yl.name AS year_level_name,
          yl.education_level AS year_level_education_level,
          str.id AS strand_id,
          str.name AS strand_name
        FROM academic_years ay
        JOIN enrollments e ON ay.id = e.academic_year_id AND e.student_id = ?
        JOIN year_levels yl ON yl.id = e.year_level_id
        LEFT JOIN enrollment_strands es ON es.enrollment_id = e.id
        LEFT JOIN strands str ON str.id = es.strand_id
        ORDER BY ay.start_at DESC
      ";

      $stmt = $pdo->prepare($sql);
      $stmt->execute([$student_id]);
      $academic_year_enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($academic_year_enrollments === false) {
        $pdo->rollBack(); 
        http_response_code(400);
        echo json_encode(['message' => "Failed to fetch enrolled academic years."]);
        exit;
      }

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched enrolled academic years.",
        'data' => [
          'enrolled_year_levels' => $academic_year_enrollments
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;

  case 'POST':
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
