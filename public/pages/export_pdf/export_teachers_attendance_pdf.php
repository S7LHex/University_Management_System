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
require_once __DIR__ ."/../../../repositories/teacher_attendance.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo=new Database;
$conn=$pdo->connect();
$attendanceModel= new TeacherAttendance($conn);

$filters=[
  "teacher_id"=>$_GET['teacher_id'] ?? null,
  "course_id"=>$_GET['course_id'] ?? null,
  "date_from"=>$_GET['date_from'] ?? null,
  "date_to"=>$_GET['date_to'] ?? null
];

$records=$attendanceModel->getAllAttendance(array_filter($filters));
if(empty($records)){
  die('No attendance records found.');
}

$rows="";
foreach($records as $record){
  $rows .="
  <tr>
    <td>". htmlspecialchars($record['first_name'])." ". htmlspecialchars($record['last_name'])."</td>
    <td>". htmlspecialchars($record['course_name']) ."</td>
    <td>". htmlspecialchars($record['session_date']) ."</td>
    <td>". htmlspecialchars($record['check_in_time']) ."</td>
    <td>". htmlspecialchars($record['status']) ."</td>     
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
    <h2>Students Attendance Report</h2>
    <table>
      <thead>
        <tr>
          <th>Teacher</th>
          <th>Course</th>
          <th>Date</th>
          <th>Attendance Time</th>
          <th>Status</th>
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

$dompdf->stream('teacher_attendance_report.pdf',['Attachment'=>true]);
exit;
?>