<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/notification.php";
require_once __DIR__."/../../repositories/teacher_attendance.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/paginator.php";
require_once __DIR__."/../actions/notifications_handler.php";

$pdo=new Database;
$conn=$pdo->connect();

$notificationModel= new Notification($conn);
$attendanceModel= new TeacherAttendance($conn);
$teacherRepo= new TeacherRepository($conn);
$courseRepo=new CourseRepository($conn);

if(!isset($_SESSION['user_id'])||$_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}

$unReadNotifications=$notificationModel->getUnreadForUser($_SESSION['user_id']);
foreach($unReadNotifications as $n){
  if($n['type']==='teacher_checkin'){
    $notificationModel->markAsRead($n['id']);
  }
}


$filters=[
  "teacher_id"=>$_GET['teacher_id'] ?? null,
  "course_id"=>$_GET['course_id'] ?? null,
  "date_from"=>$_GET['date_from'] ?? null,
  "date_to"=>$_GET['date_to'] ?? null
];

$page=isset($_GET['p']) ? (int)$_GET['p'] :1;
$perPage=5;
$totalItems=(int)$attendanceModel->attendanceCount(array_filter($filters));
$paginator= new Paginator($page,$perPage,$totalItems);

$records=$attendanceModel->getAll($paginator->perPage,$paginator->offset,array_filter($filters));

$teachers=$teacherRepo->getAllTeachers();
$courses=$courseRepo->getAllCourses();

?>
<h2>Teacher Attendance</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Teachers Attendance</span>
</nav>

<div class="card">
    <div class="table-header">
      <form action="layout.php?" method='GET' class="filters">
        <input type="hidden" name="page" value="teachers_attendance_report">
        <input type="hidden" name="p" value=<?= $page ?>>
        <div class="filter-group">
          <label for="">Teacher</label>
          <select name="teacher_id" id="">
            <option value="">Select Teacher</option>
            <?php foreach($teachers as $teacher): ?>
              <option value="<?= $teacher['id'] ?>" <?= ($_GET['teacher_id'] ??'')==$teacher['id']?'selected':'' ?>>
                <?= htmlspecialchars($teacher['first_name']) ?>  <?= htmlspecialchars($teacher['last_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label for="">Course</label>
          <select name="course_id" id="">
            <option value="">Select Courses</option>
            <?php foreach($courses as $course): ?>
              <option value="<?= $course['id'] ?>" <?= ($_GET['course_id'] ?? '')==$course['id']?'selected':'' ?>>
                <?= htmlspecialchars($course['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="filter-group">
          <label for="">Date From</label>
          <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from']?? '') ?>" placeholder="Date From">
        </div>

        <div class="filter-group">
          <label for="">To Date</label>
          <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ??'') ?>" placeholder="Date to">
        </div>
        
        <button type="submit" class="btn filter-btn"> <img src="../assets/imgs/filter-svgrepo-com.svg" alt="" class="filter-icon">Filter</button> 
      </form>

      <div class="dropdown">
          <button class='btn export-btn'>
            <img src="../assets/imgs/download-2-svgrepo-com (1).svg" alt="" class="export-icon">
            Export
            <img src="../assets/imgs/chevron-down-svgrepo-com.svg" alt="" class="export-icon">
          </button>
          <div class="dropdown-content">
            <a href="export_pdf/export_teachers_attendance_pdf.php?<?= http_build_query(array_filter($filters)) ?>"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
            <a href="export_excel/teachers_attendance_xlsx.php?<?= http_build_query(array_filter($filters)) ?>"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
          </div>
        </div>
    </div>
    <div class="table-container">
    <table border=1>
      <thead>
        <tr>
          <th>Teacher</th>
          <th>Course</th>
          <th>Date</th>
          <th>Attendance Time</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($records)): ?>
          <tr><td colspan='5'>No results found.</td></tr>
        <?php else: ?>
          <?php foreach($records as $record):?>
            <tr>
              <td><?= htmlspecialchars($record['first_name'])." ". htmlspecialchars($record['last_name']) ?></td>
              <td><?= htmlspecialchars($record['course_name']) ?></td>
              <td><?= htmlspecialchars($record['session_date']) ?></td>
              <td><?= htmlspecialchars($record['check_in_time']) ?></td>
              <td><?= htmlspecialchars($record['status']) ?></td>     
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
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
        <a href="layout.php?page=teachers_attendance_report&p=<?= $paginator->currentPage - 1 ?>" class='move-link'>&lt;</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=teachers_attendance_report&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- next btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=teachers_attendance_report&p=<?= $paginator->currentPage + 1 ?>" class='move-link'>&gt;</a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>



