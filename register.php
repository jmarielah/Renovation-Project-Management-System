<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connection.php';

//check connection
if($_SERVER["REQUEST_METHOD"] == "POST"){

$email = $_POST['email'];
$password = $_POST['password'];
$name = $_POST['name'];
$role = $_POST['role'];

$sql = "SELECT email FROM users where email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$email);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0){
    echo "ALREADY EXIST!"; 

}else{
    //HASH THE PASSWORD
    $hashedPassword = password_hash($password,PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(email, password, name, role) VALUES (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss",$email,$hashedPassword,$name,$role);

    if($stmt->execute()){
        $_SESSION['success'] = "Registered successfully!";
        header("Location: login.php");
        exit();  
    }else{
        echo " Error";
    }

}

}
?>