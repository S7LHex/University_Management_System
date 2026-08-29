<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/enrollmentrepository.php";
require_once __DIR__."/../../repositories/paymentRepository.php";
require_once __DIR__."/../../repositories/notification.php";
require_once __DIR__."/../../repositories/student_repository.php";
require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/paginator.php";

$pdo= new Database;
$conn= $pdo->connect();

$paymentRepo= new PaymentRepository($conn);
$notificationRepo= new Notification($conn);
$studentRepo = new StudentRepository($conn);
$courseRepo= new CourseRepository($conn);

$enrollRepio= new Enrollment($conn,$paymentRepo,$notificationRepo);

$page=isset($_GET['p']) ? (int) $_GET['p'] : 1 ;
$perPage= 5;
$totalItems=(int) $enrollRepio->enrollmentsCount();
$paginator= new Paginator($page,$perPage,$totalItems);

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}

$students=$studentRepo->getAllStudents();
$courses=$courseRepo->getAllCourses();

if(isset($_GET['search']) && !empty($_GET['search'])){
  $enrollments=$enrollRepio->searchEnrollment(trim($_GET['search']));
}else{
  $enrollments=$enrollRepio->getEnrollments($paginator->perPage,$paginator->offset); 
}



?>
<h2>Enrollments Management</h2>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Enrollments</span>
</nav>
<!-- ****** ENROLL FORM ************* -->
<div class="card">
<h2>Enroll Student</h2>

<?php if(isset($_SESSION['success'])): ?>
  <div class="alert-msg success-msg"><?= $_SESSION['success'] ?></div>
  <?php unset($_SESSION['success']);?>
<?php endif;?>


<?php if(isset($_SESSION['error'])): ?>
  <div class="alert-msg error-msg"> <?= htmlspecialchars($_SESSION['error']) ?></div>
  <?php unset($_SESSION['error']);?>
<?php endif;?>

<form action="../actions/enrollment_handler.php" method="POST" id="enrollForm" >

  <div class="enroll-grid">
    <div class="enroll-group">
      <label for="student_id">Student</label>
      <select name="student_id" id="student_id"  required>
        <option value="">Select Student</option>
        <?php foreach($students as $student):?>
          <option value="<?= $student['id'] ?>">
            <?= htmlspecialchars($student['first_name'])."  ".htmlspecialchars($student['last_name']) ?>
            (<?= $student['email'] ?>)
          </option>
        <?php endforeach;?>
      </select>
    </div>

    <div class="enroll-group">
      <label for="course_id">Select Course</label>
        <select name="course_id" id="course_id"  required>
          <option value="">Selsect Course</option>
          <?php foreach ($courses as $course):?>
            <option value="<?= $course['id'] ?>" data-price="<?= $course['price'] ??0 ?>">
              <?= htmlspecialchars($course['name']) ?>
              (<?= $course['price']!==null? number_format($course['price'],2):"Price not set" ?>)
            </option>
        <?php endforeach;?>
        </select>
    </div>
  </div>

  
  
  <h3 for="" class="payment-title">Payment Method</h3>    
  <div class="payment-plane"> 
      <label for="" class="payment-plane-label">
        <input type="radio" name="payment_type" value="full" checked>
        <img src="../assets/imgs/payment-svgrepo-com.svg" alt="">
        Full Payment
      </label>
      <label for="" class="payment-plane-label">
        <input type="radio" name="payment_type" value="installments">
        <img src="../assets/imgs/pay-code-one-svgrepo-com.svg" alt="">
        Installment
      </label>
  
    <div id="installmentsBox" class="installment-box">
      <label for="num_installments">Number of Installments</label>
      <input type="number" name="num_installments" id="num_installments" min="2" max="12" value="2">
    </div>

    </div>
    <div class="price-preview" id="pricePreview" style="display:none;"></div>

    <button type="submit" class='enroll-btn btn' name="enroll"><img src="../assets/imgs/add-user-svgrepo-com (4).svg" alt="">Enroll Student</button>
</form>
</div>
<!-- ----------------TABLE-------------- -->
<h2>Enrollments</h2>
<div class="card">
<div class="table-header">

  <form action="" method="GET" class='search-form'>
    <input type="hidden" name="page" value="enrollment">    
      <div class="search-box">
        <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
        <input type="text" name="search" placeholder="Search by student name..">
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
        <a href="export_pdf/export_enrollments_pdf.php"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
        <a href="export_excel/export_enrollments_xlsx.php"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
      </div>
    </div>
  </div>
