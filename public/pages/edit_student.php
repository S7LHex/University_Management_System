<?php
require_once __DIR__. "/../../repositories/student_repository.php";
require_once __DIR__. "/../../config/db.php";

if(!isset($_GET['id'])){
  header('Location:students.php');
  exit;
}
$student_id=$_GET['id'];

$pdo=new Database;
$conn=$pdo->connect();

$studentRepo=new StudentRepository($conn);

$student=$studentRepo->findStudentById($student_id);
if(!$student){
  die('Student not found');
}

?>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=students">Students</a>
  <span>/</span>
  <span class="active">Edit student</span>
</nav>

<div class="card">
<h2>Edit Student</h2>

<form action="../actions/student_handler.php" method="POST">
  <input type="hidden" name="student_id" value="<?= $student_id ?>">

  <div class="form-grid">
    
    <div class="form-group">
      <label for="">First Name</label>
      <input type="text" name="first_name" value="<?= $student['first_name'] ?>">
    </div>

    <div class="form-group">
      <label for="">Last Name</label>
      <input type="text" name="last_name" value="<?= $student['last_name'] ?>">
    </div>

    <div class="form-group">
      <label for="">Father's Name</label>
      <input type="text" name="father_name" value="<?= $student['father_name'] ?>">
    </div>

    <div class="form-group">
      <label for="">Age</label>
      <input type="number" name="age" value="<?= $student['age'] ?>">
    </div>

    <div class="form-group">
      <label for="">Address</label>
      <input type="text" name="address" value="<?= $student['address'] ?>">
    </div>

    <div class="form-group">
      <label for="">Mobile number</label>
      <input type="text" name="mobile_number" value="<?= $student['address'] ?>">
    </div>

    <div class="form-group">
      <label for="">Major</label>
      <input type="text" name="major" value="<?= $student['major'] ?>">
    </div>

    <div class="form-group">
      <label for="">Email</label>
      <input type="email" name="email" value="<?= $student['email'] ?>">
    </div>
  </div>
  <div class="form-button">
    <a href="layout.php?page=students"  class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Students</a>
    <button name="edit" class="form-btn add-form-btn"><img src="../assets/imgs/edit-svgrepo-com.svg">Edit Student</button>
  </div>
</form>
</div>