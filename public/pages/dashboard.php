<?php
require_once "../../repositories/dashboard_repository.php";

$dashboardRepo= new DashboardRepository;

$counts=$dashboardRepo->getStats();
$recentEnrollments=$dashboardRepo->getRecentEnrollments();

?>
<div class="cards">

  <div class="card-content students-card">
    <div>
      <p>Students Count <span><?= htmlspecialchars($counts['students_count']) ?></span></p>
    </div>
    <div class="card-icon">
      <img src="../assets/imgs/student-cap-svgrepo-com.svg" alt="">
    </div>
  </div>

  <div class="card-content teachers-card">
    <div>
      <p>Teachers Count <span><?= htmlspecialchars($counts['teachers_count']) ?></span></p>
    </div>
    <div class="card-icon">
      <img src="../assets/imgs/user-svgrepo-com.svg" alt="">
    </div>
  </div>

  <div class="card-content courses-card">
    <div>
      <p>Courses Count <span><?= htmlspecialchars($counts['courses_count']) ?></span></p>
    </div>
    <div class="card-icon">
      <img src="../assets/imgs/book-svgrepo-com.svg" alt="">
    </div>
  </div>

  <div class="card-content enrollments-card">
    <div>
      <p>Enrollments Counts <span><?= htmlspecialchars($counts['enrollments_count']) ?></span></p>
    </div>
    <div class="card-icon">
      <img src="../assets/imgs/clipboard-text-svgrepo-com.svg" alt="">
    </div>
  </div>
</div>

<div class="table-card">
<h2 class="dash-title">Recent Enrollments</h2>
<div class="table-container">
<table border=1 class="dash-table">
  <tr>
    <th>Student</th>
    <th>Courses</th>
    <th>Date</th>
  </tr>
  <?php foreach($recentEnrollments as $enrollment):?>
    <tr>
      <td><?= htmlspecialchars($enrollment['student_name']) ?></td>
      <td><?= htmlspecialchars($enrollment['course_name']) ?></td>
      <td><?= htmlspecialchars($enrollment['enrollment_date'])?></td>
    </tr>
  <?php endforeach;?>
</table>
</div>

<div class="dashboard-links">
  <a href="layout.php?page=add_student" class="student-link"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Add Student</a>
  <a href="layout.php?page=add_teacher" class="teacher-link"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Add Teacher</a>
  <a href="layout.php?page=create_course" class="course-link"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Add Course</a>
  <a href="layout.php?page=enrollment" class="enroll-link"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Enroll Student</a>
  <a href="register.php" class="user-link"><img src="../assets/imgs/add-svgrepo-com.svg" alt="">Register User</a>
</div>

</div>