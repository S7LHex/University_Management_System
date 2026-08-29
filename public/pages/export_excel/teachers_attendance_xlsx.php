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
require_once __DIR__ ."/../../../repositories/teacher_attendance.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo= new Database;
$conn= $pdo->connect();

$attendanceModel= new TeacherAttendance($conn);

$filters=[
  "teacher_id"=>$_GET['teacher_id'] ?? null,
  "course_id"=>$_GET['course_id'] ?? null,
  "date_from"=>$_GET['date_from'] ?? null,
  "date_to"=>$_GET['date_to'] ?? null
];

$records=$attendanceModel->getAllAttendance(array_filter($filters));
if(empty($records)){
  die('No results found.');
}

$spreadsheet= new Spreadsheet;
$sheet=$spreadsheet->getActiveSheet();

$sheet->setTitle('Teachers Attendance');

$headers=['Teacher',"Course",'Date','Attendance Time','Status'];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}

$sheet->getStyle('A1:E1')->getFont()->setBold(true);
$sheet->getStyle('A1:E1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');

$row=2;
foreach($records as $record){
  $sheet->setCellValue('A'.$row ,$record['first_name']. "  ". $record['last_name']);
  $sheet->setCellValue('B'.$row,$record['course_name']);
  $sheet->setCellValue('C'.$row,$record['session_date']);
  $sheet->setCellValue('D'.$row,$record['check_in_time']);
  $sheet->setCellValue('E'.$row,$record['status']);
  $row++;
}    

foreach(range('A','E') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

$fileName="teachers_attendance_". date('d-m-Y') . ".xlsx";

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