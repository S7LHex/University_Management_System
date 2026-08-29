<?php
require_once __DIR__."/../config/db.php";

class GradeRepository{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }
  public function saveGrade($student_id,$course_id,$grade){
    $sql="insert into grades (student_id,course_id,grade) values (?,?,?)
    on duplicate key update grade=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$student_id,$course_id,$grade,$grade]);
  }

  public function deleteGrade($student_id,$course_id){
    $sql="delete from grades where student_id=? and course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$student_id,$course_id]);
  }

  public function getGrade($student_id,$course_id){
    $sql="select grade from grades where student_id=? and course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$student_id,$course_id]);
    return $stmt->fetchColumn();
  }
}


?>