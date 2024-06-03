
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

        $stmt = $pdo->prepare("
            SELECT 
                str.id AS strand_id,
                str.name AS strand_name
            FROM 
                enrollments e
            JOIN 
                enrollment_strands es ON e.id = es.enrollment_id
            JOIN 
                strands str ON es.strand_id = str.id
            WHERE 
                e.student_id = ?
                AND e.year_level_id = ?
                AND e.status = 'done'
            ORDER BY 
                e.enrolled_at DESC
            LIMIT 1;
        ");

        $stmt->execute([$student_id, $year_level_id]);
        $strand = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdo->commit();

        if ($strand) {
            http_response_code(200);
            echo json_encode([
                'message' => "Successfully fetched student strand.",
                'data' => $strand
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'No strand found for the given student and year level.']);
        }
    } catch (PDOException $th) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => $th->getMessage()]);
    } catch (Throwable $th) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['message' => "Failed to fetch student strand."]);
    }
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
