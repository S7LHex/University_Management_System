<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__. "/../../repositories/course_repository.php";
require_once __DIR__. "/../../repositories/teacher_repository.php";
require_once __DIR__. "/../../repositories/student_repository.php";
require_once __DIR__. "/../../repositories/enrollmentrepository.php";
require_once __DIR__. "/../../repositories/paymentRepository.php";
require_once __DIR__. "/../../repositories/notification.php";
require_once __DIR__. "/../../config/db.php";

if(!isset($_GET['course_id'])){
  die('Course not specified');
}
$course_id=$_GET['course_id'];

$pdo=new Database;
$conn=$pdo->connect();


$courseRepo= new CourseRepository($conn);
$teacherRepo= new TeacherRepository($conn);
$studentRepo= new StudentRepository($conn);
$paymentRepo= new PaymentRepository($conn);
$notificationRepo = new Notification($conn);
$enrollRepo= new Enrollment($conn,$paymentRepo,$notificationRepo);

$course=$courseRepo->getCourseBYId($course_id);
if(!$course){
  die('Course not found');
}
$days=$course['days'];
$courseDays=explode(',',$days);

// print_r ($courseDays);

$courseTeachers=$courseRepo->getCourseTeachers($course_id);

$enrolledStudents=$enrollRepo->getCourseStudents($course_id);
$countEnrolledStudents=$enrollRepo->CountCourseStudent($course_id);
$countCourseTeachers=$courseRepo->countCourseTeachers($course_id);

// DURATION
$start_date= new DateTime($course['start_date']);
$end_date= new DateTime($course['end_date']);
$diff=$start_date->diff($end_date);
$duration=($diff->y * 12) + $diff->m;
$days=$diff->d;

// Status
$status='';
$today= new DateTime();
$start= new DateTime($course['start_date']);
$end= new DateTime($course['end_date']);
if($today < $start){
  $status='Upcoming';
}
elseif($today > $end){
  $status='Completed';
}
else{
  $status='Active';
}


?>
<link rel="stylesheet" href="../assets/css/course_details.css">
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <a href="layout.php?page=courses">Courses</a>
  <span>/</span>
  <span class="active">Course Details</span>
</nav>

<div class="course-cards">
  
  <div class="card students-card">
    <img src="../assets/imgs/persons-in-a-class-svgrepo-com.svg" alt="">
    <div class="card-group">
      <p>Students</p>
      <span><?= $countEnrolledStudents ?> / <?= htmlspecialchars($course['max_students']) ?></span>
      <p>Enrolled</p>
    </div>
  </div>

  <div class="card teacher-card">
    <img src="../assets/imgs/user-svgrepo-com.svg" alt="">
    <div class="card-group">
      <p>Teachers</p>
      <span><?= htmlspecialchars($countCourseTeachers) ?></span>
      <p>Assigned</p>
    </div>
  </div>

  <div class="card duration-card">
    <img src="../assets/imgs/date-range-svgrepo-com (1).svg" alt="">
    <div class="card-group">
      <p>Duration</p>
      <span style="display:block ; font-size: 1.3rem;"><?= htmlspecialchars($duration) ?> Months</span>
      <span style="font-size: 1.3rem;"><?= htmlspecialchars($days) ?> Days</span>
    </div>
  </div>

  <div class="card status-card">
    <img src="../assets/imgs/status-updated-svgrepo-com (1).svg" alt="">
    <div class="card-group">
      <p>Status</p>
      <span class="status  <?= $status ?>" ><?= htmlspecialchars($status) ?></span>
    </div>
  </div>

</div>

<div class="course-information">
  <h2>Course Information</h2>
  <div class="info-group">
    <div class="info">
      <p>Course Code</p>
      <span><?= htmlspecialchars($course['code']) ?></span>
    </div>

    <div class="info">
      <p>Course Name</p>
      <span><?= $course['name'] ?></span>
    </div>

    <div class="info">
      <p>Price</p>
      <span><?= htmlspecialchars($course['price']) ?> $</span>
    </div>

    <div class="info">
      <p>Max Students</p>
      <span><?= htmlspecialchars($course['max_students']) ?></span>
    </div>

    <div>
      <p>Current Enrollments</p>
      <span>???</span>
    </div>
  </div>
</div>

<div class="course-information schedule-container">
  <h2>Schedule</h2>
  <div class="info-group">
      <div class="info">
        <p>Start Date</p>
        <span><img src="../assets/imgs/date-range-svgrepo-com.svg" alt=""><?= htmlspecialchars($course['start_date']) ?></span>
      </div>
      <div class="info">
        <p>End Date</p>
        <span><img src="../assets/imgs/date-range-svgrepo-com.svg" alt=""><?= htmlspecialchars($course['end_date']) ?></span>
      </div>
      <div class="info">
        <p>Start Time</p>
        <span><img src="../assets/imgs/time-svgrepo-com.svg" alt=""><?= htmlspecialchars($course['start_time']) ?></span>
      </div>
      <div class="info">
        <p>End Time</p>
        <span><img src="../assets/imgs/time-svgrepo-com.svg" alt=""><?= htmlspecialchars($course['end_time']) ?></span>
      </div>
      <div>
        <p>Days</p>
        <?php foreach($courseDays as $day):?>
        <span class="course-day"><?= htmlspecialchars($day) ?></span>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<div class="table-container details-table">
<h2>Assigned Teacher</h2>

<table border="1">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Major</th>
    <th>Email</th>
  </tr>
  <?php if(empty($courseTeachers)):?>
    <tr>
      <td colspan="4" class="msg-td">No teachers assigned yet</td>
    </tr>
  <?php else: ?>
    <?php foreach($courseTeachers as $teacher):?>
  <tr>
    <td><?= htmlspecialchars($teacher['id']) ?></td>
    <td><?= htmlspecialchars($teacher['first_name']) . "  ". htmlspecialchars($teacher['last_name']) ?></td>
    <td><?= htmlspecialchars($teacher['major']) ?></td>
    <td><?= htmlspecialchars($teacher['email']) ?></td>
  </tr>
  <?php endforeach;?>

  <?php endif;?>

</table>
</div>

<div class="table-container students-details-table">
<h2>Enrolled Students</h2>

<table border="2">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Major</th>
    <th>Email</th>
  </tr>
  <?php if(empty($enrolledStudents)):?>
    <tr>
      <td colspan="4" class="msg-td">No students enrolled yet</td>
    </tr>
  <?php else: ?>
  <?php foreach($enrolledStudents as $student): ?>
  <tr>
    <td><?= htmlspecialchars($student['id']) ?></td>
    <td><?= htmlspecialchars($student['first_name']) ."  ". htmlspecialchars($student['last_name']) ?></td>
    <td><?= htmlspecialchars($student['major']) ?></td>
    <td><?= htmlspecialchars($student['email']) ?></td>
  </tr>
  <?php endforeach; ?>
  <?php endif;?>
</table>
</div>

<a href="layout.php?page=courses" class="btn back-btn"><img src="../assets/imgs/arrow-left-l-svgrepo-com.svg" alt=""> Back to Courses</a>
