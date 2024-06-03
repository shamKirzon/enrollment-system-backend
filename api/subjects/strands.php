<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM subject_strands
        "
      );

      $stmt->execute();
      $subject_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched subject strands.",
        'data' => [
          'subject_strands' => $subject_strands,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch subject_strands."]);
    }
    break;

  case 'POST':
    try {
        $json_data = json_decode(file_get_contents('php://input'), true);

        $subject_level_id = $json_data['subject_level_id'];
        $strand_ids = $json_data['strand_ids'];
        $semesters = $json_data['semesters']; // Assume this is an array of semesters

        $pdo->beginTransaction();

        // Loop through each strand_id and each semester
        foreach ($strand_ids as $strand_id) {
            foreach ($semesters as $semester) {
                try {
                    $subject_strand_id = $subject_level_id . "-" . $strand_id . "-" . $semester;

                    $stmt = $pdo->prepare(
                        "
                        INSERT INTO subject_strands (id, subject_level_id, strand_id, semester)
                        VALUES (?, ?, ?, ?)
                        "
                    );

                    $stmt->execute([$subject_strand_id, $subject_level_id, $strand_id, $semester]);
                } catch (\Throwable $th) {
                    throw new Exception("Failed to create subject strands.", 500);
                }
            }
        }

        $pdo->commit();

        http_response_code(201); 
        echo json_encode(['message' => "Successfully created subject strands."]);
    } catch (\Throwable $th) {
        $pdo->rollBack();
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  case 'PATCH':
    try {
        $json_data = json_decode(file_get_contents('php://input'), true);

        $subject_level_id = $json_data['subject_level_id'];
        $new_strand_ids = $json_data['strand_ids'];
        $new_semesters = $json_data['semesters']; // Array of semesters

        $pdo->beginTransaction();

        // Fetch existing strands and semesters for the given subject_level_id
        $stmt = $pdo->prepare("
            SELECT strand_id, semester
            FROM subject_strands
            WHERE subject_level_id = ?
        ");
        $stmt->execute([$subject_level_id]);
        $existing_subject_strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $existing_strand_semester_pairs = [];
        foreach ($existing_subject_strands as $subject_strand) {
            $existing_strand_semester_pairs[] = [
                'strand_id' => $subject_strand['strand_id'],
                'semester' => $subject_strand['semester']
            ];
        }

        $new_strand_semester_pairs = [];
        foreach ($new_strand_ids as $strand_id) {
            foreach ($new_semesters as $semester) {
                $new_strand_semester_pairs[] = [
                    'strand_id' => $strand_id,
                    'semester' => $semester
                ];
            }
        }

        // Determine pairs to add and remove
        $pairs_to_add = array_udiff($new_strand_semester_pairs, $existing_strand_semester_pairs, function($a, $b) {
            return ($a['strand_id'] === $b['strand_id'] && $a['semester'] === $b['semester']) ? 0 : 1;
        });
        $pairs_to_remove = array_udiff($existing_strand_semester_pairs, $new_strand_semester_pairs, function($a, $b) {
            return ($a['strand_id'] === $b['strand_id'] && $a['semester'] === $b['semester']) ? 0 : 1;
        });

        // Remove outdated subject strands
        if (!empty($pairs_to_remove)) {
            foreach ($pairs_to_remove as $pair) {
                $stmt = $pdo->prepare("
                    DELETE FROM subject_strands
                    WHERE subject_level_id = ? AND strand_id = ? AND semester = ?
                ");
                $stmt->execute([$subject_level_id, $pair['strand_id'], $pair['semester']]);
            }
        }

        // Insert new subject strands
        if (!empty($pairs_to_add)) {
            foreach ($pairs_to_add as $pair) {
                $subject_strand_id = $subject_level_id . "-" . $pair['strand_id'] . "-" . $pair['semester'];
                $stmt = $pdo->prepare("
                    INSERT INTO subject_strands (id, subject_level_id, strand_id, semester)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$subject_strand_id, $subject_level_id, $pair['strand_id'], $pair['semester']]);
            }
        }

        $pdo->commit();

        http_response_code(200);
        echo json_encode([
            'message' => "Successfully updated subject strands.",
            "data" => [
                "subject_level_id" => $subject_level_id,
                "strand_ids" => $new_strand_ids,
                "semesters" => $new_semesters
            ]
        ]);
    } catch (\Throwable $th) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  case 'DELETE':
    break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
