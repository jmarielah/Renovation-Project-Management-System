<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connection.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT user_id, email, password, name FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("SQL Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        if(password_verify($password, $row['password'])){
            $_SESSION['userID'] = $row['user_id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['name'] = $row['name'];

            header("Location: home.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }
}
?>