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

      $student_id = $_GET['student_id']; // NOTE: Should be LRN instead
      $academic_year_id = $_GET['academic_year_id'];
      $year_level_id = $_GET['year_level_id'];

      $mime_type = $image_info['mime'];
      $storage_dir = "/storage/payment-receipts";

      $payment_receipt_filename =  'PAYMENT-RECEIPT_STUDENT' . '_' . $student_id . '_' . $year_level_id . "_" . $academic_year_id . '.jpg';
      // The path from the root directory 
      $payment_receipt_path = get_root_dir() . $storage_dir . '/' . $payment_receipt_filename;

      if(file_put_contents($payment_receipt_path, $raw_data) === false) {
        throw new Exception("Failed to upload payment receipt.", 500);
      }

      // The 'URL' that doesn't contain the root directory
      $payment_receipt_url = $storage_dir . '/' . $payment_receipt_filename;

      http_response_code(201); 
      echo json_encode(['message' => "Successfully uploaded payment receipt.", 'data' => ['payment_receipt_url' => $payment_receipt_url]]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }

    break;
}
?>
