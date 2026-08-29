<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../repositories/user_repositories.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn=$pdo->connect();

$userRepo=new User($conn);
$teacherRepo=new TeacherRepository($conn);


$user=$userRepo->getUsersById($_SESSION['user_id']);
if(!$user){
  header("Location:login.php");
  exit;
}

$teacherId=$_SESSION['related_id'];
$teacher=$teacherRepo->findTeacherById($teacherId);
$teacherCourses=$teacherRepo->getTeacherCourses($teacherId);
// print_r($teacher);
// print_r($_SESSION);
// print_r($_SESSION);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher Dashbord</title>
<link rel="stylesheet" href="../assets/css/teacher_dashboard.css">
</head>
<body>
  <div class="portal-header">
    <div class="header-title">
      <img src="../assets/imgs/student-cap-svgrepo-com (1).svg" alt="">
      <h1>Teacher Portal</h1>
    </div>
    <a href="logout.php"><img src="../assets/imgs/logout-2-svgrepo-com (4).svg" alt="">Logout</a>
  </div>

  <div class="portal-container">
    <div class="info-card">
      <img src="../assets/imgs/user-svgrepo-com.svg" alt="">
      <div class="info-card-body">
        <h2>
          Welcome back,<?= htmlspecialchars($teacher['first_name']) ?>  <?=htmlspecialchars($teacher['last_name'])  ?>
          <img src="../assets/imgs/waving-hand-svgrepo-com.svg" alt="">
        </h2>
        <p><?= htmlspecialchars($teacher['major']) ?></p> 
        <div class="info-card-body-buttons">
          <a class="checkin-btn" href="checkin_widget.php"><img src="../assets/imgs/clipboard-checked-svgrepo-com.svg" alt="">Check In</a> 
          <a class="attendance-history-btn" href="attendance_history.php?teacher_id=<?= $teacherId ?>" class="btn"><img src="../assets/imgs/clipboard-list-svgrepo-com.svg" alt="">Attendance History</a>
        </div>
      </div>
    </div>
    <div class="table-container">
    <div class="section-title"><img src="../assets/imgs/book-open-svgrepo-com (1).svg" alt=""> <h2>MY Courses</h2></div>
      <table>
        <tr>
          <th>Course Name</th>
          <th>Students Count</th>
          <th>Attendance</th>
          <th>Show students</th>
        </tr>
        <tbody>
          <?php foreach($teacherCourses as $course):?>
            <tr>
              <td><?= htmlspecialchars($course['course_name']) ?></td>
              <td><span class="count-badge"><?= htmlspecialchars($course['students_count']) ?></span></td>
              <td><a class="attendance-btn" href="teacher_attendance.php?course_id=<?= $course['id'] ?>&teacher_id=<?= $teacherId ?>" class='btn'><img src="../assets/imgs/checklist-minimalistic-svgrepo-com.svg" alt="">Attendance</a></td>
              <td><a class="show-students-btn" href="course_students.php?course_id=<?= $course['id'] ?>&teacher_id=<?=$teacherId  ?>"><img src="../assets/imgs/users-svgrepo-com.svg" alt="">Show Students</a></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    </div>
  </div>
  
</body>
<footer>
    &copy; 2026 S7L University System.All rights reserved.  
  </footer>
</html>





