<?php
if(session_status()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role'])|| $_SESSION['role']!=='admin'){
  header('Location: ../login.php');
  exit;
}

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../../../repositories/course_repository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo= new Database;
$conn=$pdo->connect();
$courseRepo= new CourseRepository($conn);



$courses= $courseRepo->getAllCourses();
if (empty($courses)){
  die('No courses found');
}

$spreadsheet= new Spreadsheet();
$sheet=$spreadsheet->getActiveSheet();

$sheet->setTitle('Courses');

$headers=[
  'Code',
  'Name',
  'Teachers',
  'Start Date',
  'End Date',
  'Postpone Date',
  'Total Hours',
  'Start Time',
  'End Time',
  'Days',
  'Max Students',
  'Price'];

$col='A';

foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++;
}

$sheet->getStyle('A1:L1')->getFont()->setBold(true);
$sheet->getStyle('A1:L1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');


$row=2;
foreach($courses as $course){
  $courseTeahers=$courseRepo->getCourseTeachers($course['id']);

  $sheet->setCellValue('A'.$row,$course['code']);
  $sheet->setCellValue('B'.$row,$course['name']);

  $teachersNames=array_map(function($teacher){
    return $teacher['first_name'] . ' '. $teacher['last_name'] ; 
    },$courseTeahers);

  $sheet->setCellValue('C'.$row,implode(', ',$teachersNames));
  $sheet->setCellValue('D'.$row,$course['start_date']);
  $sheet->setCellValue('E'.$row,$course['end_date']);
  $sheet->setCellValue('F'.$row,$course['postpone_date']);
  $sheet->setCellValue('G'.$row,$course['total_hours']);
  $sheet->setCellValue('H'.$row,$course['start_time']);
  $sheet->setCellValue('I'.$row,$course['end_time']);
  $sheet->setCellValue('J'.$row,$course['days']);
  $sheet->setCellValue('K'.$row,$course['max_students']);
  $sheet->setCellValue('L'.$row,$course['price']);
  $row++;
}    

foreach(range('A','L') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}

$fileName='Courses_'. date('d-m-Y') . ".xlsx";

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