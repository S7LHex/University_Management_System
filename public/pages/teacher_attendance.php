<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/attendance_recordes.php";
require_once __DIR__."/../../repositories/attendance_sessions.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../repositories/teacher_attendance.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/enrollmentrepository.php";
require_once __DIR__."/../../repositories/paymentRepository.php";
require_once __DIR__."/../../repositories/notification.php";
// require_once __DIR__."/../actions/attendance_handler.php";

$pdo=new Database;
$conn=$pdo->connect();

$courseRepo= new CourseRepository($conn);
$attendanceSession=new AttendanceSession($conn );
$attendanceRecors=new AttendanceRecord($conn);
$teacherAttendanceModel=new TeacherAttendance($conn);
$teacherRepo= new TeacherRepository($conn);

$paymentRepo= new PaymentRepository($conn);
$notificationModel=new Notification($conn);
$enrollmentRepo= new Enrollment($conn,$paymentRepo,$notificationModel);


if(!isset($_GET['course_id'])||!isset($_GET['teacher_id'])){
  die('Course not specified');
}

$course_id=$_GET['course_id'];
$teacher_id=$_GET['teacher_id'];

if($_SESSION['related_id']!==$teacher_id){
  die('Teacher not found.');
}

$course=$courseRepo->getCourseBYId($course_id);
$courseStudents=$enrollmentRepo->getCourseStudents($course_id);
$teacherCourse=$teacherRepo->getTeacherCourse($teacher_id,$course_id);



$selectedDate = $_GET['session_date'] ?? $_POST['session_date'] ?? date('Y-m-d');
$existingRecords = [];
$existingSession=$attendanceSession->getByCourseAndDate($course_id,$selectedDate);
if($existingSession){
  foreach($attendanceRecors->getBySession($existingSession['id']) as $rec){
    $existingRecords[$rec['student_id']]=$rec;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../assets/css/teacher_dashboard.css">

</head>
<body>
<div class="portal-container">

  <h2>Students Attendance</h2>

  <nav class="breadcrumb">
    <a href="teacher_dashboard.php"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
    <span>/</span>
    <span class="active">Students Attendance</span>
  </nav>

  <?php if (isset($errors)):?>
    <?php foreach($errors as $error):?>
      <div class="error_msg"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (isset($successMessage)):?>
    <div class="success-msg"><?= htmlspecialchars($successMessage) ?></div>
  <?php endif; ?>

  <div class="info-card">

      <div class="course-card">
        <img src="../assets/imgs/copy-course-svgrepo-com.svg" alt="">
        <div>
          <h2><?= htmlspecialchars($course['name'])?></h2>
          <p><?= htmlspecialchars($course['code']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/users-svgrepo-com (1).svg" alt="">
        <div>
          <h2>Students</h2>
            <p><?= htmlspecialchars($teacherCourse['students_count'] .' / '. $course['max_students']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/history-svgrepo-com.svg" alt="">
        <div>
          <h2>Schedule</h2>
          <p><?= htmlspecialchars($course['days']) ?></p>
          <p><?= htmlspecialchars($course['start_time'] | $course['end_time']) ?></p>
        </div>
      </div>

      <div class="course-card">
        <img src="../assets/imgs/date-range-svgrepo-com (2).svg" alt="">
        <div>
          <h2>Duration</h2>
          <p><?= htmlspecialchars($course['start_date']) ?></p>
          <p><?= htmlspecialchars($course['end_date']) ?></p>
        </div>
      </div>
  </div>

  <?php if($course && $courseStudents):?>
    <form action="../actions/attendance_handler.php" method="POST">
      <input type="hidden" name="course_id" value="<?= $course ['id'] ?>">
      <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
      <input type="hidden" name='session_date' value="<?= htmlspecialchars($selectedDate)  ?>">
      <div class="table-container">
        <?php if(isset($_SESSION['success'])):?>
          <div class="success-msg"><?= htmlspecialchars($_SESSION['success']) ?></div>
          <?php unset($_SESSION['success']);
            endif; ?>

        <?php if(isset($_SESSION['error'])):?>
          <div class="error-msg"><?= htmlspecialchars($_SESSION['error']) ?></div>
          <?php unset($_SESSION['error']);
            endif; ?>

      <table border=1>
        <thead>
          <tr>
            <th>Student</th>
            <th>Status</th>
            <th>notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($courseStudents as $student):
            $currentStatus = $existingRecords[$student['id']]['status'] ?? 'present';
            $currentNotes  = $existingRecords[$student['id']]['notes'] ?? '';
          ?>
          <tr>
            <td><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></td>
            <td class="attendance-status">
              <?php foreach(['pre'=>'present','abs'=>'absent','lat'=>'late','exc'=>'excused'] as $val=>$label):?>
                <label for="" class="status-option">
                  <input type="radio" name="status[<?= $student['id'] ?>]" value="<?= $label ?>" <?= $currentStatus==$val?'checked':'' ?>>
                  <?= $label ?>
                </label>
              <?php endforeach; ?>  
            </td>
            <td>
              <input type="text" name="notes[<?= $student['id'] ?>]" value="<?= htmlspecialchars($currentNotes) ?>" class="attendance-notes" placeholder="Notes">
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <div class="form-buttons">
        <a href="teacher_dashboard.php" class="back-btn"><img src="../assets/imgs/arrow-left-svgrepo-com.svg" alt="">Back to Dashboard</a>  
        <button type="submit" name="save_attendance" class="btn-save">Save Attendance</button>
    </form>
  </div>
  <?php elseif ($course && !$courseStudents): ?>
    <p>No enrolled students yet.</p>
  <?php endif; ?>
</div>
</body>
</html>