<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__. "/../../config/db.php";

$pdo=new Database;
$conn=$pdo->connect();

$teacherRepo= new TeacherRepository($conn);
$courseRepo= new CourseRepository($conn);

$teachers=$teacherRepo->getAllTeachers();


if(!isset($_GET['course_id'])){
  die('Course not found');
}
$course_id=$_GET['course_id'];


$course=$courseRepo->getCourseBYId($course_id);
$currentTeachers=$courseRepo->getCourseTeachers($course_id);
$currentTeacherIds=array_column($currentTeachers,'id');
$currentDays=explode(',',$course['days']);

?>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=courses">Courses</a>
  <span>/</span>
  <span class="active">Edit Course</span>
</nav>
<div class="card">
<h2>Edit Course</h2>
<form action="../actions/course_handler.php" method="POST">
  <input type="hidden" name="course_id" value="<?= $course['id']?>">

  <div class="section">
    <h3>Course Information</h3>
    <div class="course-form-grid">
      <div class="form-group">
        <label for="">Code</label>
        <input type="text" name="code" value="<?= htmlspecialchars($course['code']) ?>">
      </div>

      <div class="form-group">
        <label for="">Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($course['name'] )?>">
      </div>

      <div class="form-group">
        <label for="">Teachers</label>
        <select name="course_teachers[]" id="teachers" multiple>
    <!-- <option value="">Select Teachers</option> -->
          <?php foreach($teachers as $teacher):?>
            <option value="<?= $teacher['id'] ?>" <?= in_array($teacher['id'],$currentTeacherIds)?'selected':'' ?>>
            <?= htmlspecialchars($teacher['first_name']) ?> (<?= htmlspecialchars($teacher['major']) ?>)
            </option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="form-group">
        <label for="">Max Students</label>
        <input type="number" name="max_students" value="<?= htmlspecialchars($course['max_students']) ?>">

      </div>

      <div class="form-group">
        <label for="">Price</label>
        <input type="number" name="price" value="<?= htmlspecialchars($course['price']) ?>">
      </div>

      <div class="form-group">
        <label for="">Total Hours</label>
        <input type="number" name="total_hours" value="<?= htmlspecialchars($course['total_hours']) ?>">
      </div>

    </div>
  </div>
  <div class="section">
    <h3>Schedule</h3>
    <div class="course-form-grid">
      
      <div class="form-group">
        <label for="">Start Date</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($course['start_date']) ?>">

      </div>
    

      <div class="form-group">
        <label for="">End Date</label>
        <input type="date" name="end_date" value="<?=htmlspecialchars( $course['end_date']) ?>">
      </div>
    

      <div class="form-group">
        <label for="">Postpone Date</label>
        <input type="date" name="postpone_date" value="<?= htmlspecialchars($course['postpone_date'] )?>">
      </div>

      <div class="form-group">
        <label for="">Start Time</label>
        <input type="time" name='start_time' value="<?= htmlspecialchars($course['start_time']) ?>">
      </div>
    
      <div class="form-group">
        <label for="">End Time</label>
        <input type="time" name='end_time' value="<?= htmlspecialchars($course['end_time']) ?>">
      </div>
    </div>
  </div>
  <div class="section">
    <h3>Available Days</h3>
    <div class="days-grid">
      <label class="day-card"><input type="checkbox" name="days[]" value="mon"<?= in_array('mon',$currentDays)?'checked':'' ?>>Monday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="tue" <?= in_array('tue',$currentDays)?'checked':'' ?>>Tuesday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="wed" <?= in_array('wed',$currentDays)?'checked':'' ?>>Wednesday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="thu" <?= in_array('thu',$currentDays)?'checked':'' ?>>Thursday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="fri" <?= in_array('fri',$currentDays)?'checked':'' ?>>Friday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="sat" <?= in_array('sat',$currentDays)?'checked':'' ?>>Saturday</label>
      <label class="day-card"><input type="checkbox" name="days[]" value="sun" <?= in_array('sun',$currentDays)?'checked':'' ?>>Sunday</label>
    </div>   
  </div>
  <div class="form-button">
    <a href="layout.php?page=courses" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Courses</a>

    <button name="edit" class="form-btn add-form-btn"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Edit Course</button>    
  </div>


  


</form>
</div>















