<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':

    try {
      $raw_data = file_get_contents('php://input');
      $student_id = $_GET['student_id'];

      $birth_certificate_url = upload_file($raw_data, "/storage/birth-certificates", $student_id, "BIRTH-CERTIFICATE");

      http_response_code(201); 
      echo json_encode(['message' => "Successfully uploaded birth certificate.", 'data' => ['birth_certificate_url' => $birth_certificate_url]]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
}
?>
