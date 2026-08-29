<?php
require_once __DIR__."/../../repositories/installer.php";

$installer=new Installer;

if($installer->adminExists()){
  die('System already installed.');
}
$error='';

if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['create'])){
  $email=trim($_POST['email']);
  $password=trim($_POST['password']);

  if(!empty($email) && !empty($password)){

    if(strlen($password)<6){
      $error="Password must be at least 6 chars.";
    }else{ 
      $hashedPassword=password_hash($password,PASSWORD_DEFAULT);
      $installer->createAdmin($email,$hashedPassword);
      header('Location: login.php');
      exit;
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System installaion</title>
  <link rel="stylesheet" href="../assets/css/register_login.css">

</head>
<body>
  <form action="" method="POST" class="login-container">
  <h2>Create first Administrator</h2>
  <?php if(isset($error)):?>
    <div>
      <p class="error-msg"><?= $error ?></p>
    </div>
  <?php endif; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name='create'>Create</button>
  </form>
</body>
</html>