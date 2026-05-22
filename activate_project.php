<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);

    $conn->begin_transaction();

    try {
        $conn->query("UPDATE projects SET is_active = 0");

        $stmt = $conn->prepare("UPDATE projects SET is_active = 1 WHERE project_id = ?");
        $stmt->bind_param("i", $project_id);
        $stmt->execute();

        $conn->commit();
        
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error updating active project status: " . $conn->error);
    }
} else {
    header("Location: projects.php");
    exit();
}