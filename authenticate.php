<?php
session_start();
include 'db_connection.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        if(password_verify($password,$row['password'])){

            $_SESSION['userID'] = $row['user_id'];
            $_SESSION['email'] = $row['email'];

            header("Location:home.php");
            exit();
        }else{
            echo "Incorrect password.";
        }
    }else{
        echo "User not found.";
    }
}