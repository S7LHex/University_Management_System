<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['user_id']) ||!isset($_SESSION['role']) ||$_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
} 

require_once __DIR__ ."/../../../vendor/autoload.php";
require_once __DIR__ ."/../../../config/db.php";
require_once __DIR__ ."/../../../repositories/paymentRepository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo= new Database;
$conn= $pdo->connect();
$paymentsRepo= new PaymentRepository($conn);

$filters=[
  'status'=> $_GET['status'] ?? null,
  'student_id'=>$_GET['student_id'] ?? null,
  'course_id' =>$_GET['course_id'] ?? null
];

$records= $paymentsRepo->getPayments(array_filter($filters));
if(empty($records)){
  die('No results found.');
}

$spreadsheet= new Spreadsheet;
$sheet=$spreadsheet->getActiveSheet();

$sheet->setTitle('Payments Report');

$headers=['Student','Course','Course Price','Installment Number','Amount','Due Date','Status','Payment Method'];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}
$sheet->getStyle('A1:H1')->getFont()->setBold(true);
$sheet->getStyle('A1:H1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');

$rows=2;
foreach($records as $record){
  $sheet->setCellValue('A'.$rows,$record['first_name'] .' '.$record['last_name']);
  $sheet->setCellValue('B'.$rows,$record['course_name']);
  $sheet->setCellValue('C'.$rows,$record['price']);
  $sheet->setCellValue('D'.$rows,$record['installment_number']);
  $sheet->setCellValue('E'.$rows,$record['amount']);
  $sheet->setCellValue('F'.$rows,$record['due_date']);
  $sheet->setCellValue('G'.$rows,$record['status']);
  $sheet->setCellValue('H'.$rows,$record['payment_method']);
  $rows++;
}

foreach(range('A','H') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

$fileName="Payments_report_". date('d-m-Y') . ".xlsx";

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