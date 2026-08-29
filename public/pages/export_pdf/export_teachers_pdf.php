<?php
ob_start();

if(session_status()===PHP_SESSION_NONE){
  session_start();
}
if(!isset($_SESSION['user_id']) || !isset($_SESSION['role'])|| $_SESSION['role']!=='admin'){
  header('Location:../login.php');
  exit;
}

require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../../../repositories/teacher_repository.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = new Database;
$conn=$pdo->connect();
$teacherRepo=new TeacherRepository($conn);

$teachers=$teacherRepo->getAllTeachers();
if(empty($teachers)){
  die('No teachers found.');
}

$rows="";
foreach($teachers as $teacher){
$rows .="
  <tr>
    <td>". htmlspecialchars($teacher['id']) ."</td>
    <td>". htmlspecialchars($teacher['first_name']) ."</td>
    <td>". htmlspecialchars($teacher['last_name'])  ."</td>
    <td>". htmlspecialchars($teacher['father_name']) ."</td>
    <td>". htmlspecialchars($teacher['age']) ."</td>
    <td>". htmlspecialchars($teacher['address']) ."</td>
    <td>". htmlspecialchars($teacher['mobile_number']) ."</td>
    <td>". htmlspecialchars($teacher['major'])."</td>
    <td>". htmlspecialchars($teacher['salary']) ."</td>
    <td>". htmlspecialchars($teacher['email']) ."</td>
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
    <h2>Teachers Details</h2>
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
          <th>Salary</th>
          <th>Email</th>
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

$options= new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$dompdf= new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','landscape');
$dompdf->render();

ob_end_clean();
$dompdf->stream('teacher_details.pdf',['Attachment'=>true]);
exit;





?>
