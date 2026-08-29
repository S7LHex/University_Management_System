<?php
require_once __DIR__."/../../config/db.php";
require_once __DIR__."/../../repositories/global_search.php";

$pdo = new Database;
$conn=$pdo->connect();
$searchModel= new GlobalSearch($conn);

$keyWord=isset($_GET['search']) ? trim($_GET['search']):'';

// if($keyWord === ''){
//   header('Location:layout.php');
//   exit;
// }

$results=$searchModel->globalSearch($keyWord);
?>
<link rel="stylesheet" href="../assets/css/search.css">

<div class="card">
  <h2 class="search-title"> <img src="../assets/imgs/search-svgrepo-com.svg" alt="" class="title-img"> Search results for : <span>"<?= htmlspecialchars($keyWord) ?>"</span></h2>

  <div class="results-container">

    <div class="result">
      <?php if($results && $results['students']):?>
        <div class="results-links">
        <h3><img src="../assets/imgs/student-cap-svgrepo-com.svg" alt="" class="results-icons">Students</h3>
        <div>
          <?php foreach($results['students'] as $student): ?>
            <a href="layout.php?page=students&search=<?= htmlspecialchars($keyWord) ?>" class="link student-link"><?= htmlspecialchars($student['first_name']." ".$student['last_name']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

    <div class="result">
      <?php if($results && $results['teachers']):?>
      <div class="results-links teachers-results">
        <h3><img src="../assets/imgs/user-svgrepo-com.svg" alt="" class="results-icons">Teachers</h3>
        <div>
          <?php foreach($results['teachers'] as $teacher):?>
            <a href="layout.php?page=teachers&search=<?= htmlspecialchars($keyWord) ?>" class="link teacher-link"><?= htmlspecialchars($teacher['first_name'].' '.$teacher['last_name']) ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    </div>

    <div class="result">
      <?php if($results && $results['courses']):?>
      <div class="results-links course-results">
        <h3><img src="../assets/imgs/book-svgrepo-com.svg" alt="" class="results-icons">Courses</h3>
        <div>
          <?php foreach($results['courses'] as $course):?>
            <a href="layout.php?page=courses&search=<?= htmlspecialchars($keyWord) ?>" class="link course-link"><?= htmlspecialchars($course['course_name']. '('.$course['code'].')') ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      </div>
    </div>

<?php if (empty($results)): ?>
  <p>No results found.</p>
<?php endif; ?>
</div>


</div>

