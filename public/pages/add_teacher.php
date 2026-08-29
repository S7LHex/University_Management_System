<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

?>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=teachers">Teachers</a>
  <span>/</span>
  <span class="active">Add Teacher</span>
</nav>
<div class="card">
<h2>Add New Teacher</h2>
<?php if(isset($error)):?>
  <div>
    <p class="error-msg"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif;?>
<form action="../actions/teacher_handler.php" method="POST">
  <div class="form-grid">

    <div class="form-group">
      <label for="">First Name</label>
      <input type="text" name="first_name" placeholder="First Name">
    </div>

    <div class="form-group">
      <label for="">Last Name</label>
      <input type="text" name="last_name" placeholder="Last Name">
    </div>

    <div class="form-group">
      <label for="">Father's Name</label>
      <input type="text" name="father_name" placeholder="Father Name">
    </div>

    <div class="form-group">
      <label for="">Age</label>
      <input type="number" name="age" placeholder="Age">
    </div>

    <div class="form-group">
      <label for="">Address</label>
      <input type="text" name="address" placeholder="Address">
    </div>

    <div class="form-group">
      <label for="">Mobile Number</label>
      <input type="text" name="mobile_number" placeholder="Mobile Number">
    </div>

    <div class="form-group">
      <label for="">Major</label>
      <input type="text" name="major" placeholder="Major">
    </div>

    <div class="form-group">
      <label for="">Salary</label>
      <input type="number" name="salary" placeholder="Salary">
    </div>

    <div class="form-group">
      <label for="">Email</label>
      <input type="email" name="email" placeholder="email">
    </div>
  </div>
  <div class="form-button">
    <a href="layout.php?page=teachers" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Teachers</a>
    <button name="add" class="form-btn add-form-btn"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Add Teacher</button>
  </div>
</form>
</div>