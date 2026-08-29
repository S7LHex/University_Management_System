<?php

require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__."/../../repositories/course_repository.php";
require_once __DIR__."/../../repositories/paymentRepository.php";
require_once __DIR__."/../../repositories/student_repository.php";
require_once __DIR__."/../../repositories/paginator.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn=$pdo->connect();

$paymentRepo= new PaymentRepository($conn);
$studentRepo = new StudentRepository($conn);
$courseRepo=  new CourseRepository($conn);


if(!isset($_SESSION['user_id'])||$_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}


$filters=[
  'status'=> $_GET['status'] ?? null,
  'student_id'=>$_GET['student_id'] ?? null,
  'course_id' =>$_GET['course_id'] ?? null
];
$activeFilters=array_filter($filters);

// ***********PAGINATION*******************************************
$page=isset($_GET['p']) ? (int)$_GET['p'] :1;
$perPage=5;
$totalItems=$paymentRepo->countAllPayments($activeFilters);
$paginator= new Paginator($page,$perPage,$totalItems);
//***********************************************************/
$payments=$paymentRepo->getAllPayments($activeFilters,$paginator->perPage,$paginator->offset);

$summary=$paymentRepo->getPaymentsSummary();

$students= $studentRepo->getAllStudents();
$courses=$courseRepo->getAllCourses();

function buildLink($page,$filters){
  $params=array_merge($filters,['p'=>$page]);
  $params=array_filter($params,fn($v) =>$v !== null && $v !== '');
  return 'layout.php?page=payments&' . http_build_query($params);

}

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<h2>Payments Managements</h2>

<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Payments Managements</span>
</nav>

  
  <div class="summary-boxes">

    <div class="summary-box box-paid">
      <img src="../assets/imgs/paid-svgrepo-com.svg" alt="">
      <div>
        <label>Total Collected</label>
        <div class="value paid"><?= number_format((float)($summary['total-paid']??0) , 2) ?></div>
        </div>
    </div>

    <div class="summary-box box-unpaid">
      <img src="../assets/imgs/unpaid-svgrepo-com (1).svg" alt="">
      <div>
        <label class="label">Total Outstanding</label>
        <div class="value unpaid"><?= number_format((float)($summary['total_unpaid']??0),2) ?></div>
      </div>
    </div>

    <div class="summary-box box-refunded">
      <img src="../assets/imgs/refund-back-svgrepo-com.svg" alt="">
      <div>
        <label>Total Refunded<label>
        <div class="value refunded"><?= number_format((float)($summary['total_refunded']??0),2) ?></div>
      </div>
    </div>
  </div>

