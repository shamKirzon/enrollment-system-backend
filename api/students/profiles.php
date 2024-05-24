<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
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
?>
