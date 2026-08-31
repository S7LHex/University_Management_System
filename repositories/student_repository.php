<?php
require_once __DIR__. "/../config/db.php"; 

class StudentRepository{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo = $pdo;
  }

  public function addStudent($first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email){
    $sql="insert into students (first_name,last_name,father_name,age,address,mobile_number,major,email) values (?,?,?,?,?,?,?,?)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email]);
  }

  public function editStudent($id,$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email){
    $sql= "update students set first_name=?,last_name=?,father_name=?,age=?,address=?,mobile_number=?,major=?,email=? where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$first_name,$last_name,$father_name,$age,$address,$mobile_number,$major,$email,$id]);
  }

  public function deleteStudent($id){
    $sql="delete from students where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);
  }

  public function findStudentById($id){
    $sql="select * from students where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  public function getStudents(int $limit , int $offset){
    $sql="select * from students limit ? offset ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->bindValue(1,$limit,PDO::PARAM_INT);
    $stmt->bindValue(2,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function countStudents(){
    $sql="select count(*) from students";
    $stmt=$this->pdo->query($sql);
    return  (int) $stmt->fetchColumn();
  }
  public function getAllStudents(){
    $sql="select * from students ";
    $stmt=$this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }


  public function searchStudents($keyword){
    $sql='select * from students where first_name like ? or id like ?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute(["%$keyword%","%$keyword%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function getStudentCourses($studentId){
    $sql="select 
    c.id,c.name as course_name,
    c.code,
    group_concat(distinct concat(t.first_name,' ',t.last_name) separator ' , ') as teachers,
    e.enrollment_date,
    g.grade
    from enrollments e
    join courses c on e.course_id=c.id
    left join course_teachers ct on c.id=ct.course_id
    left join teachers t on t.id=ct.teacher_id
    left join grades g on g.course_id=e.course_id and g.student_id=e.student_id
    where e.student_id=?
    group by c.id , c.name ,c.code,e.enrollment_date,g.grade";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}


?>
