<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once "../../repositories/course_repository.php";
require_once "../../repositories/teacher_repository.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo=new TeacherRepository($conn);
$teachers=$teacherRepo->getAllTeachers();

?>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=courses">Courses</a>
  <span>/</span>
  <span class="active">Create Course</span>
</nav>

<div class="card">
<h2>Create Course</h2>

<form action="../actions/course_handler.php" method="POST">
  <div class="section">
    <h3>Course Information</h3>
    <div class="course-form-grid">

      <div class="form-group">
        <label for="">Course Code</label>
        <input type="text" name="code" placeholder="Code">
      </div>
      
      <div class="form-group">
        <label for="">Course Name</label>
        <input type="text" name="name" placeholder="Name">
      </div>

      <div class="form-group">
        <label for="">Teachers</label>
        <select name="course_teachers[]" id="teachers" multiple>
          <option value="">Select Teachers</option>
          <?php foreach($teachers as $teacher):?>
            <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['first_name']) ?> (<?= $teacher['major'] ?>)</option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="form-group">
        <label for="">Max Students</label>
        <input type="number" name="max_students" placeholder="Max Students">
      </div>

      <div class="form-group">
        <label for="">Price</label>
        <input type="number" name="price" placeholder="Price">
      </div>

      <div class="form-group">
        <label for="">Total Hours</label>
        <input type="number" name="total_hours" placeholder="Total Hours">
      </div>

    </div>
  </div>

  <div class="section">
    <h3>Schedule</h3>
    <div class="course-form-grid">

      <div class="form-group">
        <label for="">Start Date</label>
        <input type="date" name="start_date" placeholder="Start Date" required>
      </div>
      

      <div class="form-group">
        <label for="">End Date</label>
        <input type="date" name="end_date" placeholder="End Date" required>
      </div>

      <div class="form-group">
        <label for="">Postpone Date</label>
        <input type="date" name="postpone_date" placeholder="Postpone Date">
      </div>

      <div class="form-group">
        <label for="">Start Time</label>
        <input type="time" name='start_time' placeholder="Start Time" required>
      </div>

      <div class="form-group">
        <label for="">End Time</label>
        <input type="time" name='end_time' placeholder="End Time" required>
      </div>
    </div>
  </div>
  <div class="section">
    <h3>Available Days</h3>
    <div class="days-grid">   
      <label class="day-card"><input type="checkbox" name="days[]" value="mon"><span>Monday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="tue"><span>Tuesday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="wed"><span>Wednesday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="thu"><span>Thursday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="fri"><span>Friday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="sat"><span>Saturday</span></label>
      <label class="day-card"><input type="checkbox" name="days[]" value="sun"><span>Sunday</span></label>
    </div>
  </div>
  <div class="form-button">
    <a href="layout.php?page=courses" class="form-btn back-form-link"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt="">Back to Courses</a>
    <button name="create" class="form-btn add-form-btn"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Create Course</button>

  </div>
</form>
</div>