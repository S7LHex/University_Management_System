<?php
session_start();

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/attendance_sessions.php";
require_once __DIR__."/../../repositories/attendance_recordes.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/teacher_repository.php";

if(!$_SESSION['user_id']||$_SESSION['role']!=='teacher'){
  header('Location:login.php');
  exit;
}
$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo= new TeacherRepository($conn);
$sessionModel= new AttendanceSession($conn);

$teacherId=$_SESSION['related_id'];

if ($teacherId!=$_SESSION['related_id']){
  die('Access denied');
}

$teacherCourses=$teacherRepo->getTeacherCourses($teacherId);
if(!$teacherCourses){
  die('No courses found.');
}

$courseId=isset($_GET['course_id'])?$_GET['course_id']:0;
$sessions=$courseId?$sessionModel->getSessionsByCourse($courseId):[];
// if(!$sessions){
//   die('No sessions found.');
// }
print_r($sessions);

?>
<form action="" method="GEt">
  <select name="course_id" id="" onchange="this.form.submit()">
    <option value="">Select Course</option>
    <?php foreach($teacherCourses as $course):?>
      <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
      <?php endforeach; ?>
  </select>

  <?php if($sessions):?>
    <table border=1>
      <thead>
        <tr>
          <th>Session Date</th>
          <th>Attendance</th>
          <th>Edit</th>
        </tr>
        <tbody>
            <?php foreach($sessions as $session):?>
            <tr>
              <td><?= htmlspecialchars($session['session_date']) ?></td>
              <td><?= htmlspecialchars($session['present_count']) ?> / <?= htmlspecialchars($session['total_count']) ?></td>
              <td><a href="teacher_attendance.php?course_id=<?= $courseId ?>&teacher_id=<?= $teacherId ?>">Edit</a></td>
            </tr>
            <?php endforeach;?>
        </tbody>
      </thead>
    </table>
    <?php endif; ?>
</form>