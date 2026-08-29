<?php
require_once __DIR__."/../config/db.php";

class GlobalSearch{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function globalSearch($keyWord){
    $keyWord="%$keyWord%";

    $results=[
      'students'=>[],
      'teachers'=>[],
      'courses'=>[]
    ];
// =================STYDENTS===============================
    $sql="select * from students where 
    first_name like?
    or last_name like?
    or email like?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$keyWord,$keyWord,$keyWord]);

    $results['students']=$stmt->fetchAll(PDO::FETCH_ASSOC);
// =============TEACHERS=============================================
    $sql="select * from teachers where 
    first_name like ?
    or last_name like ?
    or email like ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$keyWord,$keyWord,$keyWord]);
    $results['teachers']=$stmt->fetchAll(PDO::FETCH_ASSOC);

// ===========COURSES=====================================================
    $sql='select name as course_name, code from courses where 
    name like ? or code like ?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$keyWord,$keyWord]);
    $results['courses']=$stmt->fetchAll(PDO::FETCH_ASSOC);

    return $results;
  }
}








?>