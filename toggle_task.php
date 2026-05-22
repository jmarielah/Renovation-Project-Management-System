<?php
session_start();
if (!isset($_SESSION['userID'])) { die(json_encode(['success' => false])); }

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update_id = isset($_POST['update_id']) ? intval($_POST['update_id']) : 0;
    $is_done = isset($_POST['is_done']) ? intval($_POST['is_done']) : 0;

    $stmt = $conn->prepare("UPDATE project_updates SET is_done = ? WHERE update_id = ?");
    $stmt->bind_param("ii", $is_done, $update_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}