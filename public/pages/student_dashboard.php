<?php
session_start();
require_once __DIR__."/../../repositories/student_repository.php";

$studentRepo= new StudentRepository;

if(!isset($_SESSION['user_id']) ||$_SESSION['role']!='student'){
  header('Location:login.php');
  exit;
}

$student=$studentRepo->findStudentById($_SESSION['related_id']);
$studentCourses=$studentRepo->getStudentCourses($_SESSION['related_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
<link rel="stylesheet" href="../assets/css/teacher_dashboard.css">

</head>
<body>
  <div class="portal-header">
    <h1>Student Portal</h1>
    <a href="logout.php">Logout</a>
  </div>
  <div class="portal-container">
    <div class="info-card">
      <div class="info-card-body">
        <h2><?= htmlspecialchars($student['name']) ?></h2>
        <p><?= htmlspecialchars($student['email']) ?></p>
        <span><?= htmlspecialchars($student['major']) ?></span>
      </div>
    </div>
  
  <div class="section-title">My Courses</div>
  <?php if(empty($studentCourses)):?>
    <div class="empty-state">
      <p>No courses enrolled yet.</p>
    </div>
  <?php else:?>
  <table border="1">
    <tr>
      <th>Name</th>
      <th>Code</th>
      <th>Teacher</th>
      <th>Grade</th>
      <th>Enrollment Date</th>
    </tr>
    <?php foreach($studentCourses as $course):?>
        <tr>
          <td><?= htmlspecialchars($course['course_name']) ?></td>
          <td><?= htmlspecialchars($course['code']) ?></td>
          <td><?= htmlspecialchars($course['teacher_name']) ?></td>
          <td><?= $course['grade']? htmlspecialchars($course['grade']):'_' ?></td>
          <td><?= htmlspecialchars($course['enrollment_date']) ?></td>
        </tr>
        <?php endforeach;?>
  </table>
  <?php endif;?>
</body>
</html>

