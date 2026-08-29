<?php
if(session_status()=== PHP_SESSION_NONE){
  session_start();
}

require_once __DIR__ ."/../../config/db.php";
require_once __DIR__ ."/../../repositories/attendance_recordes.php";
require_once __DIR__ ."/../../repositories/student_repository.php";

$pdo=new Database;
$conn=$pdo->connect();
$attendanceModel= new AttendanceRecord($conn);
$studentRepo= new StudentRepository($conn);

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}

$message="";

$courseId= isset($_GET['course_id'])? (int) $_GET['course_id']: 0 ;
$studentId =isset($_GET['student_id']) ? (int) $_GET['student_id'] : 0 ;
if($studentId <= 0){
  echo "Please select a student from Students Attendance Page.";
  echo "<a href='layout.php?page=students_attendance_report&course_id=$courseId'>Back to Students Attendance </a>";
  exit;
}

$history=$attendanceModel->getStudentHistory($studentId,$courseId);
$student=$studentRepo->findStudentById($studentId);
$courseState=$attendanceModel->getCourseState($courseId);

$studentState=[
  'first_name' => '',
  'last_name' => '',
  'total_sessions' =>0,
  'present_count' => 0
];

foreach($courseState as $state){
  if($state['student_id']==$studentId){
    $studentState=$state;
  }
}
// print_r($courseState);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  
<h3>Attendance History : <span><?= htmlspecialchars($studentState['first_name']) . " ".htmlspecialchars($studentState['last_name'])  ?></span> </h3>
<?php
    $totalSessions=(int)$studentState['total_sessions'];
    $present=(int)$studentState['present_count'];
    $pct= $totalSessions>0 ? round(($present / $totalSessions) * 100) : 0 ;
    if($pct >= 75){
      $color="#28a745";
    }elseif($pct >=50){
      $color="#ffc107";
    }else{
      $color="#dc3545";
    }
?>
<div class="progress">
    <div class="progress-bar" style="width:<?= $pct ?>% ; background-color:<?= $color ?>"><?= $pct ?>%</div>
  </div>
  <div class="table-container">
  <table border=1>
    <thead>
      <tr>
        <th>Course Name</th>
        <th>Session Date</th>
        <th>Status</th>
        <th>Notes</th>
      </tr>
    </thead>
    <tbody>
      <?php if(empty($history)):?>
        <tr>
          <td colspan='4'>No results found.</td>
        </tr>
      <?php else: ?>
      <?php foreach($history as $h):?>
        <tr>
          <td><?= htmlspecialchars($h['course_name']) ?></td>
          <td><?= htmlspecialchars($h['session_date']) ?></td>
          <td><?= htmlspecialchars($h['status']) ?></td>
          <td><?= $h['notes'] ? htmlspecialchars($h['notes']):"____" ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
  </div>
  <a href="layout.php?page=students_attendance_report&course_id=<?= $courseId ?>">Back to Students Attendance</a>
</body>
</html>