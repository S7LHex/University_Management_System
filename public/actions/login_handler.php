<?php
session_start();
require_once __DIR__."/../../repositories/user_repositories.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn= $pdo->connect();
$user= new User($conn);

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['login'])){

  $email=trim($_POST['email']);
  $password=trim($_POST['password']);

  if(empty($email) || empty($password)){
    $error="Please fill all the fields.";
  }else{
    $result=$user->login($email,$password);
    if($result=="Login Successful."){
      if($_SESSION['role']==='admin'){
        header('Location:../pages/layout.php?page=dashboard');
        exit;
      }elseif($_SESSION['role']=='teacher'){
        header('Location:../pages/teacher_dashboard.php');
      }else{
        header('Location:../pages/student_dashboard.php');
      }
      exit;
    }
      $error=$result;
    
  }
  
}
?>