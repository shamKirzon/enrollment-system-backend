<?php
require_once "../../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $year_level_id = $_GET['year_level_id'];
    $academic_year_id = $_GET['academic_year_id'];

    try {
      $sql = "
        SELECT 
          sa.id AS section_assignment_id,
          sa.enrollment_id,
          s.name AS section_name,
          u.first_name,
          u.middle_name,
          u.last_name,
          u.suffix_name
        FROM section_assignments sa
        JOIN enrollments e
        JOIN section_levels sl ON sl.id = sa.section_level_id
        JOIN sections s ON s.id = sl.section_id
        JOIN users u ON u.id = e.student_id
      ";

      $conditions = [];
      $params = [];

      if ($year_level_id !== null) {
        $conditions[] = "e.year_level_id = ?";
        $params[] = $year_level_id;
      }

      if ($academic_year_id !== null) {
        $conditions[] = "e.academic_year_id = ?";
        $params[] = $academic_year_id;
      }

      if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions) . " ";
      }

      $sql .= " GROUP BY s.id";

      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      $section_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched section assignments.",
        'data' => [
          'section_assignments' => $section_assignments,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch section assignments."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $year_level_id = $json_data['year_level_id'];
    $academic_year_id = $json_data['academic_year_id'];

    $pdo->beginTransaction();

    $section_levels_sql = "
      SELECT id FROM section_levels
      WHERE year_level_id = ?
      ORDER BY RAND()
    ";
    $stmt = $pdo->prepare($section_levels_sql);
    $stmt->execute([$year_level_id]);
    $section_levels = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $student_count_per_section_sql = "
      SELECT
      ROUND(
        (
          (
            SELECT COUNT(student_id)
            FROM enrollments
            WHERE status = 'done' AND academic_year_id = ? AND year_level_id = ?
          ) /
          (
            SELECT COUNT(id)
            FROM section_levels
            WHERE year_level_id = ?
          )
        )
      ) AS student_count_per_section
    ";

    $stmt = $pdo->prepare($student_count_per_section_sql);
    $stmt->execute([$academic_year_id, $year_level_id, $year_level_id]);
    $student_count_per_section = (int) $stmt->fetchColumn();

    if($student_count_per_section < 40) {
      $student_count_per_section = 50;
    }

    for ($i=0; $i < count($section_levels); $i++) { 
      $offset = $i * $student_count_per_section;
      $enrollment_sql = "
        SELECT * FROM enrollments
        WHERE 
          academic_year_id = ? 
          AND year_level_id = ? 
          AND id NOT IN (
            SELECT enrollment_id FROM section_assignments
          )
        ORDER BY enrolled_at
        LIMIT $student_count_per_section
        OFFSET $offset
      ";

      $stmt = $pdo->prepare($enrollment_sql);
      $stmt->execute([$academic_year_id, $year_level_id]);
      $student_enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // echo $section_levels[$i];
      foreach ($student_enrollments as $enrollment) {
        $section_assignment_sql = "
          INSERT INTO section_assignments (id, enrollment_id, section_level_id)
          VALUES(uuid(), ?, ?)
        ";

        $stmt = $pdo->prepare($section_assignment_sql);
        $stmt->execute([$enrollment['id'], $section_levels[$i]]);
      }
    }

    $pdo->commit();

    http_response_code(201); 
    echo json_encode([
      'message' => "Successfully assigned sections to students.", 
    ]);

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
