<?php
require_once __DIR__."/../config/db.php";

class AttendanceSession{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function createOrGetSession($course_id,$teacher_id,$session_date){
    $exists=$this->getByCourseAndDate($course_id,$session_date);
    if($exists){
      return $exists['id'];
    }
    
    $sql="insert into attendance_sessions (course_id,teacher_id,session_date)values(?,?,?)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id,$teacher_id,$session_date]);
    return $this->pdo->lastInsertId();
  }
  public function getByCourseAndDate($course_id,$session_date){
    $sql="select * from attendance_sessions where course_id=? and session_date=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id,$session_date]);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    return $row?:null;
  }
  public function getById($session_id){
    $sql="select * from attendance_sessions where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$session_id]);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    return $row;
  }
  public function getSessionsByCourse($course_id){ 
    $sql="select s.*,
    (select count(*) from attendance_records r where r.session_id=s.id and r.status='present') as present_count,
    (select count(*) from attendance_records r where r.session_id =s.id) as total_count
    from attendance_sessions s
    where s.course_id=? order by s.session_date desc";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    $row=$stmt->fetchAll(PDO::FETCH_ASSOC);
    return $row;
  }
}

?>