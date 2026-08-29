<?php
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/student_repository.php";

$pdo= new Database;
$conn=$pdo->connect();
$studentRepo= new StudentRepository($conn);

if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['add'])){
    $first_name=trim($_POST['first_name']);
    $last_name=trim($_POST['last_name']);
    $father_name=trim($_POST['father_name']);
    $age=trim($_POST['age']);
    $address=trim($_POST['address']);
    $mobile_number=trim($_POST['mobile_number']);
    $major=trim($_POST['major']);
    $email=trim($_POST['email']);

    if(empty($first_name)||empty($last_name)||empty($father_name)||empty($age)||empty($address)||empty($mobile_number)||empty($major)||empty($email)){
      $error="Please fill all the fields";
    }else{
      $studentRepo->addStudent($first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email); 
      // مسار نسبي
      // header('Location:../pages/layout.php?page=students');
      // exit;
      define('BASE_URL','/university_system');
      header('Location:'.BASE_URL.'/public/pages/layout.php?page=students');
      exit;
    }
    }
  

  if(isset($_POST['edit'])){
    $student_id=$_POST['student_id'];
    $first_name=trim($_POST['first_name']);
    $last_name=trim($_POST['last_name']);
    $father_name=trim($_POST['father_name']);
    $age=trim($_POST['age']);
    $address=trim($_POST['address']);
    $mobile_number=trim($_POST['mobile_number']);
    $major=trim($_POST['major']);
    $email=trim($_POST['email']);

    $studentRepo->editStudent($student_id,$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email);
    define('BASE_URL','/university_system');
    header('Location:'.BASE_URL.'/public/pages/layout.php?page=students');
    exit;

  }

  if (isset($_POST['delete-student'])){
    $studentRepo->deleteStudent($_POST['student_id']);
    define('BASE_URL','/university_system');
    header('Location:'.BASE_URL.'/public/pages/layout.php?page=students');
    exit;
  }
}



?>