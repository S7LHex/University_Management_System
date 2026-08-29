<?php
require_once "../actions/login_handler.php";

?>
<link rel="stylesheet" href="../assets/css/register_login.css">

<div class="login-container">

<h2>Login</h2>
<?php if(isset($error)):?>
  <div>
    <p class="error-msg"><?= $error ?></p>
</div>
<?php endif;?>

<form action="" method="POST">
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button name='login'>Login</button>
</form>
</div>