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
require_once __DIR__ ."/../../../repositories/paymentRepository.php";
use Dompdf\Dompdf;
use Dompdf\Options;

$pdo=new Database;
$conn=$pdo->connect();
$paymentsRepo = new PaymentRepository($conn);

$filters=[
  'status'=> $_GET['status'] ?? null,
  'student_id'=>$_GET['student_id'] ?? null,
  'course_id' =>$_GET['course_id'] ?? null
];

$records= $paymentsRepo->getPayments(array_filter($filters));
if(empty($records)){
  die('No results found.');
}

$rows="";
foreach($records as $record){
  $rows .="
  <tr>
    <td>". htmlspecialchars($record['first_name'])." ". htmlspecialchars($record['last_name'])."</td>
    <td>". htmlspecialchars($record['course_name']) ."</td>
    <td>". htmlspecialchars($record['price']) ."</td>
    <td>". htmlspecialchars($record['installment_number']) ."</td>
    <td>". htmlspecialchars($record['amount']) ."</td>     
    <td>". htmlspecialchars($record['due_date']) ."</td>     
    <td>". htmlspecialchars($record['status']) ."</td>     
    <td>". htmlspecialchars($record['payment_method']) ."</td>     
  </tr>";
}
$html="
<html>
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
    <h2>Payments Report</h2>
    <table>
      <thead>
        <tr>
          <th>Student</th>
          <th>Course</th>
          <th>Course Price</th>
          <th>Installment Number</th>
          <th>Amount</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Payment Method</th>
        </tr>
        </thead>
        <tbody>
          {$rows}
        </tbody>
    </table>
    <div class='footer'>Generated on " . date('Y-m-d H:i') . "</div>
</body>
</html>";
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf= new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();
ob_end_clean();

$dompdf->stream('Payments_report.pdf',['Attachment'=>true]);
exit;
?>
