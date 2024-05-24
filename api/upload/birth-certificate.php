<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':

    try {
      $raw_data = file_get_contents('php://input');
      $image_info = getimagesizefromstring($raw_data);

      if(!$image_info) {
        throw new Exception("Failed to get image info.", 500);
      }

      $mime_type = $image_info['mime'];

      $student_id = $_GET['student_id'];
      $storage_dir = "/storage/birth-certificates";

      $birth_certificate_filename = 'student' . '_' . $student_id . '_' . 'birth-cert' . '.jpg';
      // The path from the root directory 
      $birth_certificate_path = get_root_dir() . $storage_dir . '/' . $birth_certificate_filename;

      if(file_put_contents($birth_certificate_path, $raw_data) === false) {
        throw new Exception("Failed to upload payment receipt.", 500);
      }

      // The 'URL' that doesn't contain the root directory
      $birth_certificate_url = $storage_dir . '/' . $birth_certificate_filename;

      http_response_code(201); 
      echo json_encode(['message' => "Successfully uploaded birth certificate.", 'data' => ['birth_certificate_url' => $birth_certificate_url]]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
}
?>
