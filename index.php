<?php
session_start();

require_once "config/db.php";
require_once "repositories/installer.php";

$installer=new Installer;
if(!$installer->adminExists()){
  header('Location:public/pages/install.php');
  exit;
} 

if(isset($_SESSION['user_id'])){
  if($_SESSION['role']==='admin'){
    header('Location:public/pages/layout.php');
  }elseif($_SESSION['role']==='teacher'){
    header('Location:public/pages/teacher_dashboard.php');
  }elseif($_SESSION['role']==='student'){
    header('Location:public/pages/student_dashboard.php');
  }
  exit;
}
header('Location:public/pages/login.php');
exit;









?>