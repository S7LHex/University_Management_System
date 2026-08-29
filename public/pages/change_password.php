<?php
if(session_status()===PHP_SESSION_NONE){
  session_start();
}

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:Login.php');
  exit;
}

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/user_repositories.php";

$pdo= new Database;
$conn=$pdo->connect();
$userRepo=new User($conn);


$user=$userRepo->getUsersById($_SESSION['user_id']);
$currentPassword=$user['password'];

$error="";

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['change_password'])){

  $oldPassword=trim($_POST['old_password']);
  $newPassword=trim($_POST['new_password']);
  $confirmPassword=trim($_POST['confirm_password']);

  if(!password_verify($oldPassword,$currentPassword)){
    $error="wrong password";
  }
  elseif(strlen($newPassword)< 6){
    $error="Password must be at least 6 chars.";
  }
  elseif($newPassword !== $confirmPassword){
    $error="Password not match !";
  }
  else{
    $hashedPassword=password_hash($confirmPassword,PASSWORD_DEFAULT);
    $userRepo->editUserPassword($_SESSION['user_id'],$hashedPassword);

    $_SESSION['success']="Password has been changed successfully.";

    header('Location:layout.php?page=change_password');
    exit;
  }
}

?>
<?php if(isset($_SESSION['success'])):?>
  <div class="success-msg"><?= $_SESSION['success'] ?></div>
  <?php unset($_SESSION['success']);?>
<?php endif; ?>  

<?php if(isset($error)):?>
  <div class="error-msg"><?= $error ?></div>
<?php endif; ?>
  <h2>Change Password</h2> 

<div class="card">
  <form action="" method="POST">
    <div class="password-form-group">

    <div class="form-group">
      <label for="">Enter your psssword</label>
      <input type="password" name="old_password" placeholder="Enter your password">
    </div>

    <div class="form-group">
      <label for="">New Password</label>
      <input type="password" name="new_password" placeholder="New Password">
    </div>

    <div class="form-group">
      <label for="">Confirm Password</label>
      <input type="password" name="confirm_password" placeholder="Confirm Password">
    </div>
  </div>
  <button name="change_password" class="form-btn add-form-btn"><img src="../assets/imgs/edit-svgrepo-com.svg" alt="">Change Password</button>
  </form>

</div>