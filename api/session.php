<?php
session_start();

function set_session($value) {
  $_SESSION['session'] = $value;
}

header("Content-Type: application/json");
echo json_encode(['has_session' => false]);
?>
