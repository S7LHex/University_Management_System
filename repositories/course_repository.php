<?php
require_once __DIR__."/../config/db.php";
require_once "teacher_repository.php";

class CourseRepository{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo = $pdo;
  }
  
  public function createCourse($code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$days,$max_students,$price){
    $sql='insert into courses (code,name,start_date,end_date,postpone_date,total_hours,start_time,end_time,days,max_students,price) 
    values (?,?,?,?,?,?,?,?,?,?,?)';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute ([$code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$days,$max_students,$price]);
    return $this->pdo->lastInsertId();
  }
  public function editCourse($code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$days,$max_students,$price,$course_id){
    $sql="update courses set code=?,name=?,start_date=?,end_date=?,postpone_date=?,total_hours=?,start_time=?,end_time=?,days=?,max_students=?,price=? where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$code,$name,$start_date,$end_date,$postpone_date,$total_hours,$start_time,$end_time,$days,$max_students,$price,$course_id]);
  }
  public function updateCourseTeachers($course_id,$teachersId){
    $sql='delete from course_teachers where id=?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);

    foreach($teachersId as $teacherId){
      $this->addTeacherToCourse($course_id,$teacherId);
    }

    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
  }
  public function getCourseBYId($id){
    $sql="select * from courses where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function searchCourse($keyWord){
    $sql="select c.*, t.first_name,t.last_name
    from courses c
    join course_teachers ct on c.id=ct.course_id
    join teachers t on t.id=ct.teacher_id
    where c.name like ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute(["%$keyWord%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getCourses($limit,$offset){
    $sql='select * from Courses limit ? offset ?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->bindValue(1,$limit,PDO::PARAM_INT);
    $stmt->bindValue(2,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function coursesCount(){
    $sql="select count(*) from courses";
    $stmt=$this->pdo->query($sql);
    return $stmt->fetchColumn();
  }
  public function getAllCourses(){
    $sql='select * from Courses';
    $stmt=$this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function getCourseTeachers($course_id){
    $sql="select t.*
    from course_teachers ct
    join teachers t on t.id=ct.teacher_id
    where ct.course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function countCourseTeachers($course_id){
    $sql="select count(*) from course_teachers where course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    return $stmt->fetchColumn();
  }
  public function deleteCourse($id){
    $sql="delete from courses where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }
  public function addTeacherToCourse($course_id,$teacher_id){
    $sql="insert ignore into course_teachers (course_id,teacher_id) values(?,?)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id,$teacher_id]);
  }

  public function enrollStudent($student_id,$course_id,$grade){
    $sql='insert into enrollments (student_id,course_id,grade) values (?,?,?)';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$student_id,$course_id,$grade]);
  }
  // public function searchCourse($keyword){
  //   $sql="select c.*,t.name as teacher_name 
  //   from courses c 
  //   join teachers t on c.teacher_id=t.id
  //   where c.name like ?";
  //   $stmt=$this->pdo->prepare($sql);
  //   $stmt->execute(["%$keyword%"]);
  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }
}

?>