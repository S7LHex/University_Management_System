<?php
require_once __DIR__. "/../../config/db.php";
require_once __DIR__. "/../../repositories/course_repository.php";
require_once __DIR__. "/../../repositories/paginator.php";

$pdo= new Database;
$conn=$pdo->connect();

$courseRepo= new CourseRepository($conn);

$page=isset($_GET['p'])?(int)$_GET['p'] :1;
$perPage=5;
$totalItems=$courseRepo->coursesCount();
$paginator=new Paginator($page,$perPage,$totalItems);

if(isset($_GET['search']) && !empty($_GET["search"])){
  $courses=$courseRepo->searchCourse(trim($_GET['search']));
}else{
  $courses=$courseRepo->getCourses($paginator->perPage,$paginator->offset);
}

?>
<h2>Courses</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Courses</span>
</nav>

<div class="card">
  <div class="table-header">

    <form action="" method="GET" class='search-form'>
      <input type="hidden" name="page" value="courses">
      <div class="search-box">
        <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
        <input type="text" name="search" placeholder="Search by course name..">
      </div>
      <button class="btn search-btn">Search</button>
    </form>

    <div class='table-options'>
      <div class="dropdown">
        <button class='btn export-btn'>
          <img src="../assets/imgs/download-2-svgrepo-com (1).svg" alt="" class="export-icon">
          Export
          <img src="../assets/imgs/chevron-down-svgrepo-com.svg" alt="" class="export-icon">
        </button>
        <div class="dropdown-content">
          <a href="export_pdf/export_courses_pdf.php"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
          <a href="export_excel/export_courses.php"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
        </div>
      </div>
      <a href="layout.php?page=create_course" class="btn add-btn">+ Create New Course</a>
    </div>
  </div>
<div class="table-container">
  <table border=1>
    <tr> 
        <th>Code</th>
        <th>Name</th>
        <th>Teachers</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Postpone Date</th>
        <th>Total Hours</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Days</th>
        <th>Max Students</th>
        <th>Price</th>
        <th>Actions</th> 
    </tr>
    <?php if(empty($courses)):?>
    <tr>
      <td colspan='13' class="msg-td">No Courses Found</td>
    </tr>
    <?php endif;?>
    
    <?php foreach($courses as $course):?>
      <tr>
        <td><?= htmlspecialchars($course['code']) ?></td>
        <td><a href="layout.php?page=course_details&course_id=<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></a></td>
        <td>
          <?php $teachers=$courseRepo->getCourseTeachers($course['id']);?>
          <?php foreach($teachers as $teacher):?>
            <span><?= htmlspecialchars($teacher['first_name'])." ".htmlspecialchars($teacher['last_name']) ?></span>
            <?php endforeach;?>
        </td>

        <td><?= htmlspecialchars($course['start_date']) ?></td>
        <td><?= htmlspecialchars($course['end_date']) ?></td>
        <td><?= htmlspecialchars($course['postpone_date']) ?></td>
        <td><?= htmlspecialchars($course['total_hours']) ?></td>
        <td><?= htmlspecialchars($course['start_time']) ?></td>
        <td><?= htmlspecialchars($course['end_time']) ?></td>
        <td><?= htmlspecialchars($course['days']) ?></td>

        <td><?= htmlspecialchars($course['max_students']) ?></td>
        <td><?= htmlspecialchars($course['price']) ?></td>
    
        <td class="action-td">
          <a href="layout.php?page=edit_course&course_id=<?= $course['id'] ?>" class="btn edit-btn"><img src="../assets/imgs/edit-interface-icon-svgrepo-com.svg" class="btn-icon">Edit</a>
          <form action="../actions/course_handler.php" method="POST">
            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
            <button name="delete" class="btn delete-btn" onclick="return confirm('Delete Course?')"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon">Delete</button>
          </form>
        </td>
      </tr>

  <?php endforeach;?>
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

      <!-- first-page btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="layout.php?page=courses&p=1" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=courses&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last-page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=courses&p=<?= $paginator->totalPages ?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>

