<?php
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__. "/../../config/db.php";

$pdo=new Database;
$conn=$pdo->connect();

$courseRepo= new CourseRepository($conn);

if ($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['create'])){

  $code=trim($_POST['code']);
  $name=trim($_POST['name']);

  $start_date=$_POST['start_date'];
  $end_date=$_POST['end_date'];
  $postpone_date=!empty($_POST['postpone_date'])?$_POST['postpone_date']:null;

  $total_hours=$_POST['total_hours'];
  $start_time=$_POST['start_time'];
  $end_time=$_POST['end_time'];
  $days=$_POST['days']??[];

  $teachersId=$_POST["course_teachers"]??[];

  $max_students=$_POST['max_students'];
  $price=$_POST['price'];
  
  if(empty($code)||empty($name)||empty($start_date)||empty($end_date)||empty($total_hours)||empty($start_time)||empty($end_time)||empty($days)||empty($teachersId)||empty($max_students)||empty($price)){
    $error="Please fill all the fields";
  }else{
    $daysStr=implode(',',$days);
    $course_id=$courseRepo->createCourse($code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$daysStr,$max_students,$price);
    foreach($teachersId as $teacherId){
      $courseRepo->addTeacherToCourse($course_id,$teacherId);
    }
    header("Location:../pages/layout.php?page=courses");
    exit;

  }
    
  }
}
if(isset($_POST['edit'])){
  $course_id=$_POST['course_id'];
  $code=trim($_POST['code']);
  $name=trim($_POST['name']);

  $start_date=$_POST['start_date'];
  $end_date=$_POST['end_date'];
  $postpone_date=!empty($_POST['postpone_date'])?$_POST['postpone_date']:null;

  $total_hours=$_POST['total_hours'];
  $start_time=$_POST['start_time'];
  $end_time=$_POST['end_time'];
  $days=$_POST['days']??[];

  $teachersId=$_POST["course_teachers"]??[];

  $max_students=$_POST['max_students'];
  $price=$_POST['price'];
  
  $daysStr=implode(',',$days);
  $courseRepo->editCourse($code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$daysStr,$max_students,$price,$course_id);
  $courseRepo->updateCourseTeachers($course_id,$teachersId);
  header("Location:../pages/layout.php?page=courses");
    exit;
}


if(isset($_POST['delete'])){
  $course_id=$_POST['course_id'];

  $courseRepo->deleteCourse($course_id);
  header("Location:../pages/layout.php?page=courses");
  exit;

}







?>