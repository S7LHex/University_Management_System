<?php

if(session_status()=== PHP_SESSION_NONE){
  session_start();
}

require_once __DIR__ ."/../../config/db.php";
require_once __DIR__ ."/../../repositories/attendance_recordes.php";
require_once __DIR__ ."/../../repositories/paginator.php";
require_once __DIR__ ."/../../repositories/course_repository.php";

$pdo=new Database;
$conn=$pdo->connect();
$attendanceModel= new AttendanceRecord($conn);
$courseRepo= new  CourseRepository($conn);

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}

$courses=$courseRepo->getAllCourses();

$course_id= isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;

$page= isset($_GET['p'])?(int)$_GET['p']:1;
$perPage=5;
$totalItems=$attendanceModel->countCourseStudents($course_id);
$paginator= new Paginator($page,$perPage,$totalItems);

$courseStates= $course_id ? $attendanceModel->getCourseState($course_id,$paginator->perPage,$paginator->offset) : [];

?>
<h2>Students Attendance</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Students Attendance</span>
</nav>
<div class="card">
  <div class="attendance-table-actions">

    <form action="layout.php" method="GET" style="width: 30%;">
      <input type="hidden" name="page" value="students_attendance_report">
      <div class="select-course-group">
        <label for="">Select Course</label>
        <select name="course_id" class="students-select" onchange="this.form.submit()">
        <option value="">Select course</option>
        <?php foreach($courses as $course): ?>
          <option value="<?= $course['id'] ?>" <?= $course_id == (int) $course['id']?'selected':'' ?>>
            <?= htmlspecialchars($course['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      </div>
      
    </form>
    <div class="dropdown">
      <button class='btn export-btn'>
        <img src="../assets/imgs/download-2-svgrepo-com (1).svg" alt="" class="export-icon">
        Export
        <img src="../assets/imgs/chevron-down-svgrepo-com.svg" alt="" class="export-icon">
      </button>
      <div class="dropdown-content">
        <a href="export_pdf/export_attendance_report_pdf.php?course_id=<?= $course_id ?>"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
        <a href="export_excel/export_attendance_xlsx.php?course_id=<?= $course_id ?>"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
      </div>
      </div>
    </div>

  <div class="table-container">
    <table border=1>
      <thead>
        <tr>
          <th>Student</th>
          <th>Details</th>
          <th>Sessions Count</th>
          <th>Present</th>
          <th>absent</th>
          <th>Late</th>
          <th>Excused</th>
          <th>Attendance Rate</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($courseStates)): ?>
          <tr>
            <td colspan="7"><?= $course_id ? "No attendance data available for this course yet." : "Select course to view the report."  ?></td>
          </tr>
        <?php else: ?>
        <?php foreach($courseStates as $state):
          $totalSessions= (int) $state['total_sessions'];
          $present= (int) $state['present_count'];
          $pct= $totalSessions>0 ? round(($present/$totalSessions) * 100) : 0;
          
          if ($pct >= 75){
            $color="#28a745";
          }elseif($pct >=50){
            $color="#ffc107";
          }else{
            $color="#dc3545";
          }
          ?>
          <tr>
            <td><?= htmlspecialchars($state['first_name'])." ".htmlspecialchars($state['last_name'])  ?></td>
            <td><a href="layout.php?page=student_attendance_history&course_id=<?= $course_id ?>&student_id=<?= $state['student_id'] ?>">History</a></td>
            <td><?= $totalSessions ?></td>
            <td><?= $present ?></td>
            <td><?= htmlspecialchars((int)$state['absent_count']) ?></td>
            <td><?= htmlspecialchars((int)$state['late_count']) ?></td>
            <td><?= htmlspecialchars((int)$state['excused_count']) ?></td>
            <td>
              <div class="progress">
                <div id="bar" class="progress-bar" style="width:<?= $pct ?>% ; background-color:<?= $color ?>"> <?= $pct ?>%</div>
              </div>
              </td>
          </tr>
          <?php endforeach; ?>
        <?php endif;?>
      </tbody>
    </table>
  </div>
  <!-- PAGINATION -->
<?php if($paginator->totalPages > 1):?>
  <div class="pagination">

    <p>Showing <?= $paginator->currentPage ?> of <?= $paginator->totalPages  ?> pages.</p>

    <div class="pagination-links">
      <?php 
        $window=1;
        $startPage= max(1,$paginator->currentPage - $window);
        $endPage= min ($paginator->totalPages , $paginator->currentPage+$window);  
      ?>

      <!-- previous btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="layout.php?page=students_attendance_report&course_id=<?= $course_id ?>&p=<?= $paginator->currentPage - 1 ?>" class='move-link'>&lt;</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=students_attendance_report&course_id=<?= $course_id ?>&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- next btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=students_attendance_report&course_id=<?= $course_id ?>&p=<?= $paginator->currentPage + 1 ?>" class='move-link'>&gt;</a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>
