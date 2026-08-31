<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/teacher_attendance.php";


$pdo= new Database;
$conn=$pdo->connect();
$attendaceModel= new TeacherAttendance($conn);

$teacherId=$_SESSION['related_id'];
$userId=$_SESSION['user_id'];


$todayCourses=$attendaceModel->getTodayCourses($teacherId);

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

  <h2>Attendance Check-in</h2>

  <nav class="breadcrumb">
    <a href="teacher_dashboard.php"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
    <span>/</span>
    <span class="active">Attendance Chech-in</span>
  </nav>

<?php if(isset($checkinMessage)):?>
  <div>
    <p><?= htmlspecialchars($checkinMessage) ?></p>
  </div>
<?php endif;?>

<div class="table-container">

  <?php if(isset($_SESSION['success'])):?>
    <div class="success-msg"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']);
  endif; ?>

  <?php if(isset($_SESSION['error'])):?>
    <div class="error-msg"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']);
  endif; ?>
<h2 class="section-title">Today Courses</h2>
<table border="1">
  <thead>
    <tr>
      <th>Course</th>
      <th>Start Time</th>
      <th>Check-in</th>
    </tr>
  </thead>
  <tbody>
    <?php if(!$todayCourses): ?>
      <tr>
        <td colspan="3">You don't have any lectures today.</td>
      </tr>
      <?php else:?>
        <?php foreach($todayCourses as $course):?>
          <tr>
            <td><?= htmlspecialchars($course['name']) ?></td>
            <td><?= htmlspecialchars($course['start_time']) ?></td>
            <td>
              <?php if($course['checked_in_today']):?>
                <p>Your have checked-in your attendance.</p>
              <?php else:?>
              <form action="../actions/checkin_handler.php" method="POST">
                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                <input type="hidden" name="teacher_id" value="<?= $teacherId ?>">
                <input type="hidden" name="user_id" value="<?= $userId ?>">
                <button name='checkin' class="btn-save"> <img src="../assets/imgs/check-circle-svgrepo-com.svg" alt="" class="btn-img"> Check In</button>

              </form>
              <?php endif;?>
            </td>
          </tr>
        <?php endforeach;?>
      <?php endif;?>
    </tbody>
  </table>
    <a href="teacher_dashboard.php" class="back-btn"><img src="../assets/imgs/arrow-left-svgrepo-com.svg" alt="">Back to Dashboard</a>  
<div>
  
</div>
</body>
</html>

