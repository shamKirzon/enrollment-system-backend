<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = $_GET['student_id'];
    $year_level_id = $_GET['year_level_id'] ?? null;
    $semester = isset($_GET['semester']) ? $_GET['semester'] : null; // Optional semester

    try {
        $pdo->beginTransaction();

        // Prepare the query
        $query = "
            SELECT 
                s.name AS subject_name,
                sl.id AS subject_level_id,
                sg.id AS student_grade_id,
                sg.grade,
                sg.period,
                AVG(sg.grade) OVER (PARTITION BY sl.subject_id) AS average_grade,
                yl.education_level,
                ss.semester
            FROM subject_levels sl
            LEFT JOIN subjects s ON sl.subject_id = s.id
            LEFT JOIN year_levels yl ON yl.id = sl.year_level_id
            LEFT JOIN student_grades sg ON sg.subject_level_id = sl.id AND sg.student_id = ?
            LEFT JOIN subject_strands ss ON sl.id = ss.subject_level_id
            WHERE yl.id = ?
            AND (? IS NULL OR ss.semester = ?)
            GROUP BY s.name, sl.id, sg.id, sg.grade, sg.period, yl.education_level, ss.semester";

        $query .= " ORDER BY s.name, sg.period;";

        // Prepare and execute the query
        $stmt = $pdo->prepare($query);
        $stmt->execute([$student_id, $year_level_id, $semester, $semester]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Define all possible periods based on the semester
        $all_periods = $semester == '1' ? ['1', '2'] : ($semester == '2' ? ['3', '4'] : ['1', '2', '3', '4']);

        // Calculate the final grade (mean of all average grades)
        $subjects = [];
        $total_average = 0;
        $subject_count = 0;
        $education_level = "";

        foreach ($results as $row) {
            $subject_name = $row['subject_name'];
            $subject_level_id = $row['subject_level_id'];
            $education_level = $row['education_level'];

            if (!isset($subjects[$subject_name])) {
                $subjects[$subject_name] = [
                    'subject_name' => $subject_name,
                    'subject_level_id' => $subject_level_id,
                    'grades' => [],
                    'average_grade' => $row['average_grade']
                ];
                if ($row['average_grade'] !== null) {
                    $total_average += $row['average_grade'];
                    $subject_count++;
                }
            }

            if (in_array($row['period'], $all_periods)) {
                $subjects[$subject_name]['grades'][] = [
                    'grade' => $row['grade'],
                    'period' => $row['period'],
                    'student_grade_id' => $row['student_grade_id']
                ];
            }
        }

        // Ensure all periods are present for each subject
        foreach ($subjects as &$subject) {
            $existing_periods = array_column($subject['grades'], 'period');
            foreach ($all_periods as $period) {
                if (!in_array($period, $existing_periods)) {
                    $subject['grades'][] = [
                        'grade' => null,
                        'period' => $period
                    ];
                }
            }
            // Sort grades by period to maintain order
            usort($subject['grades'], function($a, $b) {
                return $a['period'] <=> $b['period'];
            });
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

    $many = isset($_GET['many']) ? $_GET['many'] : false;

    if($many === false) {
        create_grade($pdo, $json_data);
        exit;
    }

    create_grades($pdo, $json_data);
    exit;
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

function create_grade(PDO $pdo, mixed $json_data) {
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
}

function create_grades(PDO $pdo, mixed $json_data) {
    $grades = $json_data['grades'];
    $strand_id = $json_data['strand_id'] ?? null; // Optional strand_id
    $student_id = $json_data['student_id']; // Assuming student_id is passed in the main payload

    try {
        $pdo->beginTransaction();

        foreach ($grades as $grade_data) {
            $subject_level_id = $grade_data['subject_level_id'];
            $subject_name = $grade_data['subject_name'];
            $average_grade = $grade_data['average_grade'];
            $grades_array = $grade_data['grades'];

            foreach ($grades_array as $grade_info) {
                $grade = $grade_info['grade'];
                $period = $grade_info['period'];
                $student_grade_id = $grade_info['student_grade_id'] ?? null;

                // Skip if grade is not provided
                if ($grade === null) {
                    continue;
                }

                if ($student_grade_id !== null) {
                    // Check if the grade with the given student_grade_id exists
                    $check_stmt = $pdo->prepare("
                        SELECT 1 FROM student_grades 
                        WHERE id = ? AND student_id = ? AND subject_level_id = ?
                    ");
                    $check_stmt->execute([$student_grade_id, $student_id, $subject_level_id]);

                    if ($check_stmt->rowCount() > 0) {
                        // Update the existing grade
                        $stmt = $pdo->prepare("
                            UPDATE student_grades 
                            SET grade = ?, period = ? 
                            WHERE id = ? AND student_id = ? AND subject_level_id = ?
                        ");
                        $stmt->execute([$grade, $period, $student_grade_id, $student_id, $subject_level_id]);
                        continue;
                    }
                }

                // Insert a new grade
                $stmt = $pdo->prepare("
                    INSERT INTO student_grades (grade, period, subject_level_id, student_id)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$grade, $period, $subject_level_id, $student_id]);

                // Get the ID of the inserted student grade
                $new_student_grade_id = $pdo->lastInsertId();

                // Insert into student_grade_strands if strand_id is provided
                if ($strand_id !== null) {
                    $stmt = $pdo->prepare("
                        INSERT INTO student_grade_strands (student_grade_id, strand_id)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$new_student_grade_id, $strand_id]);
                }
            }
        }

        // Commit the transaction
        $pdo->commit();

        http_response_code(201);
        echo json_encode(['message' => "Successfully created or updated student grades."]);
    } catch (PDOException $th) {
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
    } catch (Throwable $th) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => "Failed to create or update student grades."]);
    }
}
?>
