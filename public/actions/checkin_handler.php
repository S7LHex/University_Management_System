<?php

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/teacher_attendance.php"; 
require_once __DIR__."/../../repositories/notification.php"; 
require_once __DIR__."/../../repositories/teacher_repository.php"; 
require_once __DIR__."/../../repositories/course_repository.php"; 

$pdo= new Database;
$conn=$pdo->connect();

$attendanceModel= new TeacherAttendance($conn);
$notificationModel= new Notification($conn);
$teacherRepo= new TeacherRepository($conn);
$courseRepo= new CourseRepository($conn);

$checkinMessage='';


if($_SERVER['REQUEST_METHOD']==='POST'){
  if(isset($_POST['checkin'])){
    $courseId=$_POST['course_id'];
    $teacherId=$_POST['teacher_id'];
    $userId=$_POST['user_id'];

    $newId=$attendanceModel->checkIn($teacherId,$courseId,$userId);
    echo $newId;

    // Notification
    if($newId>0){
    $teacherInfo=$teacherRepo->findTeacherById($teacherId);
    $courseInfo=$courseRepo->getCourseBYId($courseId);

    $message="Teacher [ ". $teacherInfo['first_name'].' '.$teacherInfo['last_name']. " ] checked in for the ".$courseInfo['name'].
    " class at ". date("H:i");

    $notificationModel->notifyAllAdmins($message,'teacher_checkin',$newId);
    $checkinMessage="Your attendance has been successfully recorded.";
    }else{
      $checkinMessage="You have already checked in today.";
    }


  }
}














?>