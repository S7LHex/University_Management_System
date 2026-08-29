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
require_once __DIR__ ."/../../../repositories/student_repository.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo= new Database;
$conn=$pdo->connect();

$studentRepo= new StudentRepository($conn);

$students=$studentRepo->getAllStudents();
if(empty($students)){
  die('No students found.');
}

$rows="";
foreach($students as $student){
  $rows .= "
  <tr>
      <td>". htmlspecialchars($student['id'])."</td>
      <td>". htmlspecialchars($student['first_name'])."</td>
      <td>". htmlspecialchars($student['last_name'])."</td>
      <td>". htmlspecialchars($student['father_name'])."</td>
      <td>". htmlspecialchars($student['age'])."</td>
      <td>". htmlspecialchars($student['address'])."</td>
      <td>". htmlspecialchars($student['mobile_number'])."</td>
      <td>". htmlspecialchars($student['major'])."</td>
      <td>". htmlspecialchars($student['email'])."</td>
  </tr>";
}

$html="
<html>
  <head>
    <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
    h2 {text-align:center ; color: #333}
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #333; padding: 6px; text-align: center; }
    th { background-color: #2c3e50; color: white; }
    .footer { margin-top: 20px; font-size: 12px; color: #777; text-align: left; }  
    </style>
  </head>
  <body>
    <h2>Students</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Father's Name</th>
          <th>Age</th>
          <th>Address</th>
          <th>Mobile Number</th>
          <th>Major</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        {$rows}
      </tbody>
    </table>
    <div class='footer'>Generated on :".date("d-m-Y H:i")."</div>
  </body>
</html>";

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadhtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();

ob_end_clean();
$dompdf->stream('Students_details.pdf',['Attachment'=>true]);
exit;

?>