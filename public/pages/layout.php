<?php
if(session_status() === PHP_SESSION_NONE){
  session_start();
}

if(!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role']!=='admin'){
  header('Location:login.php');
  exit;
}
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/user_repositories.php";
$pdo= new Database;
$conn=$pdo->connect();
$userRepo= new User($conn);
$user=$userRepo->getUsersById($_SESSION['user_id']);


$page=$_GET['page'] ?? 'dashboard';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/add_edit.css">
  <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
</head>
<body>
  <aside id="sidebar">
    <div class="logo">
      <img src="../assets/imgs/university.svg" alt="" class="icon">
      <div class="title">
        <h3>S7L</h3>
        <span>University System</span>
      </div>
    </div>
    <ul>
      <li class="<?= $page=='dashboard'?'active':'' ?>"><a href="layout.php?page=dashboard"><img src="../assets/imgs/dashboard.svg" alt="" class="icon"><span>Dashboard</span></a></li>
      <li class="<?= $page=='students'?'active':'' ?>"><a href="layout.php?page=students"><img src="../assets/imgs/670690-200.png" class="icon"><span>Students</span> </a></li>
      <li class="<?= $page=='teachers'?'active':'' ?>"><a href="layout.php?page=teachers"><img src="../assets/imgs/chalkboard-teacher.svg" class="icon" alt=""><span>Teachers</span></a></li>
      <li class="<?= $page=='courses'?'active':'' ?>"><a href="layout.php?page=courses"><img src="../assets/imgs/elearning-learning-online-book-computer.svg" class="icon" alt=""><span>Courses</span></a></li>
      <li class="<?= $page=='enrollment'?'active':'' ?>"><a href="layout.php?page=enrollment"><img src="../assets/imgs/register.svg" class="icon" alt=""><span>Enrollment</span></a></li>
      <li class="<?= $page=='payments'?'active':'' ?>"><a href="layout.php?page=payments"><img src="../assets/imgs/credit-card-svgrepo-com.svg" class="icon" alt=""><span>Payments</span></a></li>

      <li class="<?= $page=='teachers_attendance_report'?'active':'' ?>"><a href="layout.php?page=teachers_attendance_report"><img src="../assets/imgs/checklist-on-clipboard-svgrepo-com (1).svg" class="icon" alt=""><span>Teachers Attendance</span></a></li>
      <li class="<?= $page=='students_attendance_report'?'active':'' ?>"><a href="layout.php?page=students_attendance_report"><img src="../assets/imgs/classlist-svgrepo-com (1).svg" class="icon" alt=""><span>Students Attendance</span></a></li>
      <li class="<?= $page=='users_management'?'active':'' ?>"><a href="layout.php?page=users_management"><img src="../assets/imgs/add-user.svg" class="icon" alt=""><span>Users Management</span></a></li>
      <li class="logout-link"><a href="logout.php"><img src="../assets/imgs/logout.svg" class="icon" alt=""><span>Logout</span></a></li>
    </ul>  
  </aside>
<div id="wrapper">
  <header>
    <div class="header-left">
      <button class="side-btn"><img src="../assets/imgs/menu-hamburger-nav-svgrepo-com (1).svg" id="togglesidebar" class="side-btn-icon"></button>
      <!-- GLOBAL FORM -->
      <form action="layout.php" method="GET" class='search-form'>
        <input type="hidden" name="page" value="search">    
        <div class="search-box">
          <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
          <input type="text" name="search" placeholder="Search ...." class="glopal-search-input">
        </div> 
        <button class="btn search-btn" type="submit">Search</button>
      </form>
    </div>
    <div class="right">
      <div>
        <?php include __DIR__.'/../admin_notification/notification_bell.php'?>
      </div>
      <div class="profile-dropdown">
        <button class="profile-btn" id="profileBtn">
          <img src="../assets/imgs/letter-english-a-svgrepo-com.svg" alt="" class="profile-icon">
          <div class="profile-info">
            <strong>Admin</strong>
            <span><?= $user['display_name'] ?></span>
          </div>
          <img src="../assets/imgs/chevron-down-svgrepo-com (1).svg" alt="" class="profile-dropdown-icon">
        </button>
        <div class="profile-menu" id="profileMenu">
          <a href="layout.php?page=account_settings"><img src="../assets/imgs/settings-svgrepo-com.svg" alt="">Account Settings</a>
          <a href="layout.php?page=change_password"><img src="../assets/imgs/password-key-svgrepo-com.svg" alt="">Edit Password</a> 
          <div class="profile-divider"></div>
          <a href="logout.php" class="logout">Logout</a>
        </div>
        
      </div>
      <!-- <a href="layout.php?page=account_settings">Account Settings</a>
      <a href="layout.php?page=change_password">Change Password</a>  -->
  
  </header>

  
  <main>
    <div class="container">
      <?php
      if ($page=='dashboard') require 'dashboard.php';

      if ($page=='students')  require 'students.php';
      if ($page=='add_student')  require 'add_student.php';
      if ($page=='edit_student')  require 'edit_student.php';

      if($page=='teachers')  require 'teachers.php';
      if($page=="add_teacher") require "add_teacher.php"; 
      if($page=="edit_teacher") require "edit_teacher.php"; 

      if($page=="courses") require "courses.php"; 
      if($page=="create_course") require "create_course.php"; 
      if($page=="edit_course") require "edit_course.php"; 
      if($page=="course_details") require "course_details.php";
      
      if($page=="payments") require "payments.php"; 

      if($page=="enrollment") require "enrollment.php";
      
      if($page=='teachers_attendance_report') require "teachers_attendance_report.php";
      if($page=='students_attendance_report') require "students_attendance_report.php";
      if($page=='student_attendance_history') require "student_attendance_history.php";

      if($page=="search") require "search.php";
      if($page=='account_settings') require "account_settings.php";
      if($page=='change_password') require "change_password.php";

      if($page=='users_management') require "users_management.php";
      if($page=='edit_user') require "edit_user.php";
      if($page=='add_user') require "add_user.php";
      ?>
    </div>
  </main>
  <footer>
    &copy; 2026 S7L University System.All rights reserved.  
  </footer>
</div>
</body>
</html>

<script>
  // document.addEventListener('DOMContentLoaded',function(){
  // const btn=document.getElementById('togglesidebar');
  // const sidebar=document.getElementById('sidebar');
  // const wrapper=document.getElementById('wrapper');
  // if(!btn || !sidebar) return;
  
  // btn.addEventListener('click',function(){
  //   if(window.innerWidth<=768){
  //     sidebar.classList.toggle('open');
  //   }else{
  //     sidebar.classList.toggle('collapsed');
  //   }
  // });
  //   });
  const sidebar=document.getElementById('sidebar');
  const wrapper=document.getElementById('wrapper');

  const body=document.body;

  document.getElementById('togglesidebar').onclick=()=>{
    sidebar.classList.toggle('collapsed');
    body.classList.toggle('sidebar-collapsed');
    wrapper.classList.toggle('expand');

  }
  
  const profileBtn=document.getElementById('profileBtn');
  const profileMenu=document.getElementById('profileMenu');
  profileBtn.addEventListener('click',function(e){
    e.stopPropagation();
    profileMenu.classList.toggle("show");
  });
  document.addEventListener('click',function(){
    profileMenu.classList.remove("show")
  });
</script>
</body>
</html>