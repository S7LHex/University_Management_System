<?php
if(session_status()===PHP_SESSION_NONE){
  session_start();
}

require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/notification.php";

header('Content-type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin'){
  http_response_code(403);
  echo json_encode(['success' => false,'message'=>'Unauthorized']);
  exit;
}

$pdo= new Database;
$conn=$pdo->connect();
$notificationRepo= new Notification($conn);

$unread=$notificationRepo->getUnreadForUser($_SESSION['user_id']);
foreach($unread as $n){
  $notificationRepo->markAsRead((int) $n['id']);
}
echo json_encode(['success'=>true, 'marked_count'=>count($unread)]);

?>