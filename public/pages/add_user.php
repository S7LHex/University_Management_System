<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__."/../../repositories/user_repositories.php";
require_once __DIR__."/../../config/db.php";

$pdo = new Database;
$conn=$pdo->connect();
$userRepo= new User($conn);


?>

<h2>Add User</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=users_management">User Management</a>
  <span>/</span>
  <span class="active">Add User</span>
</nav>

<div class="card">
  <h2>Add Information</h2>
  
  <?php if(isset($_SESSION['error'])):?>
    <div class="alert-msg error-msg"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']);?>
    <?php endif;?>

  <form action="../actions/user_handler.php" method="POST">
  <div class="form-grid">

    <div class="form-group">
      <label for="">User name</label>
      <input type="text" name='user_name' placeholder="User Name" require>
    </div>

    <div class="form-group">
      <label for="">Email</label>
      <input type="email" name="email" placeholder="Email" require>
    </div>

    <div class="form-group">
      <label for="">Password</label>
      <input type="password" name="password" placeholder="Password" require>
    </div>

    <div class="form-group">
      <label for="">Role</label>
      <select name="role" id="">
        <option value="">Select Role</option>
        <option value="admin">Admin</option>
        <option value="teacher">Teacher</option>
        <option value="student">Student</option>
      </select>
    </div>
</div>
    <div class="form-button">
      <a href="layout.php?page=users_management" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Users</a>
      <button class="form-btn add-form-btn" name="add"><img src="../assets/imgs/edit-svgrepo-com.svg">Add User</button>
    </div>

  


  </form>
</div>