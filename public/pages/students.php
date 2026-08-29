<?php
require_once  __DIR__."/../../config/db.php";
require_once  __DIR__."/../../repositories/student_repository.php";
require_once  __DIR__."/../../repositories/paginator.php";
// require_once  __DIR__."/../actions/student_handler.php";

$pdo=new Database;
$conn=$pdo->connect();
$studentRepo= new StudentRepository($conn);

$page= isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage=5;
$totalItems=$studentRepo->countStudents();
$paginator= new Paginator($page ,$perPage,$totalItems);
?>
<h2>Students</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Students</span>
</nav>
<div class="card">
  <div class="table-header">
    <form action="" method="GET" class='search-form'>
      <input type="hidden" name="page" value="students">    
        <div class="search-box">
          <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
          <input type="text" name="search" placeholder="Search Student">
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
          <a href="export_pdf/export_students_pdf.php"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
          <a href="export_excel/export_students_xlsx.php"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
        </div>
      </div>
      <a href="layout.php?page=add_student" class="btn  add-btn">+ Add New Student</a>
    </div>
</div>

  <?php if(isset($_GET['search']) && !empty($_GET['search'])):?>
    <?php $students=$studentRepo->searchStudents($_GET['search'])?>
  <?php else:?>
    <?php $students=$studentRepo->getStudents($paginator->perPage, $paginator->offset)?> 
  <?php endif;?>
<div class="table-container">
  <table border=2>
    <tr>
      <th>ID</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Father's Name</th>
      <th>Age</th>
      <th>Address</th>
      <th>Mobile Number</th>
      <th>Major</th>
      <th>Email</th>
      <th >Actions</th>
    </tr>
    <?php if(empty($students)):?>
      <tr>
        <td colspan="5" class="msg-td">No Students Found</td>
      </tr>
    <?php endif;?>
    
    <?php foreach ($students as $student) :?>
      <tr>
        <td><?= htmlspecialchars($student['id']) ?></td>
        <td><?= htmlspecialchars($student['first_name']) ?></td>
        <td><?= htmlspecialchars($student['last_name']) ?></td>
        <td><?= htmlspecialchars($student['father_name']) ?></td>
        <td><?= htmlspecialchars($student['age']) ?></td>
        <td><?= htmlspecialchars($student['address']) ?></td>
        <td><?= htmlspecialchars($student['mobile_number'])?></td>
        <td><?= htmlspecialchars($student['major'])?></td>
        <td><?= htmlspecialchars($student['email']) ?></td>

        <td class="action-td">
          <a href="layout.php?page=edit_student&id=<?= $student['id'] ?>" class="btn edit-btn"><img src="../assets/imgs/edit-interface-icon-svgrepo-com.svg" class="btn-icon">Edit</a>
          <form action="../actions/student_handler.php" method="POST">
            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
            <button name="delete-student" class="btn delete-btn" onclick="return confirm('Delete this student?')"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon">Delete</button>
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
        <a href="layout.php?page=students&p=1" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=students&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last-page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=students&p=<?= $paginator->totalPages ?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>
