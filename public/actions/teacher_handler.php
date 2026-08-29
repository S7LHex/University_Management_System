<?php
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo=new TeacherRepository($conn);

if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['add'])){

    $first_name=trim($_POST['first_name']);
    $last_name=trim($_POST['last_name']);
    $father_name=trim($_POST['father_name']);
    $age=trim($_POST['age']);
    $address=trim($_POST['address']);
    $mobile_number=trim($_POST['mobile_number']);
    $major=trim($_POST['major']);
    $salary=trim($_POST['salary']);
    $email=trim($_POST['email']);

    if(empty($first_name)||empty($last_name)||empty($father_name)||empty($age)||empty($address)||empty($mobile_number)||empty($major)||empty($salary)||empty($email)){
      $error="Please fill all the fields";
    }else{
      $teacherRepo->addTeacher($first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email);
      define('BASE_URL','/university_system');

      header('Location:'.BASE_URL.'/public/pages/layout.php?page=teachers');
      exit;
    }
  }

  if(isset($_POST['edit'])){
    
    $teacher_id=$_POST['teacher_id'];
    $first_name=trim($_POST['first_name']);
    $last_name=trim($_POST['last_name']);
    $father_name=trim($_POST['father_name']);
    $age=trim($_POST['age']);
    $address=trim($_POST['address']);
    $mobile_number=trim($_POST['mobile_number']);
    $major=trim($_POST['major']);
    $salary=trim($_POST['salary']);
    $email=trim($_POST['email']);

    $teacherRepo->editTeacher($teacher_id,$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email);

    define('BASE_URL','/university_system');
    header('Location:'.BASE_URL.'/public/pages/layout.php?page=teachers');
    exit;
  }
  if(isset($_POST["delete"])){
    $teacher_id=$_POST['teacher_id'];
    $teacherRepo->deleteTeacher($teacher_id);
    define('BASE_URL','/university_system');
    header('Location:'.BASE_URL.'/public/pages/layout.php?page=teachers');
    exit;
  }
}
?>