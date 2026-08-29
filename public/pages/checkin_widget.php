<?php
session_start();
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/teacher_attendance.php";
require_once __DIR__."/../actions/checkin_handler.php";


$pdo= new Database;
$conn=$pdo->connect();
$attendaceModel= new TeacherAttendance($conn);

$teacherId=$_SESSION['related_id'];
$userId=$_SESSION['user_id'];


$todayCourses=$attendaceModel->getTodayCourses($teacherId);
// print_r($todayCourses);

?>
<h3>Attendance Check-in</h3>
<?php if(isset($checkinMessage)):?>
  <div>
    <p><?= htmlspecialchars($checkinMessage) ?></p>
  </div>
<?php endif;?>
<?php if(!$todayCourses): ?>
  <div>
    <p>You don't have any lectures today.</p>
  </div>
<?php else:?>
<?php foreach($todayCourses as $course):?>
  <div>
    <span><?= htmlspecialchars($course['name']) ?> (<?= htmlspecialchars($course['start_time']) ?>)</span>
    <?php if($course['checked_in_today']):?>
      <p>Your attendance has been recorded successfully.</p>
    <?php else:?>
      <form action="" method="POST">
        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
        <input type="hidden" name="teacher_id" value="<?= $teacherId ?>">
        <input type="hidden" name="user_id" value="<?= $userId ?>">
        <button name='checkin'>Check In</button>
      </form>
    <?php endif;?>
  </div>
<?php endforeach;?>
<?php endif;?>

