<?php

function requireRole(array $allowedRoles,string $redirectTo='login.php'){
  if(session_status()=== PHP_SESSION_NONE){
    session_start();
  }
  if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'],$allowedRoles,true)){
    header('Location:public/pages/'.$redirectTo);
    exit;
  }
}


?>

<!-- كيف تستخدمها بكل صفحة أستاذ (متلاً teacher/attendance_take.php)

بدل الكود المكرر يلي عندك بكل صفحة:
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /login.php');
    exit;
}

بتصير سطر واحد بس:


require_once __DIR__ . '/../../helpers/auth.php';
requireRole(['teacher']); -->