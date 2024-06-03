<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    try {
      $stmt = $pdo->prepare(
        "
        SELECT * FROM addresses
        "
      );

      $stmt->execute();
      $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched tuition addresseses.",
        'data' => [
          'addresses' => $addresses,
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch addresseses."]);
    }
    break;

  case 'POST':
    $json_data = json_decode(file_get_contents('php://input'), true);

    $country = $json_data['country'];
    $region = $json_data['region'];
    $province = $json_data['province'];
    $city = $json_data['city'];
    $barangay = $json_data['barangay'];
    $street = $json_data['street'];

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO addresses (country, region, province, city, barangay, street)
        VALUES (?, ?, ?, ?, ?, ?)
        "
      );

      $stmt->execute([$country, $region, $province, $city, $barangay, $street]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created address."]);
    } catch (\Throwable $th) {
      http_response_code(409);
      echo json_encode(['message' => "Failed to create address."]);
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
