<?php
if(session_status()===PHP_SESSION_NONE){
  session_start();
}

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../../../repositories/teacher_repository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo= new Database;
$conn=$pdo->connect();
$teacherRepo= new TeacherRepository($conn);

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:../login.php');
  exit;
}

$teachers=$teacherRepo->getAllTeachers();
if(empty($teachers)){
  die('No teachers found.');
}

$spreadsheet= new Spreadsheet;
$sheet=$spreadsheet->getActiveSheet();
$sheet->setTitle('Teachers');

$headers=['ID',"First Name","Last Name","Father's Name","Age","Address","Mobile Number","Major","Salary","Email"];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}

$sheet->getStyle('A1:J1')->getFont()->setBold(true);
$sheet->getStyle('A1:J1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');


$row=2;
foreach($teachers as $teacher){
  $sheet->setCellValue('A'.$row,$teacher['id']);
  $sheet->setCellValue('B'.$row,$teacher['first_name']);
  $sheet->setCellValue('C'.$row,$teacher['last_name']);
  $sheet->setCellValue('D'.$row,$teacher["father_name"]);
  $sheet->setCellValue('E'.$row,$teacher['age']);
  $sheet->setCellValue('F'.$row,$teacher['address']);
  $sheet->setCellValue('G'.$row,$teacher['mobile_number']);
  $sheet->setCellValue('H'.$row,$teacher['major']);
  $sheet->setCellValue('I'.$row,$teacher['salary']);
  $sheet->setCellValue('J'.$row,$teacher['email']);
  $row++;
}

foreach(range('A','J') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

// $saveFileName= preg_replace('/[^A-Za-z0-9\-_]/','_','Teachers');
$fileName="teachers_" . date('d-m-Y') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$writer=new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>