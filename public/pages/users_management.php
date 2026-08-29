<?php
require_once __DIR__."/../../helpers/auth.php";
requireRole(['admin']);

require_once __DIR__. "/../../config/db.php";
require_once __DIR__. "/../../repositories/user_repositories.php";
require_once __DIR__. "/../../repositories/paginator.php";
// require_once __DIR__. "/../../repositories/student_repository.php";
// require_once __DIR__. "/../../repositories/teacher_repository.php";
// require_once __DIR__. "/../actions/register_handler.php";

$pdo= new Database;
$conn= $pdo->connect();
$userRepo= new User($conn);



$filtters=[
  'role' => $_GET['role'] ?? null,
];

$page=isset($_GET['p'])? (int)$_GET['p']:1;
$perPage=5;
$totalItems=$userRepo->countAllUsers(array_filter($filtters));
$paginator= new Paginator($page,$perPage,$totalItems);

$users=$userRepo->getUsers(array_filter($filtters),$paginator->perPage,$paginator->offset);

// $studentRepo=new StudentRepository($conn);
// $teacherRepo=new TeacherRepository($conn);

// $students=$studentRepo->getAllStudents();
// $teachers=$teacherRepo->getAllTeachers();

?>
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

<link rel="stylesheet" href="../assets/css/users_management.css"> 

<h2>Users Management</h2>
<nav class="breadcrumb">
  <a href="layout.php?page=dashboard"><img src="../assets/imgs/home-1-svgrepo-com.svg" alt=""> Dashboard</a>
  <span>/</span>
  <span class="active">Users Management</span>
</nav>

<div class="card">
  
  <!-- SERACH USER -->
  <div class="table-header">

    <form action="" method="GET" class='search-form'>
      <input type="hidden" name="page" value="users_management">    
        <div class="search-box">
          <img src="../assets/imgs/search-alt-1-svgrepo-com.svg" alt="" class="search-icon">
          <input type="text" name="search" placeholder="Search User">
        </div> 
        <button class="btn search-btn">Search</button>
    </form> 
    <a href="layout.php?page=add_user" class="btn add-btn">+ Add User</a>

  </div>

  <form action="">
    <input type="hidden" name="page" value="users_management">
    <div class="filter-group">
      <label for="">Role</label>
      <select name="role" id="" onchange="this.form.submit()">
        <option value="">Select Role</option>
        <option value="admin" <?= ($_GET['role']??'')==='admin'?'selected':'' ?>>Admin</option>
        <option value="teacher" <?= ($_GET['role']??'')==='teacher'?'selected':'' ?>>Teacher</option>
        <option value="student" <?= ($_GET['student']??'')==='student'?'selected':'' ?>>Student</option>
      </select>
    </div>
  </form>

  <?php if(isset($_SESSION['error'])):?>
    <div class="alert-msg error-msg"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error'])?>
  <?php endif; ?>

  <?php if(isset($_SESSION['success'])):?>
    <div class="alert-msg success-msg"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success'])?>
  <?php endif; ?>
  
  <div class="table-container">
    <table border=1>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
        <tbody>
          <?php if(empty($users)):?>
            <tr>
              <td colspan="4" class="msg-td">No users found.</td>
            </tr>
          <?php else:
            foreach($users as $user):?>
            <tr>
              <td><?= htmlspecialchars($user['display_name']) ?></td>
              <td><?= htmlspecialchars($user['email']) ?></td>
              <td><?= htmlspecialchars($user['role']) ?></td>

              <td class="action-td">
                <a href="layout.php?page=edit_user&user_id=<?= $user['id'] ?>" class="btn edit-btn"><img src="../assets/imgs/edit-interface-icon-svgrepo-com.svg" class="btn-icon">Edit</a>
                <form action="../actions/user_handler.php" method="POST">
                  <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                  <button class="btn delete-btn" name='delete' onclick="return confirm('Delete this user?')"><img src="../assets/imgs/delete-2-svgrepo-com.svg" alt="" class="btn-icon">Delete</button>
                </form>
              </td>

            </tr>
            <?php endforeach;?>
          <?php endif;?>
        </tbody>
    </table>
  </div>

<!-- PAGINATION -->
  <?php if($paginator->totalPages > 1):?>
    <div class="pagination">

    <p>Showing <?= $paginator->currentPage ?> of <?= $paginator->totalPages  ?> pages.</p>

    <div class="pagination-links">
      <?php 
        $window=1;
        $startPage= max(1,$paginator->currentPage - $window);
        $endPage= min ($paginator->totalPages , $paginator->currentPage+$window);  
      ?>
      <!-- first-page btn -->
      <?php if($paginator->currentPage > 1):?>
        <a href="layout.php?page=users_management&p=1&<?= http_build_query(array_filter($filtters)) ?>" class='move-link'>1</a>
        <span>....</span>
      <?php endif;?>

      <!-- window -->
      <?php for($i=$startPage ; $i <=$endPage ;$i++):
        $active=($i===$paginator->currentPage)?'active':'';
      ?>
        <a href="layout.php?page=users_management&p=<?= $i ?>&<?= http_build_query(array_filter($filtters)) ?>" class="<?= $active ?>"><?= $i ?></a>
      <?php endfor;?>

      <!-- last_page btn -->
      <?php if($paginator->currentPage<$paginator->totalPages):?>
        <span>...</span>
        <a href="layout.php?page=users_management&p=<?= $paginator->totalPages ?>&<?= http_build_query(array_filter($filtters)) ?>" class='move-link'><?= $paginator->totalPages ?></a>
      <?php endif;?>
      </div>
    </div>
    <?php endif; ?>
    </div>
</div>
























<!-- <h2>Add User</h2>

<?php if(isset($error)):?>
  <div>
    <p class="error-msg"><?= htmlspecialchars($error) ?></p>
  </div>
<?php endif;?>

<?php if(isset($success)):?>
  <div>
    <p class="success-msg"><?= htmlspecialchars($success) ?></p>
  </div>
<?php endif;?>

<form action="" method="POST" >
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>

  <select name="role" id="role" onchange="toggleRelated()">
    <option value="">Select Role</option>
    <option value="admin">Admin</option> 
    <option value="teacher">Teacher</option>
    <option value="student">Student</option>
  </select>

  <input type="hidden" name="related_id" id="related_id_input" value="">
  <select  id="teacher_select" style="display:none" onchange="updateRelatedId(this.value)">
    <option value="">Select Teacher</option>
  
  </select>
  <button name='register' type="submit" class="btn">Register</button>
</form>
<a href="layout.php" class="back-link">BacK to Dashboard</a>
</div> -->

<!-- <script>
  $('#student_select').select2({
      placeholder: 'Search by student name...',
      allowClear: true,
      width: '60%',
  });
  function toggleRelated(){
    const role=document.getElementById('role').value;
    document.getElementById("teacher_select").style.display=(role==='teacher')?'block':'none';
    // document.getElementById("student_select").style.display=(role==='student')?'block':'none';

    document.getElementById('related_id_input').value='';
    document.getElementById('teacher_select').value='';
    document.getElementById('student_select').value='';
  }
  function updateRelatedId(value){
    document.getElementById('related_id_input').value=value;
  }
</script> -->