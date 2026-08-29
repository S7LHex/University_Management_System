<?php
require_once __DIR__."/../config/db.php";

class AttendanceRecord{
  private PDO $pdo;
  private const VALID_STATUS=['present','absent','late','excused'];

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function saveBulk(int $session_id,array $studentStatus): bool {
    $sql="insert into attendance_records (session_id,student_id,status,notes)
    values(:session_id,:student_id,:status,:notes)
    on duplicate key update status=values(status), notes=values(notes) ";
    $stmt=$this->pdo->prepare($sql);
    $this->pdo->beginTransaction();
    try{
      foreach($studentStatus as $studentId=>$data){
        $status=in_array($data['status']??'',self::VALID_STATUS,true)
        ?$data['status']
        :'absent';
        $notes=$data['notes']??null;
        $stmt->execute([
          ':session_id'=>$session_id,
          ':student_id'=>$studentId,
          ':status'=>$status,
          ':notes'=>$notes
        ]);
      }
      $this->pdo->commit();
      return true;
    }
    catch(Exception $e){
      $this->pdo->rollBack();
      throw $e;
    }
  }
  // حضور كل الطلاب جلسة معينة
  public function getBySession(int $sessionId):array{
    $sql="select r.student_id , r.status ,r.notes,
    st.first_name , st.last_name
    from attendance_records r
    join students st on st.id=r.student_id
    where r.session_id=:sessionId
    order by st.first_name,st.last_name";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([':sessionId'=>$sessionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  // سجل حضور طالب 
  public function getStudentHistory(int $studentId,?int $course_id=null):array{
    $sql="select s.session_date , s.course_id ,
    c.name as course_name ,r.status ,r.notes
    from attendance_records r
    join attendance_sessions s on s.id=r.session_id
    join courses c on c.id=s.course_id
    where  r.student_id=:student_id ";

    $params=[':student_id'=>$studentId];
    if($course_id!==null){
      $sql .=' and s.course_id=:course_id ';
      $params[':course_id']=$course_id;
    }
    $sql .=" order by s.session_date desc ";

    $stmt=$this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);   
  }

  public function getCourseState(int $courseId,int $limit,int $offset): array{
    $sql="select st.id as student_id ,st.first_name,st.last_name,
    count(r.id) as total_sessions,
    sum(case when r.status='present' then 1 else 0 end) as present_count,
    sum(case when r.status='absent' then 1 else 0 end) as absent_count,
    sum(case when r.status='late' then 1 else 0 end) as late_count,
    sum(case when r.status='excused' then 1 else 0 end)as excused_count
    from enrollments e
    join students st on st.id=e.student_id
    left join attendance_sessions s on s.course_id=e.course_id
    left join attendance_records r on r.session_id=s.id and r.student_id=st.id
    where e.course_id=?
    group by st.id , st.first_name,st.last_name
    order by st.first_name , st.last_name limit ? offset ?";

    $stmt=$this->pdo->prepare($sql);
    $stmt->bindValue(1,$courseId,PDO::PARAM_INT);
    $stmt->bindValue(2,$limit,PDO::PARAM_INT);
    $stmt->bindValue(3,$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countCourseStudents(int $courseId){
    $sql="select count(*) from enrollments where course_id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$courseId]);
    return $stmt->fetchColumn();
  }

   public function getAllCourseState(int $courseId): array{
    $sql="select st.id as student_id ,st.first_name,st.last_name,
    count(r.id) as total_sessions,
    sum(case when r.status='present' then 1 else 0 end) as present_count,
    sum(case when r.status='absent' then 1 else 0 end) as absent_count,
    sum(case when r.status='late' then 1 else 0 end) as late_count,
    sum(case when r.status='excused' then 1 else 0 end)as excused_count
    from enrollments e
    join students st on st.id=e.student_id
    left join attendance_sessions s on s.course_id=e.course_id
    left join attendance_records r on r.session_id=s.id and r.student_id=st.id
    where e.course_id=?
    group by st.id , st.first_name,st.last_name
    order by st.first_name , st.last_name";

    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$courseId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}



?>