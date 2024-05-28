<?php
require_once "../../db.php";

$pdo = getConnection();

header('Content-Type: application/json');
 
switch ($_SERVER['REQUEST_METHOD']) {
  case "GET":
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    try {

      $pdo->beginTransaction(); 

      $count_sql = "
        SELECT 
        COUNT(*)
        FROM section_levels sl
        RIGHT JOIN sections s ON s.id = sl.section_id
        LEFT JOIN teachers t ON t.id = sl.adviser_id
        LEFT JOIN year_levels yl ON yl.id = sl.year_level_id
        LEFT JOIN section_strands ss ON ss.section_level_id = sl.id
        LEFT JOIN strands str ON str.id = ss.strand_id
      ";

      $count_stmt = $pdo->prepare($count_sql);
      $count_stmt->execute();
      $count = $count_stmt->fetchColumn();

      if ($count === false) {
        $pdo->rollBack(); 
        throw new Exception("No sections found.", 404);
      }

      $sql = "
        SELECT 
        sl.id AS section_level_id,
        sl.section_id,
        sl.year_level_id,
        sl.adviser_id,
        s.id AS section_id,
        s.name AS section_name,
        t.first_name AS adviser_first_name,
        t.middle_name AS adviser_middle_name,
        t.last_name AS adviser_last_name,
        t.suffix_name AS adviser_suffix_name,
        t.sex AS adviser_sex,
        yl.name AS year_level_name,
        str.id AS strand_id,
        str.name AS strand_name
        FROM section_levels sl
        RIGHT JOIN sections s ON s.id = sl.section_id
        LEFT JOIN teachers t ON t.id = sl.adviser_id
        LEFT JOIN year_levels yl ON yl.id = sl.year_level_id
        LEFT JOIN section_strands ss ON ss.section_level_id = sl.id
        LEFT JOIN strands str ON str.id = ss.strand_id
        LIMIT $limit OFFSET $offset
      ";

      $stmt = $pdo->prepare($sql);

      $stmt->execute();
      $section_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

      http_response_code(200);

      echo json_encode([
        'message' => "Successfully fetched section levels.",
        'data' => [
          'section_levels' => $section_levels,
          'count' => $count
        ]
      ]);
    } catch (\Throwable $th) {
      http_response_code($th->getCode());
      echo json_encode(['message' => "Failed to fetch section levels."]);
    }
    break;

  case "POST":
    $json_data = json_decode(file_get_contents('php://input'), true);

    $year_level_id = $json_data['year_level_id'];
    // $strand_id = $json_data['strand_id'];
    // $section_name = $json_data['name'];
    $section_id = $json_data['section_id'];
    $adviser_id = $json_data['adviser_id'];

    $section_level_id = $section_id . "-" . $year_level_id;

    try {
      $stmt = $pdo->prepare(
        "
        INSERT INTO section_levels (id, section_id, year_level_id, adviser_id)
        VALUES (?, ?, ?, ?)
        "
      );
      $stmt->execute([$section_level_id, $section_id, $year_level_id, $adviser_id]);

      http_response_code(201); 
      echo json_encode(['message' => "Successfully created section level.", 
        "data" => [
          "section_level_id" => $section_level_id
        ]
      ]);
    } catch (\PDOException $th) {
      http_response_code(409);
      echo json_encode(['message' => "Check if section $section_level_id already exists."]);
    }

    // if ($strand_id !== null) {
    //   $section_strand_id = $section_level_id . "-" . $strand_id;
    //
    //   try {
    //     $stmt = $pdo->prepare(
    //       "
    //       INSERT INTO section_strands (id, section_level_id, strand_id)
    //       VALUES (?, ?, ?)
    //       "
    //     );
    //     $stmt->execute([$section_strand_id, $section_level_id, $strand_id]);
    //   } catch (\PDOException $th) {
    //     http_response_code(409);
    //     echo json_encode(['message' => "Section $section_strand_id already exists."]);
    //     break;
    //   }
    // }

  break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}
?>
