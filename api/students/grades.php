<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = $_GET['student_id'];
    $year_level_id = $_GET['year_level_id'];

    try {
        $pdo->beginTransaction();

        // Prepare the query
        $stmt = $pdo->prepare("
            SELECT 
                s.name AS subject_name,
                sg.grade,
                sg.period,
                AVG(CASE WHEN sg.period IN ('1', '2', '3', '4') THEN sg.grade ELSE NULL END) OVER (PARTITION BY sl.subject_id) AS average_grade,
                yl.education_level
            FROM student_grades sg
            LEFT JOIN subject_levels sl ON sg.subject_level_id = sl.id
            LEFT JOIN subjects s ON sl.subject_id = s.id
            LEFT JOIN year_levels yl ON yl.id = sl.year_level_id
            WHERE sg.student_id = ? AND yl.id = ?
            ORDER BY s.name, sg.period
        ");

        // Execute the query
        $stmt->execute([$student_id, $year_level_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate the final grade (mean of all average grades)
        $subjects = [];
        $total_average = 0;
        $subject_count = 0;
        $education_level = "";

        foreach ($results as $row) {
          $subject_name = $row['subject_name'];
          $education_level = $row['education_level'];

            if (!isset($subjects[$subject_name])) {
                $subjects[$subject_name] = [
                  'subject_name' => $subject_name,
                  'grades' => [],
                  'average_grade' => $row['average_grade']
                ];
                $total_average += $row['average_grade'];
                $subject_count++;
            }

            $subjects[$subject_name]['grades'][] = [
                'grade' => $row['grade'],
                'period' => $row['period']
            ];
        }

        $final_grade = $subject_count > 0 ? $total_average / $subject_count : 0;

        $pdo->commit();

        // Output the results
        http_response_code(200);
        echo json_encode([
          'message' => "Successfully fetched student grades.",
            "data" => [
            'subject_grades' => array_values($subjects),
            'final_grade' => $final_grade,
            "education_level" => $education_level
           ]
        ]);
    } catch (\Throwable $th) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $grade = $json_data['grade'];
    $period = $json_data['period'];
    $subject_level_id = $json_data['subject_level_id'];
    $student_id = $json_data['student_id'];
    $strand_id = $json_data['strand_id'] ?? null; // Optional strand_id

    try {
      $pdo->beginTransaction();

        $check_stmt = $pdo->prepare("
          SELECT 1 FROM student_grades 
          WHERE period = ? AND subject_level_id = ? AND student_id = ?
        ");
      $check_stmt->execute([$period, $subject_level_id, $student_id]);

      if($check_stmt->rowCount() > 0) {
        throw new PDOException("Grade for the subject already exists.", 409);
      }

        // Insert into student_grades
        $stmt = $pdo->prepare("
            INSERT INTO student_grades (grade, period, subject_level_id, student_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$grade, $period, $subject_level_id, $student_id]);

        // Get the ID of the inserted student grade
        $student_grade_id = $pdo->lastInsertId();

        // Insert into student_grade_strands if strand_id is provided
        if ($strand_id !== null) {
            $stmt = $pdo->prepare("
                INSERT INTO student_grade_strands (student_grade_id, strand_id)
                VALUES (?, ?)
            ");
            $stmt->execute([$student_grade_id, $strand_id]);
        }

        // Commit the transaction
        $pdo->commit();

        http_response_code(201);
        echo json_encode(['message' => "Successfully created student grade."]);
    } catch (PDOException $th) {
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
    } catch (Throwable $th) {
        $pdo->rollBack();
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
