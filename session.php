<?php
session_start();

header("Content-Type: application/json");
echo json_encode(['has_session' => $_SESSION['session']]);
?>
