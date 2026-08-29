<?php
require_once __DIR__ ."/../../repositories/teacher_repository.php";
require_once __DIR__ ."/../../config/db.php";

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

if(!isset($_GET['teacher_id'])){
  header('Location:teachers.php');
  exit;
}
$teacher_id=$_GET['teacher_id'];

$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo=new TeacherRepository($conn);
$teacher=$teacherRepo->findTeacherById($teacher_id);

?>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=teachers">Teachers</a>
  <span>/</span>
  <span class="active">Edit Teacher</span>
</nav>
<div class="card">
<h2>Edit Teacher</h2>
<form action="../actions/teacher_handler.php" method='POST'>
  <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
  <div class="form-grid">

    <div class="form-group">
      <label for="">First Name</label>
      <input type="text" name="first_name" value="<?= $teacher['first_name'] ?>">

    </div>
  
    <div class="form-group">
      <label for="">Last Name</label>
      <input type="text" name="last_name" value="<?= $teacher['last_name'] ?>">

    </div>

    <div class="form-group">
      <label for="">Father's Name</label>
      <input type="text" name="father_name" value="<?= $teacher['father_name'] ?>">

    </div>

    <div class="form-group">
      <label for="">Age</label>
      <input type="number" name="age" value="<?= $teacher['age'] ?>">

    </div>

    <div class="form-group">
      <label for="">Address</label>
      <input type="text" name="address" value="<?= $teacher['address'] ?>">
    </div>

    <div class="form-group">
      <label for="">Mobile Number</label>
      <input type="text" name="mobile_number" value="<?= $teacher['mobile_number'] ?>">
    </div>

    <div class="form-group">
      <label for="">Major</label>
      <input type="text" name="major" value="<?= $teacher['major'] ?>">
    </div>

    <div class="form-group">
      <label for="">Salary</label>
      <input type="number" name="salary" value="<?= $teacher['salary'] ?>">
    </div>

    <div class="form-group">
      <label for="">Email</label>
      <input type="email" name="email" value="<?= $teacher['email'] ?>">
    </div>
  </div>
  <div class="form-button">
    <a href="layout.php?page=teachers" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Teachers</a>
    <button name='edit' class="form-btn add-form-btn"><img src="../assets/imgs/edit-svgrepo-com.svg">Edit Teacher</button>
  </div>
</form>
</div>