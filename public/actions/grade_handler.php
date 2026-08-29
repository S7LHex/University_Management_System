<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);


require_once __DIR__."/../../repositories/grade_repository.php";
require_once __DIR__."/../../config/db.php";
$pdo = new Database;
$conn=$pdo->connect();

$gradeRepo= new GradeRepository($conn);

if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['save'])){

    $student_id=$_POST['student_id'];
    $course_id=$_POST['course_id'];
    $teacher_id=$_POST['teacher_id'];
    $grade=$_POST['grade'];

    if(!empty($grade) && is_numeric($grade) && $grade>=0 && $grade<=100){
      $gradeRepo->saveGrade($student_id,$course_id,$grade);
      header('Location:../pages/course_students.php?course_id='.$course_id.'&teacher_id='.$teacher_id);
      exit;
    }else{
      $_SESSION['error']="Grade must be a number between 0 and 100";
      header('Location:../pages/add_grade.php?student_id='.$student_id.'&course_id='.$course_id.'&teacher_id='.$teacher_id);
      exit;
    }
  }

  if(isset($_POST['delete_grade'])){
    $student_id=$_POST['student_id'];
    $course_id=$_POST['course_id'];
    $gradeRepo->deleteGrade($student_id,$course_id);
    header('Location:../pages/course_students.php?course_id='.$course_id);
    exit;
  }
}









?>