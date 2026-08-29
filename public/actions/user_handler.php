<?php
require_once __DIR__."/../../repositories/user_repositories.php";
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

$pdo= new Database;
$conn=$pdo->connect();

$userRepo=new User($conn);

if($_SERVER['REQUEST_METHOD']=="POST"){
  if (isset($_POST['add'])){

    $userName=trim($_POST['user_name']);
    $email=trim($_POST['email']);
    $password=trim($_POST['password']);
    $role=$_POST['role'];

    // $relatedId=$_POST['related_id'];

    if(empty($email) || empty($password)||empty($role)){
      $_SESSION['error']="All fieldes are required.";
      header('Location:../pages/layout.php?page=add_user');
      exit;
    }
    if(strlen($password)<6){
      $_SESSION['error']="Password must be at least 6 chars";
      header('Location:../pages/layout.php?page=add_user');
      exit;
    }
    // elseif(($role==='teacher' || $role==='student') && empty($relatedId)){
    //   $error="Please select a ".$role.".";
    // }
      $result=$userRepo->register($userName,$email,$password,$role);
      if($result=="User has been added Successfully."){
        $_SESSION['success']=$result;
        header('Location:../pages/layout.php?page=users_management');
        exit;
      }else{
        $_SESSION['error']=$result;
        header('Location:../pages/layout.php?page=add_user');
        exit;
      }
  }
    

    if(isset($_POST['edit'])){
      $userId=$_POST['user_id'];
      $userName=trim($_POST['user_name']);
      $email=trim($_POST['email']);
      $role=$_POST['role'];

      $result=$userRepo->editUser($userId,$userName,$email,$role);
      if($result== 'User has been edited successfully.'){
          $_SESSION['success']= $result;
          header('Location:../pages/layout.php?page=users_management');
          exit;
      }else{
        $_SESSION['error']=$result;
        header('Location:../pages/layout.php?page=edit_user&user_id='.$userId);
        exit;
      }
    }

    if(isset($_POST['delete'])){
      $userId=$_POST['user_id'];

      $userRepo->deleteUser($userId);
      $_SESSION['success']="User has been removed successfully.";
      header('Location:../pages/layout.php?page=users_management');
      exit;

      
    }
}
      
      
    












?>
