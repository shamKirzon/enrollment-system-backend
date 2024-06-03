<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = $_GET['student_id'];

    if(isset($student_id)) {
      try {
        $student_family_members = get_family_members($pdo, $student_id);

        if($student_family_members === false) {
          throw new PDOException("Failed to fetch student family members.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched student family members.",
            'data' => [
                'student_family_members' => $student_family_members,
            ]
        ]);

      } catch (\Throwable $th) {
        http_response_code($th->getCode());
        echo json_encode(['message' => $th->getMessage()]);
      }

      exit;
    }

    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM student_family_members
        "
      );

      $stmt->execute();
      $student_family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched student family members.",
        'data' => [
          'student_family_members' => $student_family_members,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch student family members."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $first_name = $json_data['first_name'];
    $middle_name = $json_data['middle_name'];
    $last_name = $json_data['last_name'];
    $suffix_name = $json_data['suffix_name'];
    $relationship = $json_data['relationship'];
    $occupation = $json_data['occupation'];
    $address_id = $json_data['address_id'];
    $student_id = $json_data['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO student_family_members (id, first_name, middle_name, last_name, suffix_name, relationship, occupation, address_id, student_id)
        VALUES (uuid(), ?, ?, ?, ?, ?, ?, ?, ?)
        "
      );

      $stmt->execute([$first_name, $middle_name, $last_name, $suffix_name, $relationship, $occupation, $address_id, $student_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created student family member."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create student family member."]);
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

function get_family_members(PDO $pdo, string $student_id) {
  $sql = "
  SELECT sfm.id AS student_family_member_id,
    sfm.first_name,
    sfm.middle_name,
    sfm.last_name,
    sfm.suffix_name,
    sfm.relationship,
    sfm.occupation,
    sfm.address_id,

    a.country,
    a.region,
    a.province,
    a.city,
    a.barangay,
    a.street

    FROM student_family_members sfm
    JOIN addresses a
    WHERE student_id = ?
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$student_id]);
  $student_family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $student_family_members;
}
?>
