<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);

    $stmt = $conn->prepare("UPDATE projects SET status = 'completed', is_active = 0 WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    
    if ($stmt->execute()) {
        header("Location: projects.php"); 
        exit();
    } else {
        echo "Error updating project status.";
    }
}
?>