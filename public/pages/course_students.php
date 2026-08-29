<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin','teacher']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/grade_repository.php";
// require_once __DIR__."/../actions/grade_handler.php";

$pdo= new Database;
$conn= $pdo->connect();

$courseRepo=new CourseRepository($conn);
$teacherRepo= new TeacherRepository($conn);
$gradeRepo= new GradeRepository($conn);

if(!isset($_GET['course_id'])||!isset($_GET['teacher_id'])){
  die('Course not specified');
}

$course_id=$_GET['course_id'];
$teacher_id=$_GET['teacher_id'];

$course=$courseRepo->getCourseBYId($course_id);

// $teacherCourse=$teacherRepo->getTeacherCourse($teacher_id,$course_id);
$students=$teacherRepo->getCourseStudentsForTeacher($course_id,$teacher_id);
// print_r($course);
// print_r($teacherCourse);

// if(!$course || !$teacherCourse){
//   die('Course not found');
// }
if($teacher_id!==$_SESSION['related_id']){
    header('Location:login.php');
    exit;
}


?>

<link rel="stylesheet" href="../assets/css/teacher_dashboard.css">

<div class="portal-container">
<h2>Course Students</h2>
<nav class="breadcrumb">
  <a href="teacher_dashboard.php"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Course Students</span>
</nav>

  <div class="info-card">

      <div class="info-card-body">
        <img src="../assets/imgs/copy-course-svgrepo-com.svg" alt="">
        <div>
          <h2><?= htmlspecialchars($course['name'])?></h2>
          <p><?= htmlspecialchars($course['code']) ?></p>
        </div>
      </div>

      <div class="info-card-body">
        <img src="../assets/imgs/users-svgrepo-com (1).svg" alt="">
        <div>
          <h2>Max Students</h2>
          <p><?= htmlspecialchars($course['max_students']) ?></p>
        </div>
      </div>

      <div class="info-card-body">
        <img src="../assets/imgs/history-svgrepo-com.svg" alt="">
        <div>
          <h2>Schedule</h2>
          <p><?= htmlspecialchars($course['days']) ?></p>
          <p><?= htmlspecialchars($course['start_time'] | $course['end_time']) ?></p>
        </div>
      </div>

      <div class="info-card-body">
        <img src="../assets/imgs/date-range-svgrepo-com (2).svg" alt="">
        <div>
          <h2>Duration</h2>
          <p><?= htmlspecialchars($course['start_date']) ?></p>
          <p><?= htmlspecialchars($course['end_date']) ?></p>
        </div>
      </div>


  </div>
<?php if(empty($students)):?>
  <div class="empty-state">
    <p>No students enrolled yet.</p>
  </div>
<?php else:?>

<div class="table-container">
<div class="section-title"><h2>Enrolled Students</h2></div>

<table border="1">
  <tr>
    <th>Name</th>
    <th>Email</th>
    <th>Grade</th>
    <th>Actions</th>

  </tr>
  <tbody>
    <?php foreach($students as $student):?>
      <?php $grade=$gradeRepo->getGrade($student['id'],$course_id); 
      $class= $grade>=60 ? 'passed':'failed';
      ?>
      <tr>
        <td><?= htmlspecialchars($student['first_name']).' '. htmlspecialchars($student['last_name']) ?></td>
        <td><?= htmlspecialchars($student['email']) ?></td>
        <td><span class="grade <?= $class ?>"><?= htmlspecialchars($grade?:'_') ?></span></td>
        <td>
          <a href="add_grade.php?student_id=<?= $student['id'] ?>&course_id=<?= $course['id'] ?>&teacher_id=<?= $teacher_id ?>" class='btn'>
            <?= $grade?'Edit Grade':'Add Grade' ?>
          </a>
          <?php if($grade):?>
          <form action="" method="POST">
            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <button name="delete_grade" class="delete-btn" onclick="return confirm('Delete grade?')">Delete Grade</button>
          </form>
          <?php endif; ?>
        </td>
    </tr>
    <?php endforeach;?>
  </tbody>
</table>
<a href="teacher_dashboard.php" class="back-btn"><img src="../assets/imgs/arrow-left-svgrepo-com.svg" alt="">Back to Dashboard</a>

</div>
<?php endif; ?>

</div>