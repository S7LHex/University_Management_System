<?php
if(session_status()==PHP_SESSION_NONE){
  session_start();
}

require_once __DIR__."/../../../vendor/autoload.php";
require_once __DIR__."/../../../config/db.php";
require_once __DIR__."/../../../repositories/student_repository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo = new Database;
$conn=$pdo->connect();
$studentRepo= new StudentRepository($conn);

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== "admin"){
  header('Location:../login.php');
  exit;
}

$students=$studentRepo->getAllStudents();

if(empty($students)){
  die('No students found');
}

$spreadsheet= new Spreadsheet;
$sheet= $spreadsheet->getActiveSheet();

$sheet->setTitle('Students');

$headers=["ID",'First Name','Last Name',"Father's Name","Age","Address","Mobile Number","Major","Email"];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}
$sheet->getStyle('A1:I1')->getFont()->setBold(true);
$sheet->getStyle('A1:I1')->getFill()
  ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
  ->getStartColor()->setRGB('E0E0E0');


$row=2;
foreach($students as $student){
  $sheet->setCellValue('A'.$row,$student['id']);
  $sheet->setCellValue('B'.$row,$student['first_name']);
  $sheet->setCellValue('C'.$row,$student['last_name']);
  $sheet->setCellValue('D'.$row,$student['father_name']);
  $sheet->setCellValue('E'.$row,$student['age']);
  $sheet->setCellValue('F'.$row,$student['address']);
  $sheet->setCellValue('G'.$row,$student['mobile_number']);
  $sheet->setCellValue('H'.$row,$student['major']);
  $sheet->setCellValue('I'.$row,$student['email']);
  $row++;
}  

foreach(range('A','I') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

$safeFileName=preg_replace('/[^A-Za-z0-9\-_]/',"_",'Students');
$fileName='students_data_' . $safeFileName . '_' . date('d-m-Y') .'.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$writer= new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
