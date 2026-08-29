<?php
if(session_status()==PHP_SESSION_NONE){
  session_start();
}

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
  header('Location: ../login.php');
  exit;
}

require_once __DIR__."/../../../vendor/autoload.php";
require_once __DIR__."/../../../config/db.php";
require_once __DIR__."/../../../repositories/enrollmentrepository.php";
require_once __DIR__."/../../../repositories/paymentRepository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo= new Database;
$conn=$pdo->connect();
$paymentRepo= new PaymentRepository($conn);
$enrollmentRepo= new Enrollment($conn,$paymentRepo);

$enrollments=$enrollmentRepo->getAllEnrollments();
if(empty($enrollments)){
  die('No enrollments found.');
}

$spreadsheet= new Spreadsheet;
$sheet=$spreadsheet->getActiveSheet();

$sheet->setTitle('Enrollments');

$headers=['Student','Course','Course Price','Enrollment Date','Installments Number','Payment Status'];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}

$sheet->getStyle('A1:F1')->getFont()->setBold(true);
$sheet->getStyle('A1:F1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');

$row=2;
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
  $sheet->setCellValue('A'.$row,$enroll['first_name'] ."  ". $enroll['last_name']);
  $sheet->setCellValue('B'.$row,$enroll['course_name']);
  $sheet->setCellValue('C'.$row,$enroll['price']);
  $sheet->setCellValue('D'.$row,$enroll['enrollment_date']);
  $sheet->setCellValue('E'.$row,$enroll['total_installments']);
  $sheet->setCellValue('F'.$row,$paymentStatus);

  $row++;
}    
foreach(range('A','F') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

$fileName="Enrollments_" . date('d/m/Y') . '.xlsx';

if (ob_get_length()) {
  ob_end_clean();
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>