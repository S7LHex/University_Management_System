<?php
ob_start();

if(session_status()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role'])|| $_SESSION['role']!=='admin'){
  header('Location:../login.php');
  exit;
}

require_once __DIR__ ."/../../../vendor/autoload.php";
require_once __DIR__ ."/../../../config/db.php";
require_once __DIR__ ."/../../../repositories/enrollmentrepository.php";
require_once __DIR__ ."/../../../repositories/paymentRepository.php";
use Dompdf\Dompdf;
use Dompdf\Options;


$pdo = new Database;
$conn= $pdo->connect();
$paymentsRepo=new PaymentRepository($conn);
$enrollRepo= new Enrollment($conn,$paymentsRepo);

$enrollments=$enrollRepo->getAllEnrollments();
if(empty($enrollments)){
  die('No enrollments found.');
}

$rows="";
foreach($enrollments as $enroll){
  if($enroll['total_installments']==0 && $enroll['price']==0){
    $paymentStatus="Free course (no installments).";
  }elseif($enroll['total_installments']==0 && $enroll['price']!=0){
    $paymentStatus="Full Payment";
  }
  elseif($enroll['paid_installments']==$enroll['total_installments']){
    $paymentStatus="paidb in full";
  }elseif($enroll['paid_installments']>0){
    $paymentStatus=$enroll['paid_installments']." / ". $enroll['total_installments']." (installments)";
  }
  else{
    $paymentStatus="Unpaid";
    }
$rows .="
<tr>
  <td>". htmlspecialchars($enroll['first_name']) ." ".htmlspecialchars($enroll['last_name']) ."</td>
  <td>". htmlspecialchars($enroll['course_name']) ."</td>
  <td>". htmlspecialchars($enroll['price']) ."</td>
  <td>". htmlspecialchars($enroll['enrollment_date']) ."</td>
  <td>". htmlspecialchars($enroll['total_installments']) ."</td>
  <td>". htmlspecialchars($paymentStatus) ."</td>
</tr>";
}

$html="<html>
<head>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    h2 { text-align: center; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #333; padding: 6px; text-align: center; }
    th { background-color: #2c3e50; color: white; }
    .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: left; }
</style>
</head>
<body>
    <h2>Enrollments</h2>
    <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Course Price</th>
            <th>Enrollment Date</th>
            <th>Installments Number</th>
            <th>Payments Status</th>
          </tr>
        </thead>
        <tbody>
          {$rows}
        </tbody>
    </table>
    <div class='footer'>Generated on " . date('Y-m-d H:i') . "</div>
</body>
</html>
";

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf= new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();
ob_end_clean();

$dompdf->stream('enrollments.pdf',['Attachment'=>true]);
exit;
?>