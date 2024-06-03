<?php
require_once "../db.php";

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case "GET":
    try {
      $sql = "SELECT * FROM strands";

      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $strands = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if (!$strands) {
        throw new PDOException("Failed to fetch academic years.", 400);
      }

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched strands.",
        'data' => [
          'strands' => $strands,
        ]
      ]);
    } catch (PDOException $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => $th->getMessage()]);
    }
    break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}

?>