<div class="card">
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert-msg error-msg"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error'])?>
  <?php endif; ?>

  <?php if(isset($_SESSION['success'])):?>
    <div class="alert-msg success-msg"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
    <div class="table-header">
      <form action="" method="GET">
        <input type="hidden" name="page" value="payments">
        <div class="filters">
          <div class="filter-item">
            <label for="">Status</label>
            <select name="status" id="" onchange="this.form.submit()">
              <option value="">All Status</option>
              <option value="unpaid" <?= ($_GET['status']?? '')==='unpaid'?'selected':'' ?>>Unpaid</option>
              <option value="paid" <?= ($_GET['status']?? '')==='paid'?'selected':'' ?>>Paid</option>
              <option value="refunded" <?= ($_GET['status']?? '')==='refunded'?'selected':'' ?>>Refunded</option>
              <option value="cancelled" <?= ($_GET['status']?? '')==='cancelled'?'selected':'' ?>>Cancelled</option>
            </select>
          </div>

          <div class="filter-item">
            <label for="">Student</label>
            <select name="student_id" id="student_id">
              <option value="">All Students</option>
              <?php foreach($students as $s):?>
                <option value="<?= $s['id'] ?>" <?= ($_GET['student_id']??'')==$s['id']?'selected':'' ?>>
                  <?= htmlspecialchars($s['first_name'] . " ".$s['last_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
          </div>

        <div class="filter-item">
          <label for="">Course</label>
          <select name="course_id" id="" onchange="this.form.submit()">
            <option value="">All Courses</option>
            <?php foreach($courses as $c): ?>
              <option value="<?= $c['id'] ?>"<?= ($_GET['course_id'] ?? '')==$c['id']?'selected':'' ?>>
                <?= htmlspecialchars($c['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
  </form>

  <div class="dropdown">
    <button class='btn export-btn'>
      <img src="../assets/imgs/download-2-svgrepo-com (1).svg" alt="" class="export-icon">
      Export
      <img src="../assets/imgs/chevron-down-svgrepo-com.svg" alt="" class="export-icon">
    </button>
    <div class="dropdown-content">
      <a href="export_pdf/export_payments_pdf.php?<?= http_build_query(array_filter($filters)) ?>"><img src="../assets/imgs/pdf-file-svgrepo-com.svg" alt="" class="export-icon"> Export PDF</a>
      <a href="export_excel/export_payments_excel.php?<?= http_build_query(array_filter($filters)) ?>"><img src="../assets/imgs/excel-svgrepo-com.svg" alt="" class="export-icon"> Export Excel</a>
    </div>
  </div>
</div>

  <div class="table-container">
<table border=1>
  <tr>
    <th>Student</th>
    <th>Course</th>
    <th>Course Price</th>
    <th>Installment Number</th>
    <th>Amount</th>
    <th>Due Date</th>
    <th>Status</th>
    <th>Payment Method</th>
    <th>Action</th>
  </tr>
  <tbody>
    <?php if (empty($payments)):?>
      <tr><td colspan="9" class="msg-td">No matching results found.</td></tr>
    <?php else:?> 

    <?php foreach($payments as $p):
      $isOverdue=$p['status']=='unpaid' && strtotime($p['due_date']) < strtotime(date('Y-m-d'));

      $statusLabels=['paid'=>'paid' ,"unpaid"=>'unpaid', 'refunded'=>'refunded','cancelled'=>'cancelled'];
      $statusLabel=$statusLabels[$p['status']]??$p['status'];

      $methodlabels=['cash'=>'cash','bank_transfer'=>'bank_transfer','cared'=>'card'];
      $methodlabel=$p['payment_method'] ? ($methodlabels[$p['payment_method']] ?? $p['payment_method']) :'__';
    ?>
    <tr>
      <td><?= htmlspecialchars($p['first_name'] . " ".$p['last_name']) ?></td>
      <td><?= htmlspecialchars($p['course_name']) ?></td>
      <td><?= htmlspecialchars($p['price']) ?></td>
      <td><?= htmlspecialchars($p['installment_number']) ?></td>
      <td><?= number_format((float)$p['amount'],2) ?></td>
      <td>
        <?= htmlspecialchars($p['due_date']) ?>
        <?php if($isOverdue): ?>
          <span>Late</span>
          <?php endif; ?>
      </td>
      <td><span><?= $statusLabel ?></span></td>
      <td><?= htmlspecialchars($methodlabel) ?></td>
      <td>
        <!-- MARK AS PAID FORM -->
        <?php if($p['status']==='unpaid'): ?>
          <form action="../actions/payments_handler.php" method='POST' class="mark-as-paid-form">
            <div>
              <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
              <select name="payment_method" id="" required>
                <option value="">Payment Method</option>
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="card">Card</option>
              </select>
              <input type="text" name="notes" placeholder="Notes">
            </div>
            <button class="btn paid-btn" name='mark_as_paid'><img src="../assets/imgs/approval-svgrepo-com.svg" alt="">Mark as paid</button>
          </form>
          <!-- DELETE PAYMENT -->
          <?php elseif($p['status']==='cancelled' && empty($p['enrollment_id'])): ?>
            <form action="../actions/payments_handler.php" method="POST">
              <input type="hidden" name="payment_id" value="<?= $p['id'] ?>">
              <button class="btn delete-btn" name="delete_payment" onclick="return confirm('Cancel this payment?')"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon">Delete Payment</button> 
            </form>
          <?php else: ?>
            <span>__</span>
        <?php endif;?>
      </td>
    </tr>
  <?php endforeach;?>
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

      <!-- first-page btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="<?= buildLink('1',$filters) ?>" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="<?= buildLink($i,$filters) ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last-page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="<?=buildLink($paginator->totalPages ,$filters) ?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
    </div>
  </div>
<?php endif; ?>
</div>
</div>
<script>
  $('#student_id').select2({
      placeholder: 'Search by student name...',
      allowClear: true,
      width: '100%'
  }).on('change',function(){
    this.form.submit();
  });
</script>