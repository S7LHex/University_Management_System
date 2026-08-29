<?php
if(session_status()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
} 

require_once __DIR__."/../../../vendor/autoload.php";
require_once __DIR__."/../../../config/db.php";
require_once __DIR__."/../../../repositories/attendance_recordes.php";
require_once __DIR__."/../../../repositories/course_repository.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$pdo=new Database;
$conn= $pdo->connect();

$attendanceModel= new AttendanceRecord($conn);
$courseRepo= new CourseRepository($conn);

$courseId=isset($_GET['course_id'])? (int) $_GET['course_id']:0;
if($courseId<=0){
  die("Please select course from from <a href='layout.php?page=students_attendance_report&course_id=$courseId'>Attendance Page </a> ");
}

$course=$courseRepo->getCourseBYId($courseId);
$courseName=$course['name'] ?? ('course_'. $courseId); 

$courseState=$attendanceModel->getAllCourseState($courseId);

// ********* CREATE EXCEL FILE ***************************************

$spreadsheet= new Spreadsheet();
$sheet= $spreadsheet->getActiveSheet();

$sheet->setTitle('Students Attendance Report');

// HEADER ROW
$headers=['Student','Sessions Count','Present','Absent','Late','Excused','Attendance Rate'];
$col='A';
foreach($headers as $header){
  $sheet->setCellValue($col.'1',$header);
  $col++; 
}

$sheet->getStyle('A1:G1')->getFont()->setBold(true);
$sheet->getStyle('A1:G1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setRGB('E0E0E0');

// WRITE DATA
$row=2;
foreach($courseState as $state){
  $totalSessions= (int)$state['total_sessions'];
  $present= (int)$state['present_count'];
  $pct=$totalSessions>0? round(($present/$totalSessions) * 100) : 0;
  
  $sheet->setCellValue('A'.$row,$state['first_name'] .' '. $state['last_name']);
  $sheet->setCellValue('B'.$row,$totalSessions);
  $sheet->setCellValue('C'.$row,$present);
  $sheet->setCellValue('D'.$row,(int)$state['absent_count']);
  $sheet->setCellValue('E'.$row,(int)$state['late_count']);
  $sheet->setCellValue('F'.$row,(int)$state['excused_count']);
  $sheet->setCellValue('G'.$row,$pct);
  $row++;
}

// توسيع عرض الأعمدة تلقائياً حسب المحتوى
foreach(range('A','G') as $columnLetter){
  $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
}
// ============ الإخراج للمتصفح ============
// اسم ملف آمن (يشيل أي رموز غير مسموحة بأسماء الملفات)
$safeFileName=preg_replace('/[^A-Za-z0-9\-_]/','_',$courseName);
$fileName='attendance_report_' . $safeFileName . '_' . date('Y-m-d') . '.xlsx';

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