<?php
session_start();
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/attendance_recordes.php";
require_once __DIR__."/../../repositories/attendance_sessions.php";
// require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../repositories/teacher_attendance.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/enrollmentrepository.php";
require_once __DIR__."/../../repositories/notification.php";
require_once __DIR__."/../actions/attendance_handler.php";

$pdo=new Database;
$conn=$pdo->connect();

$courseRepo= new CourseRepository($conn);
$attendanceSession=new AttendanceSession($conn );
$attendanceRecors=new AttendanceRecord($conn);
$enrollmentRepo= new Enrollment($conn);
$teacherAttendanceModel=new TeacherAttendance($conn);
$notificationModel=new Notification($conn);

if(!isset($_SESSION['user_id'])|| $_SESSION['role']!=='teacher'){
  header('Location:login.php');
  exit;
}

if(!isset($_GET['course_id'])||!isset($_GET['teacher_id'])){
  die('Course not specified');
}
$course_id=$_GET['course_id'];
$teacher_id=$_GET['teacher_id'];



if($_SESSION['related_id']!==$teacher_id){
  die('Teacher not found.');
}

// $teacherCourses=$teacherRepo->getTeacherCourses($teacher_id);
// print_r($teacherCourses);
// if(!$teacherCourses){
//   die('No Courses found');
// }
$course=$courseRepo->getCourseBYId($course_id);
$courseStudents=$enrollmentRepo->getCourseStudents($course_id);


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
</head>
<body>
  <h2>Students Attendance</h2>
  <?php if (isset($errors)):?>
    <?php foreach($errors as $error):?>
      <div class="error_msg"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (isset($successMessage)):?>
    <div class="success-msg"><?= htmlspecialchars($successMessage) ?></div>
  <?php endif; ?>

  <p><?= htmlspecialchars($course['name']) ?></p>
  <p>Days: <?= htmlspecialchars($course['days']) ?></p>

  <?php if($course && $courseStudents):?>
    <form action="" method="POST">
      <input type="hidden" name="course_id" value="<?= $course ['id'] ?>">
      <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
      <input type="hidden" name='session_date' value="<?= htmlspecialchars($selectedDate)  ?>">
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
            <td><?= htmlspecialchars($student['first_name']).' '. htmlspecialchars($student['last_name']) ?></td>
            <td>
              <?php foreach(['pre'=>'present','abs'=>'absent','lat'=>'late','exc'=>'excused'] as $val=>$label):?>
                <label for="">
                  <input type="radio" name="status[<?= $student['id'] ?>]" value="<?= $val ?>" <?= $currentStatus==$val?'checked':'' ?>>
                  <?= $label ?>
                </label>
              <?php endforeach; ?>  
            </td>
            <td>
              <input type="text" name="notes[<?= $student['id'] ?>]" value="<?= htmlspecialchars($currentNotes) ?>" placeholder="Notes">
            </td>
          </tr>
      <?php endforeach; ?>
        </tbody>
      </table>
      <button type="submit" name="save_attendance">Save Attendance</button>
    </form>
    <?php elseif ($course && !$courseStudents): ?>
        <p>No enrolled students yet.</p>
    <?php endif; ?>

</body>
</html>