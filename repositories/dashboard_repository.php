<?php
require_once __DIR__ ."/../config/db.php";

class DashboardRepository{
  private $conn;

  public function __construct(){
    $db = new Database;
    $this->conn=$db->connect();
  }

  public function getStats(){
    $sql="select 
    (select count(*) from students) as students_count,
    (select count(*) from teachers) as teachers_count,
    (select count(*) from courses) as courses_count,
    (select count(*) from enrollments) as enrollments_count ";
    $stmt=$this->conn->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getRecentEnrollments(){
    $sql="select 
    s.first_name as student_name,
    c.name as course_name,
    e.enrollment_date
    from enrollments e
    join students s on s.id=e.student_id
    join courses c on c.id=e.course_id
    order By e.enrollment_date desc
    limit 5 ";

    $stmt=$this->conn->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

?>