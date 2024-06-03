<?php
require_once "../../db.php";
require_once "../../file-manager.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':

    $student_id = $_GET['student_id'];
    try {
      $raw_data = file_get_contents('php://input');
      $image_info = getimagesizefromstring($raw_data);

      if(!$image_info) {
        throw new Exception("Failed to get image info.", 500);
      }

      $mime_type = $image_info['mime'];

      $student_id = $_GET['student_id'];
      $storage_dir = "/storage/report-cards";

      $report_card_filename = 'STUDENT' . '_' . $student_id . '_' . 'REPORT-CARD' . '.jpg';
      // The path from the root directory 
      $report_card_path = get_root_dir() . $storage_dir . '/' . $report_card_filename;

      if(file_put_contents($report_card_path, $raw_data) === false) {
        throw new Exception("Failed to upload report card.", 500);
      }

      // The 'URL' that doesn't contain the root directory
      $report_card_url = $storage_dir . '/' . $report_card_filename;

      http_response_code(201); 
      echo json_encode(['message' => "Successfully uploaded report card.", 'data' => ['report_card_url' => $report_card_url]]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
}

function upload_file(string $raw_data, string $storage_dir, string $student_id) {
  try {
    $image_info = getimagesizefromstring($raw_data);

    if(!$image_info) {
      throw new Exception("Failed to get image info.", 500);
    }

    $mime_type = $image_info['mime'];

    $filename = 'STUDENT' . '_' . $student_id . '_' . 'REPORT-CARD' . '.jpg';
    // The path from the root directory 
    $path = get_root_dir() . $storage_dir . '/' . $filename;

    if(file_put_contents($path, $raw_data) === false) {
      throw new Exception("Failed to upload report card.", 500);
    }

    // The 'URL' that doesn't contain the root directory
    $file_url = $storage_dir . '/' . $filename;

    http_response_code(201); 
    echo json_encode(['message' => "Successfully uploaded file.", 'data' => ['file_url' => $file_url]]);
  } catch (\Throwable $th) {
    http_response_code($th->getCode());
    echo json_encode(['message' => $th->getMessage()]);
  }
}

?>
