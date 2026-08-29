<?php
require_once __DIR__. "/../../config/db.php";
require_once __DIR__. "/../../repositories/enrollmentrepository.php";
require_once __DIR__. "/../../repositories/paymentRepository.php";
require_once __DIR__. "/../../repositories/notification.php";
require_once __DIR__. "/../../helpers/auth.php";

requireRole(['admin']);

$pdo= new Database;
$conn=$pdo->connect();
$paymentRepo= new PaymentRepository($conn);
$notificationRepo = new Notification($conn);

$enrollRepio= new Enrollment($conn,$paymentRepo,$notificationRepo);


if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['enroll'])){
    $studentId=$_POST['student_id'];
    $courseId=$_POST['course_id'];
    $paymentType=$_POST['payment_type'] ?? 'full';
    $numInstallments=$paymentType==='full'?1
    :max(2,min(12,(int) ($_POST['num_installments'] ?? 2))) ;

  if($studentId <=0 || $courseId <=0){
    $_SESSION['error']="Please select both a student and a course.";
  }else{
    $result=$enrollRepio->enrollStudent($studentId,$courseId,$numInstallments);
    if($result=="Student has been enrolled successfully."){
        $_SESSION['success']=$result;
        header('Location:../pages/layout.php?page=enrollment');
        exit;
    }
    else{
      $_SESSION['error']=$result;
      header('Location:../pages/layout.php?page=enrollment');
      exit;
    }
  }
  }
  
  if(isset($_POST['unenroll'])){
    $enrollRepio->unenrollStudent($_POST['student_id'],$_POST['course_id']);
    header('Location:../pages/layout.php?page=enrollment');
    exit;
  }
}

?>