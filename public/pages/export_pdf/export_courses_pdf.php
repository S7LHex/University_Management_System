<?php
ob_start();

if(session_start()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
  header('Location:../login.php');
  exit;
}

require_once __DIR__ ."/../../../vendor/autoload.php";
require_once __DIR__ ."/../../../config/db.php";
require_once __DIR__ ."/../../../repositories/course_repository.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo= new Database;
$conn= $pdo->connect();
$courseRepo= new CourseRepository($conn);

$courses= $courseRepo->getAllCourses();
if(empty($courses)){
  die('No courses found.');
}

$rows="";
foreach($courses as $course){
  $courseTeachers=$courseRepo->getCourseTeachers($course['id']);
  $teacherNames=array_map(function($teacher){
    return $teacher['first_name'] ."  ". $teacher['last_name'];
  },$courseTeachers);

  
$rows .=
  "<tr>
    <td>". htmlspecialchars($course['code']) ."</td>
    <td>". htmlspecialchars($course['name']) ."</td>
    <td>". implode(', ',$teacherNames) ."</td>
    <td>". htmlspecialchars($course['start_date'])."</td>
    <td>". htmlspecialchars($course['end_date']) ."</td>
    <td>". htmlspecialchars($course['postpone_date']) ."</td>
    <td>". htmlspecialchars($course['total_hours']) ."</td>
    <td>". htmlspecialchars($course['start_time']) ."</td>
    <td>". htmlspecialchars($course['end_time']) ."</td>
    <td>". htmlspecialchars($course['days']) ."</td>
    <td>". htmlspecialchars($course['max_students'])."</td>
    <td>". htmlspecialchars($course['price']) ."</td>
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
    <h2>Courses Details</h2>
    <table>
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Teachers</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Postpone Date</th>
          <th>Total Hours</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Days</th>
          <th>Max Students</th>
          <th>Price</th> 
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

ob_end_clean();

$dompdf->stream('course_details',['Attachment'=>true]);
exit;



















?>