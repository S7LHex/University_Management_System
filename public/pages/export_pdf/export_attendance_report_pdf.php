<?php
ob_start(); // يمسح أي output زايد قبل تصدير الـ PDF

if(session_status()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role'])|| $_SESSION['role']!=='admin'){
  header('Location:../login.php');
  exit;
}

require_once __DIR__."/../../../vendor/autoload.php";
require_once __DIR__."/../../../config/db.php";
require_once __DIR__."/../../../repositories/attendance_recordes.php";
require_once __DIR__."/../../../repositories/course_repository.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo=new Database;
$conn=$pdo->connect();
$attendanceModel= new AttendanceRecord($conn);
$courseRepo= new CourseRepository($conn);

$courseId=isset($_GET['course_id']) ? (int) $_GET['course_id'] : 0;
if(!$courseId){
  die("Please select course from <a href='layout.php?page=students_attendance_report'>Attendance Page</a>");
}

$course= $courseRepo->getCourseBYId($courseId);
$courseStates= $attendanceModel->getAllCourseState($courseId);

// نبني صفوف الجدول
$rows='';
foreach($courseStates as $state){
  $totalSessions= (int) $state['total_sessions'];
  $present= (int) $state['present'];
  $pct= $totalSessions>0 ? round(($present/$totalSessions) * 100) : 0;
  if ($pct >= 75) {
    $color = "#28a745";
  }elseif ($pct >= 50) {
    $color = "#ffc107";
  } else {
    $color = "#dc3545";
  }

  $rows .="
        <tr>
          <td>". htmlspecialchars($state['first_name'])." ".htmlspecialchars($state['last_name']) . "</td>
          <td> {$totalSessions} </td>
          <td> {$present} </td>
          <td>" . htmlspecialchars((int)$state['absent_count']) ."</td>
          <td>" . htmlspecialchars((int)$state['late_count']) ."</td>
          <td>" . htmlspecialchars((int)$state['excused_count']) ."</td>
          <td style='color:{$color}; font-weight:bold;'>{$pct}%</td>
          </tr>";
}

$courseName = $course ? htmlspecialchars($course['name']) : "Course #$courseId";

$html = "
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
    <h2>Students Attendance Report</h2>
    <p><strong>Course:</strong> {$courseName}</p>
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Sessions Count</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Excused</th>
                <th>Attendance Rate</th>
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


 // يمسح أي output تجمّع قبل الـ stream
ob_end_clean();

$dompdf->stream('attendance_report.pdf',['Attachment'=>true]);
exit; // يمنع أي كود إضافي يشتغل بعد إرسال الملف