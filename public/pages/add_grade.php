<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/student_repository.php";
require_once __DIR__."/../../repositories/course_repository.php";

$pdo= new Database;
$conn= $pdo->connect();

$courseRepo=new CourseRepository($conn);
$studentRepo=new StudentRepository($conn);


if(!isset($_GET['student_id']) || !isset($_GET['course_id']) ||!isset($_GET['teacher_id'])){
  die('Missing Parameters');
}

$course_id=$_GET['course_id'];
$student_id=$_GET['student_id'];
$teacher_id=$_GET['teacher_id'];

$student=$studentRepo->findStudentById($student_id);
$course=$courseRepo->getCourseBYId($course_id);
if(!$student||!$course){
  die('Student or course not found');
}

?>
<link rel="stylesheet" href="../assets/css/add-grade.css">

<div class="portal-container">
  <div class="form-card">
    <h2>Add/Edit Grade</h2>

    <?php if(isset($_SESSION['error'])):?>
      <div class="error-msg"><?htmlspecialchars($_SESSION['erro'])?></div>
      <?php unset($_SESSION['error']);?>
      <?php endif;?>

      <form action="../actions/grade_handler.php" method="POST" class="edit-add-form">
          <input type="hidden" name="student_id" value="<?= $student_id ?>">
          <input type="hidden" name="course_id" value="<?= $course_id ?>">
          <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">

          <div class="form-group">
            <label for="">Student</label>
            <input type="text" name="name" value="<?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?>" readonly>
          </div>

          <div class="form-group">
            <label for="">Course</label>
            <input type="text" name="course" value="<?=htmlspecialchars($course['name'] )?>" readonly>
          </div>

          <div class="form-group">
            <label for="">Grade (0 - 100)</label>
            <input type="number" name="grade" placeholder="Grade" min="0" max="100" required>
          </div>

          <div class="form-buttons">
            <a href="course_students.php?course_id=<?= $course_id ?>&teacher_id=<?= $teacher_id ?>" class="back-btn"><img src="../assets/imgs/arrow-left-svgrepo-com.svg" alt="">Back</a>  
            <button name="save"  class="btn-save">Save Grade</button>   
          </div>

      </form>
  </div>
</div>
