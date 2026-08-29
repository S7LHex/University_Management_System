<?php
require_once __DIR__ ."/../config/db.php";
require_once __DIR__ ."/course_repository.php";
require_once __DIR__ ."/paymentRepository.php";
require_once __DIR__ ."/notification.php";

class Enrollment{
  private PDO $pdo;  
  private PaymentRepository $paymentRepo;
  private Notification $notificationRepo;

  public function __construct(PDO $pdo , PaymentRepository $paymentRepo,Notification $notificationRepo)
  {
    $this->pdo=$pdo;
    $this->paymentRepo = $paymentRepo;
    $this->notificationRepo = $notificationRepo;
  }

  public function enrollStudent($studentId,$courseId,$numInstallments=1){
    $stmt=$this->pdo->prepare('select id,price from courses where id=?');
    $stmt->execute([$courseId]);
    $course=$stmt->fetch(PDO::FETCH_ASSOC);
    if(!$course){
      return "Course does not exist";
    }

    $stmt=$this->pdo->prepare('select id from students where id=?');
    $stmt->execute([$studentId]);
    if(!$stmt->fetch()){
      return "Student does not exist";
    }

    $sql="select id from enrollments where student_id=? and course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$studentId,$courseId]);
    if($stmt->fetch(PDO::FETCH_ASSOC)){
      return "The student already enrolled in this course.";
    }

    $this->pdo->beginTransaction();
    try{
      
      $sql="insert into enrollments (student_id,course_id) values (?,?)";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$studentId,$courseId]);
      $enrollmentId=(int)$this->pdo->lastInsertId();

      $installmentsCreated=$this->paymentRepo->createInstallments($enrollmentId,$studentId,$courseId,$course['price'],$numInstallments);
      if(!$installmentsCreated){
        $this->pdo->rollBack();
        return "Cannot enroll: course price is not set.";
      }
      $this->pdo->commit();
  
    }catch(Exception $e){
      $this->pdo->rollBack();
      throw $e;
    }
    // *********ENROLLMENT NOTIFICATION************************************************************************************************************
      $sql="select first_name, last_name from students where id=?";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$studentId]);
      $student=$stmt->fetch(PDO::FETCH_ASSOC);

      $sql="select name from courses where id=?";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$courseId]);
      $course=$stmt->fetch(PDO::FETCH_ASSOC);

      $message="New Enrollment:". $student['first_name'] .' '.$student['last_name'].' in the '. $course['name'].' course.';
      $this->notificationRepo->notifyAllAdmins($message,'new_enrollment',null,$_SESSION['user_id']);
      return "Student has been enrolled successfully.";
  }
  public function getStudentCourses($studentId){
    $sql="select e.* ,c.name 
    from enrollments e join courses c on c.id=e.course_id
    join students s on s.id=e.student_id 
    where s.id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$studentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
  }
  public function getCourseStudents($course_id){
    $sql="select s.* 
    from enrollments e 
    join students s on s.id=e.student_id
    where e.course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getEnrollments(int $limit,int $offset){
    $sql="select 
    e.id ,
    e.enrollment_date ,
    s.first_name , s.last_name,
    c.name as course_name,
    c.price,
    e.student_id,
    e.course_id,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id) as total_installments,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id and p.status='paid') as paid_installments
    from enrollments e
    join students s on s.id=e.student_id
    join courses c on c.id=e.course_id
    order by e.id desc limit ? offset ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->bindValue(1,$limit,PDO::PARAM_INT);
    $stmt->bindValue(2,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function enrollmentsCount(){
    $sql="select count(*) from enrollments e
    join students s on s.id=e.student_id
    join courses c on c.id=e.course_id ";
    $stmt=$this->pdo->query($sql);
    return (int) $stmt->fetchColumn();
  }
  public function getAllEnrollments(){
    $sql="select 
    e.id ,
    e.enrollment_date,
    s.first_name , s.last_name,
    c.name as course_name,
    c.price,
    e.student_id,
    e.course_id,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id) as total_installments,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id and p.status='paid') as paid_installments
    from enrollments e
    join students s on s.id=e.student_id
    join courses c on c.id=e.course_id
    order by e.id desc";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function searchEnrollment($keyWord){
    $sql="select
    e.*,
    s.first_name,
    s.last_name,
    c.name as course_name,
    c.price,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id) as total_installments,
    (select count(*) from payments p where p.student_id=e.student_id and p.course_id=e.course_id and p.status='paid') as paid_installments
    from enrollments e
    join students s on s.id=e.student_id
    join courses c on c.id=e.course_id
    where s.first_name like ?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute(["%$keyWord%"]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  public function getCourseStudent($course_id,$student_id){
    $sql="select  s.*
    from enrollments e
    join students s on e.student_id=s.id
    where e.course_id=? and e.student_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id,$student_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  public function CountCourseStudent($course_id){
    $sql="select  count(*)
    from enrollments e
    where course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$course_id]);
    return $stmt->fetchColumn();
  }

  public function unenrollStudent($studentId,$courseId){

  $stmt=$this->pdo->prepare('select start_date , end_date from courses where id=?');
  $stmt->execute([$courseId]);
  $course=$stmt->fetch(PDO::FETCH_ASSOC);
  if(! $course){
    return 'Course not found';
  }

  $start=strtotime($course['start_date']);
  $end=strtotime($course['end_date']);

  if(! $course['end_date'] || $end <= $start){
    $isBeforMidpoint=false;
  }else{
    $midPoint=$start + (($end - $start) / 2);
    $isBeforMidpoint= time() < $midPoint;
  }

  $this->pdo->beginTransaction();
  try{
      $stmt=$this->pdo->prepare('select id from enrollments where student_id=? and course_id=?');
      $stmt->execute([$studentId,$courseId]);
      $enrollment=$stmt->fetch(PDO::FETCH_ASSOC);
      if(!$enrollment){
        $this->pdo->rollBack();
        return "Enrollment not found.";
      }
      $enrollmentId=(int)$enrollment['id'];
      $this->paymentRepo->processRefundOnUnenroll($enrollmentId,$isBeforMidpoint);

      $stmt=$this->pdo->prepare('update payments set enrollment_id=null where enrollment_id=?');
      $stmt->execute([$enrollmentId]);
  
      $sql="delete from enrollments where id=?";
      // $sql="update enrollments set status='cancelled' where id=?";
      $stmt=$this->pdo->prepare($sql);
      // $stmt->execute([$studentId,$courseId]);
      $stmt->execute([$enrollmentId]);
      if ($stmt->rowCount()===0){
        $this->pdo->rollBack();
        return "Enrollment not found";
      }
      $this->pdo->commit();

      // NOTIFICATION
      $sql="select first_name, last_name from students where id=?";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$studentId]);
      $student=$stmt->fetch(PDO::FETCH_ASSOC);

      $sql="select name from courses where id=?";
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$courseId]);
      $course=$stmt->fetch(PDO::FETCH_ASSOC);

      $refundNote= $isBeforMidpoint?'(The paid installment has been refunded)' : "(Without Refund)";
      $message="Cancel Enrollment: ". $student['first_name'].' '. $student['last_name'].' in the '. $course['name'].' course '. $refundNote;
      $this->notificationRepo->notifyAllAdmins($message,'unenrollment',null,$_SESSION['user_id']);
      return "Unenrolled successfully" ;
    }
    catch (Exception $e){
      if($this->pdo->inTransaction()){
        $this->pdo->rollBack();
      }
      throw $e;
    }

  }
}

?>



