<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__."/../../repositories/paymentRepository.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn= $pdo->connect();

$paymentRepo=new PaymentRepository($conn);
$courseRepo=new CourseRepository($conn);

if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['mark_as_paid'])){

    $paymentId=(int) $_POST['payment_id'];
    $paymentMethod=$_POST['payment_method'] ?? "";
    $notes=trim($_POST['notes'] ?? '');

    $validMethods=['cash','bank_transfer','card'];
    if(!in_array($paymentMethod,$validMethods,true)){
      $_SESSION['error']="Please select available method to pay.";
      header('Location:../pages/layout.php?page=payments');
      exit;
    }else{
      $payment=$paymentRepo->getPaymentById($paymentId);
      if(!$payment){
        $_SESSION['error']="Installment is not found."; 
        header('Location:../pages/layout.php?page=payments');
        exit; 
      }
      elseif($payment['status']!=='unpaid'){
        $_SESSION['error']="This installment is marked as " . $payment['status'];
        header('Location:../pages/layout.php?page=payments');
        exit;
      }
      else{
        $success=$paymentRepo->markAsPaid($paymentId,$paymentMethod,$notes ?: null);
        if($success){
          $_SESSION['success']="Installment paid successfully.";
          header('Location:../pages/layout.php?page=payments');
          exit;
        }else{
          $_SESSION['error']="Error,please try again";
          header('Location:../pages/layout.php?page=payments');
          exit;
        }
      }
    }
  }
  
  // if(isset($_POST['mark_paid'])){
  //   $payment_id=$_POST['payment_id'];
  //   $course_id=$_POST['course_id'];
  //   $payment_date=$_POST['payment_date'];
  //   $payment_method=$_POST['payment_method'];
  //   $notes=$_POST['notes']??null;
  //   if(empty($payment_date) ||empty($payment_method)){
  //     $error="Please fill fields";
  //   }else{
  //     $paymentRepo->markAsPaid($payment_id,$payment_date,$payment_method,$notes);
  //     header('Location:../pages/layout.php?page=payments&course_id='.$course_id);
  //     exit;
  //   }
  // }
  if(isset($_POST['delete_payment'])){
    $payment_id=(int)$_POST['payment_id'];
    $payment=$paymentRepo->getPaymentById($payment_id);
    if(!$payment){
      $message="Payment not found.";
      $messageType='error';
    }

    elseif($payment['status']!=='cancelled'){
      $message="Only camcelled payments can be deleted.";
      $messagetype="error";
    }else{
      $paymentRepo->deleteInstallment($payment_id);
      header('Location:../pages/layout.php?page=payments');
      exit;
    }
  }
}
?>