</div>

<div class="table-container">
<table border="1">
  <tr>
    <th>Student</th>
    <th>Course</th>
    <th>Course Price</th>
    <th>Enrollment Date</th>
    <th>Installments Number</th>
    <th>Payment Status</th>
    <th>Actions</th>
  </tr>
  <tbody>
    <?php if(!$enrollments):?>
      <tr>
        <td colspan="5" class="msg-td">No Enrollments Found.</td>
      </tr>
    <?php endif;?>

    <?php foreach($enrollments as $enrollment):
      if($enrollment['total_installments']==0 && $enrollment['price']==0){
        $paymentStatus="Free course (no installments).";
      }elseif($enrollment['total_installments']==0 && $enrollment['price']!=0){
        $paymentStatus="Full Payment";
      }
      elseif($enrollment['paid_installments']==$enrollment['total_installments']){
        $paymentStatus="paidb in full";
      }elseif($enrollment['paid_installments']>0){
        $paymentStatus=$enrollment['paid_installments']." / ". $enrollment['total_installments']." (installments)";
      }
      else{
        $paymentStatus="Unpaid";
      }
      
      
      ?>
    <tr>
      <td><?= htmlspecialchars($enrollment['first_name']) ." ".htmlspecialchars($enrollment['last_name']) ?></td>
      <td><?= htmlspecialchars($enrollment['course_name']) ?></td>
      <td><?= htmlspecialchars($enrollment['price']) ?></td>
      <td><?= htmlspecialchars($enrollment['enrollment_date']) ?></td>
      <td><?= htmlspecialchars($enrollment['total_installments']) ?></td>
      <td><?= htmlspecialchars($paymentStatus) ?></td>
      <td class="action-td">
        <form action="../actions/enrollment_handler.php" method="POST">
          <input type="hidden" name="student_id" value="<?= $enrollment['student_id'] ?>">
          <input type="hidden" name="course_id" value="<?= $enrollment['course_id'] ?>">
          <button class="btn delete-btn" name="unenroll" onclick="return confirm('Uninstall this sdtudent?')"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon"></button>
        </form>
      </td>
    </tr>
    <?php endforeach;?>
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

      <!-- first-page btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="layout.php?page=enrollment&p=1" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=enrollment&p=<?= $i ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last-page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=enrollment&p=<?= $paginator->totalPages ?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>

<script>
  $('#student_id').select2({
      placeholder: 'Search by student name...',
      width: '100%'
  });
  $('#course_id').select2({
      placeholder: 'Search by course name...',
      width: '100%'
  });

  const paymentRadios = document.querySelectorAll('input[name="payment_type"]');
  const installmentsBox = document.getElementById('installmentsBox');
  const numInstallmentsInput = document.getElementById('num_installments');
  const pricePreview = document.getElementById('pricePreview');
  const courseSelect = document.getElementById('course_id');

  function toggleInstallmentsBox() {
      const isInstallments = document.querySelector('input[name="payment_type"]:checked').value === 'installments';
      installmentsBox.style.display = isInstallments ? 'block' : 'none';
      updatePricePreview();
  }

  function updatePricePreview() {
      const selectedOption = courseSelect.options[courseSelect.selectedIndex];
      if (!selectedOption || !selectedOption.value) {
          pricePreview.style.display = 'none';
          return;
      }

      const price = parseFloat(selectedOption.dataset.price || 0);
      const isInstallments = document.querySelector('input[name="payment_type"]:checked').value === 'installments';

      if (price === 0) {
          pricePreview.textContent = 'This course is free.No installments are required.';
          pricePreview.style.display = 'block';
          return;
      }

      if (isInstallments) {
          const num = parseInt(numInstallmentsInput.value || 2);
          const perInstallment = (price / num).toFixed(2);
          pricePreview.textContent = `${price.toFixed(2)} will be divided into ${num} installments (approximately ${perInstallment} each).`;
      } else {
        pricePreview.textContent = `A single installment for the full amount of ${price.toFixed(2)} will be created.`;
      }
      pricePreview.style.display = 'block';
  }

  paymentRadios.forEach(radio => radio.addEventListener('change', toggleInstallmentsBox));
  numInstallmentsInput.addEventListener('input', updatePricePreview);
  $('#course_id').on('change', updatePricePreview); 
  toggleInstallmentsBox();
    </script>
