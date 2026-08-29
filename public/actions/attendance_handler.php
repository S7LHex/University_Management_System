<?php
require_once __DIR__."/../../config/db.php";

require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/attendance_sessions.php";
require_once __DIR__."/../../repositories/attendance_recordes.php";

$pdo= new Database;
$conn=$pdo->connect();

$courseRepo= new CourseRepository($conn);
$sessionModel= new AttendanceSession($conn);
$recordModel= new AttendanceRecord($conn);


if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['save_attendance'])){
  
  $course_id=$_POST['course_id'];
  $teacherId=$_POST['teacher_id'];
  $selectedDate = $_GET['session_date'] ?? $_POST['session_date'] ?? date('Y-m-d');
  echo $selectedDate;
  $course=$courseRepo->getCourseBYId($course_id);
  $errors=[];
  $successMessage='';


  $dayMap=[0=>'sun',1=>'mon',2=>'tue',3=>'wed',4=>'thu',5=>'fri',6=>'sat'];
  $dayName=$dayMap[(int) date('w', strtotime($selectedDate))];
  $allowedDays=array_map('trim', explode(',', $course['days']));

  if (!in_array($dayName, $allowedDays, true)) {
    $errors[] = "The selected day(" . $dayName . ") is not one of the scheduled course days (" . $course['days'] . ")";
    }
    // تحقق: التاريخ ضمن مدة الكورس
  elseif($selectedDate < $course['start_date'] || $selectedDate > ($course['postpone_date'] ?: $course['end_date'])) {
    $errors[] = "The selected date is outside the course period.";
  } 
  else{
    $sessionId=$sessionModel->createOrGetSession($course_id, $teacherId, $selectedDate);

    $statuses=[];
    foreach($_POST['status'] ?? [] as $student_id=>$status){
      $statuses[$student_id]=[
        'status'=>$status,
        'notes'=>$_POST['notes'][$student_id]??null
      ];
    }
    $recordModel->saveBulk($sessionId,$statuses);
    $successMessage='Attendance saved successfully.';

  }
}


}
?>