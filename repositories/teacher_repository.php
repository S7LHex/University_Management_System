<?php
require_once __DIR__."/../config/db.php";

class TeacherRepository{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }
  
  public function addTeacher($first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email){
    $sql="insert into teachers (first_name,last_name,father_name,age,address,mobile_number,major,salary,email) values (?,?,?,?,?,?,?,?,?)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email]);
  }

  public function getTeachers(int $limit,int $offset){
    $sql="select * from teachers limit ? offset ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->bindValue(1,$limit,PDO::PARAM_INT);
    $stmt->bindvalue(2,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAllTeachers(){
    $stmt=$this->pdo->query('select * from teachers');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function countTeachers(){
    $sql="select count(*) from teachers";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn();
  }
  
  public function deleteTeacher($id){
    $sql= "delete from teachers where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }
  public function editTeacher($id,$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email){
    $sql='update teachers set first_name=?,last_name=?,father_name=?,age=?,address=?,mobile_number=?,major=?,salary=?,email=? where id=?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$salary,$email,$id]);
  }

  public function findTeacherById($id){
    $sql='select * from teachers where id=?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  public function searchTeacher($keyword){
    $sql='select * from teachers where first_name like ?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute(["%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function getTeacherCourse($teacherId,$courseId){
    $sql="select ct.course_id,c.id,c.name as course_name ,count(e.student_id)as students_count
    from course_teachers ct
    left join courses c on c.id=ct.course_id
    left join enrollments e on e.course_id=c.id
    where ct.teacher_id=? and ct.course_id=?
    group by c.id,c.name";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$teacherId,$courseId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getTeacherCourses($teacherId){
    $sql="select ct.course_id,c.id,c.name as course_name ,count(e.student_id)as students_count
    from course_teachers ct
    left join courses c on c.id=ct.course_id
    left join enrollments e on e.course_id=c.id
    where ct.teacher_id=?
    group by c.id,c.name";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$teacherId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);    
  }
  public function getCourseStudentsForTeacher($courseId,$teacherId){
  $sql="select s.id,s.first_name,s.last_name,s.email 
  from enrollments e
  join students s on e.student_id=s.id
  join course_teachers ct on e.course_id=ct.course_id
  where e.course_id=? and ct.teacher_id=?";
  $stmt=$this->pdo->prepare($sql);
  $stmt->execute([$courseId,$teacherId]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}

?>