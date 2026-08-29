<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once  __DIR__."/../../config/db.php";
require_once  __DIR__."/../../repositories/user_repositories.php";

$pdo = new Database();
$conn=$pdo->connect();
$userRepo = new User($conn);

$userId= isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

$user=$userRepo->getUsersById($userId);

?>

<h2>Edit User</h2>

<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=users_management">User Management</a>
  <span>/</span>
  <span class="active">Edit User</span>
</nav>

<div class="card">
  <h2>Edit Informations</h2>
  
  <?php if(isset($_SESSION['error'])):?>
    <div class="alert-msg error-msg"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error'])?>
  <?php endif; ?>

  

  <form action="../actions/user_handler.php" method="POST">
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

  <div class="form-grid">
    <div class="form-group">
      <label for="">User Name</label>
      <input type="text" name='user_name' value="<?= $user['display_name'] ?>">
    </div>

    <div class="form-group">
      <label for="">Email</label>
      <input type="email" name="email" value="<?= $user['email'] ?>">
    </div>
    <div class="form-group">
      <label for="">Role</label>
      <select name="role" id="">
        <option value="admin"   <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
        <option value="teacher" <?= $user['role']=='teacher'?'selected':'' ?>>Teacher</option>
        <option value="student" <?= $user['role']=='student'?'selected':'' ?>>Student</option>
      </select>
    </div>
  </div>
    <div class="form-button">
      <a href="layout.php?page=users_management" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Users</a>
      <button class="form-btn add-form-btn" name="edit"><img src="../assets/imgs/edit-svgrepo-com.svg">Edit User</button>
    </div>
  </div>

  </form>
</div>