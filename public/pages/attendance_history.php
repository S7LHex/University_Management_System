<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/attendance_sessions.php";
require_once __DIR__."/../../repositories/attendance_recordes.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/teacher_repository.php";


$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo= new TeacherRepository($conn);
$sessionModel= new AttendanceSession($conn);
$courseRepo= new CourseRepository($conn);

$teacherId=$_SESSION['related_id'];

if ($teacherId!=$_SESSION['related_id']){
  die('Access denied');
}

$teacherCourses=$teacherRepo->getTeacherCourses($teacherId);
if(!$teacherCourses){
  die('No courses found.');
}

$courseId=isset($_GET['course_id'])?(int)$_GET['course_id']:0;

$selectedCourse=$courseRepo->getCourseBYId($courseId);
$teacherCourse=$teacherRepo->getTeacherCourse($teacherId,$courseId);

// print_r($course);
$sessions=$courseId?$sessionModel->getSessionsByCourse($courseId):[];
// if(!$sessions){
//   die('No sessions found.');
// }


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/teacher_dashboard.css">

  <title>Document</title>
</head>
<body>
<div class="portal-container">
  <h2>Attendance History</h2>

    <nav class="breadcrumb">
    <a href="teacher_dashboard.php"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
    <span>/</span>
    <span class="active">Attendance History</span>
  </nav>

  <div class="table-container">

  <form action="" method="GEt">
    <div class="form-group">
      <label for="">Select Course</label>
      <select name="course_id" id="" onchange="this.form.submit()">
        <option value="">Select Course</option>
        <?php foreach($teacherCourses as $course):?>
          <option value="<?= $course['course_id'] ?>" <?= $courseId==$course['course_id']?'selected':'' ?>><?= htmlspecialchars($course['course_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <?php if(isset($_GET['course_id'])):?>
      <div class="info-card">

      <div class="course-card">
        <img src="../assets/imgs/copy-course-svgrepo-com.svg" alt="">
        <div>
          <h2><?= htmlspecialchars($selectedCourse['name'])?></h2>
          <p><?= htmlspecialchars($selectedCourse['code']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/users-svgrepo-com (1).svg" alt="">
        <div>
          <h2>Students</h2>
          <p><?= htmlspecialchars($teacherCourse['students_count'].' / '.$selectedCourse['max_students']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/history-svgrepo-com.svg" alt="">
        <div>
          <h2>Schedule</h2>
          <p><?= htmlspecialchars($selectedCourse['days']) ?></p>
          <p><?= htmlspecialchars($selectedCourse['start_time'] | $selectedCourse['end_time']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/date-range-svgrepo-com (2).svg" alt="">
        <div>
          <h2>Duration</h2>
          <p><?= htmlspecialchars($selectedCourse['start_date']) ?></p>
          <p><?= htmlspecialchars($selectedCourse['end_date']) ?></p>
        </div>
      </div>
  </div>
  <?php endif; ?>
  
    <table border=1>
      <thead>
        <tr>
          <th>Session Date</th>
          <th>Day</th>
          <th>Attendance</th>
          <th>Rate</th>
          <th>Edit</th>
        </tr>
        <tbody>
          <?php if(!isset($_GET['course_id'])):?>
          <tr>
            <td colspan="3" style="text-align: left;"><p>Please select course to show attendance history.</p></td>
          </tr>
          <?php elseif(!$sessions):?>
            <tr>
              <td colspan="3" style="text-align: left;"><p>No sessions found yet</p></td>
            </tr>
            <?php else:?>
  
            <?php foreach($sessions as $session):?>
              <?php 
              $color='';
              $rate=0;
              $present= (int)$session['present_count'];
              $totalStudents= (int)$session['students_count'];
              if ($totalStudents>0){
                $rate=($present / $totalStudents)*100;
              }
              // if($rate <= 59){
              //   $color='#b91c1c';
              // }elseif($rate >= 60 && $rate <=79){
              //   $color='orange';
              // }else{
              //   $color='green';
              // }
              ?>
            <tr>
              <td><?= htmlspecialchars($session['session_date']) ?></td>
              <td><?= htmlspecialchars($session['session_day']) ?></td>
              <td><?= htmlspecialchars($session['present_count']) ?> (Present) / <?= htmlspecialchars($session['students_count']) ?> (Students)</td>
              <td><?= round($rate) ?>%
              <!-- <div><div style="width:<?= round($rate) ?>;background:<?= $color ?>"><?= round($rate) ?>%</div></div> -->
            </td>
              <td><a href="teacher_attendance.php?course_id=<?= $courseId ?>&teacher_id=<?= $teacherId ?>" class="btn-save">Edit Attendance</a></td>
            </tr>
            <?php endforeach;?>
          <?php endif; ?>

        </tbody>
      </thead>
    </table>
        <a href="teacher_dashboard.php" class="back-btn"><img src="../assets/imgs/arrow-left-svgrepo-com.svg" alt="">Back to Dashboard</a>  

    </div>
</form>
</div>
</body>
</html>