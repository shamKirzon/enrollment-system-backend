<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $student_id = $_GET['student_id'];

    if(isset($student_id)) {
      try {
        $student_profile = get_student_profile($pdo, $student_id);

        if($student_profile === false) {
          throw new PDOException("Failed to fetch student profile.", 500);
        }

        echo json_encode([
            'message' => "Successfully fetched student profile.",
            'data' => [
                'student_profile' => $student_profile,
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
        SELECT * FROM student_profiles
        "
      );

      $stmt->execute();
      $student_profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched student profiles.",
        'data' => [
          'student_profiles' => $student_profiles,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch student profiles."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $lrn = $json_data['lrn'];
    $birth_date = $json_data['birth_date'];
    $birth_place = $json_data['birth_place'];
    $sex = $json_data['sex'];
    $citizenship = $json_data['citizenship'];
    $religion = $json_data['religion'];
    $parent_contact_number = $json_data['parent_contact_number'];
    $landline = $json_data['landline'];
    $birth_certificate_url = $json_data['birth_certificate_url'];
    $baptismal_certificate_url = $json_data['baptismal_certificate_url'];
    $address_id = $json_data['address_id'];
    $student_id = $json_data['student_id'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO student_profiles (id, lrn, birth_date, birth_place, sex, citizenship, religion, parent_contact_number, landline, birth_certificate_url, baptismal_certificate_url, address_id, student_id)
        VALUES (uuid(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
      );

      $stmt->execute([$lrn, $birth_date, $birth_place, $sex, $citizenship, $religion, $parent_contact_number, $landline, $birth_certificate_url, $baptismal_certificate_url, $address_id, $student_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created student profile."]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to create student profile."]);
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

function get_student_profile(PDO $pdo, string $student_id) {

      $server_url = get_server_url();

  $sql = "
  SELECT 
  sp.id AS student_profile_id,
  sp.lrn,
  sp.birth_date,
  sp.birth_place,
  sp.sex,
  sp.citizenship,
  sp.religion,
  sp.parent_contact_number,
  sp.landline,
  CONCAT('$server_url', sp.birth_certificate_url) AS birth_certificate_url,
  CONCAT('$server_url', sp.baptismal_certificate_url) AS baptismal_certificate_url,
  sp.address_id,
  a.country,
  a.region,
  a.province,
  a.city,
  a.barangay,
  a.street
  FROM student_profiles sp
  LEFT JOIN addresses a ON a.id = sp.address_id
  WHERE student_id = ?
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$student_id]);
  $student_profile = $stmt->fetch(PDO::FETCH_ASSOC);

  return $student_profile;
}
?>
