<?php

if(session_status()===PHP_SESSION_NONE){
  session_start();
}

if(!isset($_SESSION['user_id'])||$_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}

require_once __DIR__.'/../../config/db.php';
require_once __DIR__.'/../../repositories/user_repositories.php';

$pdo= new Database;
$conn=$pdo->connect();
$userRepo= new User($conn);

$user=$userRepo->getUsersById($_SESSION['user_id']);

$error="";
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edit'])){
  $displayName=trim($_POST['display_name']);
  $email=trim($_POST['email']);

  $userRepo->editUser($_SESSION['user_id'],$displayName,$email);
  $_SESSION['success']="Settings have been edited successfully.";

  header('Location:layout.php?page=account_settings');
  exit;

}
?>

<?php if(isset($_SESSION['success'])):?>
  <div class="success-msg"><?= $_SESSION['success'] ?></div>
<?php unset($_SESSION['success']); ?>  
<?php endif; ?>  

<?php if(isset($error)):?>
  <div class="error-msg"><?= $error ?></div>
<?php endif; ?>  

<div class="card">
  <h2>Edit Account</h2>
  <form action="" method="POST">
    <div class="form-grid">

      <div class="form-group">
        <label for="">Display Name</label>
        <input type="text" name="display_name" value="<?= htmlspecialchars($user['display_name']) ?>">
      </div>

      <div class="form-group">
        <label for="">Email</label>
        <input type="email" name="email" value=<?= htmlspecialchars($user['email']) ?>>
      </div>
    </div>

    <button name="edit" class="form-btn add-form-btn"><img src="../assets/imgs/edit-svgrepo-com.svg" alt="">Edit Data</button>
  </form>
</div>  