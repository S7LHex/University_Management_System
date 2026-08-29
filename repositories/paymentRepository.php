<?php
require_once __DIR__."/../config/db.php";

class PaymentRepository{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function createInstallments($enrollment_id,$student_id,$course_id,$totalPrice,$numInstallments){
    
    if($totalPrice === null){
      return false;
    }
    if($totalPrice == 0){
      return true;
    }
    if($numInstallments < 1){
      $numInstallments=1;
    }
    $baseAmount= floor(($totalPrice/$numInstallments) * 100)/100;
    $lastAmount=round($totalPrice - ($baseAmount * ($numInstallments -1)),2);

    $sql="insert into payments (enrollment_id,student_id,course_id,installment_number,amount,due_date,status) values (?,?,?,?,?,?,'unpaid')";
    $stmt=$this->pdo->prepare($sql);

    for($i=1;$i<=$numInstallments;$i++){
      $amount=($i ===$numInstallments)?$lastAmount : $baseAmount;
      $dueDate=date('Y-m-d',strtotime('+'.($i-1).'months'));
      $stmt->execute([$enrollment_id,$student_id,$course_id,$i,$amount,$dueDate]);
    }
    return true;
  }

  public function getPaymentById($paymentId){
    $stmt=$this->pdo->prepare('select * from payments where id=?');
    $stmt->execute([$paymentId]);
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }
  public function getAllPayments(array $filters,int $limit,int $offset):array{
    $sql="select p.*,s.first_name,s.last_name,c.name as course_name,c.price
    from payments p
    join students s on s.id=p.student_id
    join courses c on c.id=p.course_id
    where 1=1  " ;

    $params=[];

    if(!empty($filters['status'])){
      $sql .=" and p.status= :status ";
      $params[':status'] = $filters['status'];
    }
    if(!empty($filters['student_id'])){
      $sql .=" and p.student_id=:student_id ";
      $params[':student_id']=$filters['student_id'];
    }
    if(!empty($filters['course_id'])){
      $sql .=" and p.course_id=:course_id ";
      $params[':course_id']=$filters['course_id'];
    }

    $sql.=" order by p.due_date desc limit :limit offset :offset " ;
    $stmt=$this->pdo->prepare($sql);
    foreach($params as $key =>$value){
      $stmt->bindValue($key,$value);
    }
    $stmt->bindValue(':limit',$limit,PDO::PARAM_INT);
    $stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function countAllPayments(array $filters):int{
  $sql="select count(*) from payments p where 1=1 ";
  $params=[];
  if(!empty($filters['status'])){
    $sql .=" and p.status= :status ";
    $params[':status'] = $filters['status'];
  }
  if(!empty($filters['student_id'])){
    $sql .=" and p.student_id = :student_id ";
    $params[':student_id']=$filters['student_id'];
  }
  if(!empty($filters['course_id'])){
    $sql .=" and p.course_id= :course_id ";
    $params[':course_id']=$filters['course_id'];
  }
  $stmt=$this->pdo->prepare($sql);
  $stmt->execute($params);
  return (int) $stmt->fetchColumn();
}

public function getPayments( array $filters){
  $sql="select p.*,s.first_name,s.last_name,c.name as course_name,c.price
    from payments p
    join students s on s.id=p.student_id
    join courses c on c.id=p.course_id
    where 1=1  " ;

    $params=[];

    if(!empty($filters['status'])){
      $sql .=" and p.status= :status ";
      $params[':status'] = $filters['status'];
    }
    if(!empty($filters['student_id'])){
      $sql .=" and p.student_id=:student_id ";
      $params[':student_id']=$filters['student_id'];
    }
    if(!empty($filters['course_id'])){
      $sql .=" and p.course_id=:course_id ";
      $params[':course_id']=$filters['course_id'];
    }

    $sql.=" order by p.due_date desc " ;
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}


public function getPaymentsSummary(){
  $sql="select 
  sum(case when status='paid' then amount else 0 end) as total_paid, 
  sum(case when status='unpaid' then amount else 0 end) as total_unpaid,
  sum(case when status='refunded' then amount else 0 end) as total_refunded
  from payments";
  $stmt=$this->pdo->query($sql);
  return $stmt->fetch(PDO :: FETCH_ASSOC);
}

  public function getStudentPayments($student_id){
    $sql="select p.*,c.name as course_name
    from payments p
    join courses c on c.id=p.course_id
    where p.student_id=? order by p.due_date asc";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$student_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function getUnpaidInstallments(){
    $sql="select p.*,s.first_name,s.last_name,c.name as course_name,c.price 
    from payments p
    join students s on s.id=p.student_id
    join courses c on c.id=p.course_id
    where p.status='unpaid'
    order by p.due_date asc";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function getCoursePayments($course_id){
    $sql="select p.*,c.name as course_name,s.first_name as student_name
    from payments p 
    join courses c on p.course_id=c.id
    join students s on p.student_id=s.id
    where p.course_id=?
    order by s.name,p.installment_number";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function markAsPaid($payment_id,$payment_method,$notes){
    $sql="update payments set status=?,payment_date=NOW(),payment_method=?,notes=? where id=? and status='unpaid'";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute(['paid',$payment_method,$notes,$payment_id]);
    return $stmt->rowCount()>0;
  }


  public function deleteInstallment($payment_id){
    $sql="delete from payments where id=? and enrollment_id is null and status='cancelled'";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$payment_id]);
  }

  public function processRefundOnUnenroll(int $enrollmentId,bool $isBeforeMidpoint) : void {
    if($isBeforeMidpoint){
      $sql="update payments set status =? where enrollment_id=? and status='paid' ";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute(['refunded',$enrollmentId]);
    }
    $sql="update payments set status='cancelled' where enrollment_id=? and status='unpaid'";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$enrollmentId]);
  
  }

}

?>

