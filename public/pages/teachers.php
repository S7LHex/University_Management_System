<?php
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/teacher_repository.php";
require_once __DIR__."/../../repositories/paginator.php";

$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo= new TeacherRepository($conn);


$page= isset($_GET['p'])? (int)$_GET['p'] : 1;
$perPage=5;
$totalItems=$teacherRepo->countTeachers();
$paginator = new Paginator($page,$perPage,$totalItems);

if(isset($_GET['search']) && !empty($_GET['search'])){
  $teachers= $teacherRepo->searchTeacher($_GET['search']);
}else{
  $teachers=$teacherRepo->getTeachers($paginator->perPage,$paginator->offset);
}

?>
<h2>Teachers</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Teachers</span>
</nav>
<div class="card">
  <div class="table-header">
    <form action="" method="GET" class='search-form' onchange="submit">
      <input type="hidden" name="page" value="teachers">
      <div class="search-box">
        <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
        <input type="text" name="search" placeholder="Search Teasher">
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
          <a href="export_pdf/export_teachers_pdf.php"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
          <a href="export_excel/export_teachers_xlsx.php"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
        </div>
      </div>
      <a href="layout.php?page=add_teacher" class="btn add-btn">+ Add New Teacher</a>
    </div>
  </div>
<div class="table-container">
<table border="1">
  <tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Father's Name</th>
    <th>Age</th>
    <th>Address</th>
    <th>Mobile Number</th>
    <th>Major</th>
    <th>Salary</th>
    <th>Email</th>
    <th >Actions</th>
  </tr>
  <?php if(empty($teachers)):?>
    <tr>
      <td colspan="5" class="msg-td">No teachers Found</td>
    </tr>
  <?php endif;?>
  
  <?php foreach($teachers as $teacher): ?>
  <tr>
    <td><?= htmlspecialchars($teacher['id']) ?></td>
      <td><?= htmlspecialchars($teacher['first_name']) ?></td>
      <td><?= htmlspecialchars($teacher['last_name']) ?></td>
      <td><?= htmlspecialchars($teacher['father_name']) ?></td>
      <td><?= htmlspecialchars($teacher['age']) ?></td>
      <td><?= htmlspecialchars($teacher['address']) ?></td>
      <td><?= htmlspecialchars($teacher['mobile_number'])?></td>
      <td><?= htmlspecialchars($teacher['major'])?></td>
      <td><?= htmlspecialchars($teacher['salary'])?></td>
      <td><?= htmlspecialchars($teacher['email']) ?></td>
    <td class="action-td">
      <a href="layout.php?page=edit_teacher&teacher_id=<?= $teacher['id'] ?>" class="btn edit-btn"><img src="../assets/imgs/edit-interface-icon-svgrepo-com.svg" class="btn-icon">Edit</a>
      <form action="../actions/teacher_handler.php" method='POST'>
        <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
        <button name="delete" onclick="return confirm('Delete Teacher?')" class="btn delete-btn"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
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

      <!-- first-bage btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="layout.php?page=teachers&p=1" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=teachers&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last-page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=teachers&p=<?= $paginator->totalPages?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>