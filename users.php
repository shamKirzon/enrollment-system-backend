<?php
require_once "db.php";

$pdo = getConnection();
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

$json_data = json_encode($users);

http_response_code(200);
header('Content-Type: application/json');

echo $json_data;
?>
