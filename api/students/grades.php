<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM student_grades
        "
      );

      $stmt->execute();
      $student_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched student grades.",
        'data' => [
          'student_grades' => $student_grades,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch student grades."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $grade = $json_data['grade'];
    $period = $json_data['period'];
    $subject_level_id = $json_data['subject_level_id'];
    $student_id = $json_data['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO student_grades (id, grade, period, subject_level_id, student_id)
        VALUES (uuid(), ?, ?, ?, ?)
        "
      );

      $stmt->execute([$grade, $period, $subject_level_id, $student_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created student grade."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create student grade."]);
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
?>
