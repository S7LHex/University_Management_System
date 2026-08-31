<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','student']);

require_once __DIR__."/../../repositories/student_repository.php";
require_once __DIR__."/../../config/db.php";

$pdo = new Database;
$conn=$pdo->connect();
$studentRepo= new StudentRepository($conn);


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
    <img src="../assets/imgs/student-fill-svgrepo-com (1).svg" alt="">

      <div class="info-card-body">
        <h2><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></h2>
        <p><img src="../assets/imgs/book-open-svgrepo-com (2).svg" alt=""><?= htmlspecialchars($student['major']) ?></p> 
      </div>

      <div class="info-card-body">
        <p><img src="../assets/imgs/gui-email-read-svgrepo-com.svg" alt=""><?= htmlspecialchars($student['email']) ?></p>
        <p><img src="../assets/imgs/phone-svgrepo-com.svg" alt=""><?= htmlspecialchars($student['mobile_number']) ?></p>
        <p><img src="../assets/imgs/address-svgrepo-com (1).svg" alt=""><?= htmlspecialchars($student['address']) ?></p>
      </div>

    </div>
  <div class="table-container">
  <h2 class="section-title">My Courses</h2>
  <?php if(empty($studentCourses)):?>
    <div class="empty-state">
      <p>No courses enrolled yet.</p>
    </div>
  <?php else:?>
  <table border="1">
    <tr>
      <th>Course</th>
      <th>Code</th>
      <th>Teachers</th>
      <th>Grade</th>
      <th>Enrollment Date</th>
    </tr>
    <?php foreach($studentCourses as $course):
        $class=$course['grade']>=60 ? 'passed' : 'failed';
      ?>

        <tr>
          <td><?= htmlspecialchars($course['course_name']) ?></td>
          <td><?= htmlspecialchars($course['code']) ?></td>
          <td><?= htmlspecialchars($course['teachers']) ?></td>
          <td><span class="grade <?= $class ?>"><?= $course['grade']? htmlspecialchars($course['grade']):'_' ?></span></td>
          <td><?= htmlspecialchars($course['enrollment_date']) ?></td>
        </tr>
        <?php endforeach;?>
  </table>
  </div>
  <?php endif;?>
</body>
</html>

