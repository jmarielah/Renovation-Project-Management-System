<?php
session_start();
include 'db_connection.php';

if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];

    // Update the database to set is_active to 0
    $stmt = $conn->prepare("UPDATE projects SET is_active = 0 WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    
    header("Location: projects.php");
    exit();
}
?>