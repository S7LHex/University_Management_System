<?php
require_once __DIR__."/../config/db.php";

class TeacherAttendance{
  private PDO $pdo;
  private const DAY_MAP=[
    0=>'sun',
    1=>'mon',
    2=>'tue',
    3=>'wed',
    4=>'thu',
    5=>'fri',
    6=>'sat',
  ];

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function getTodayCourses(int $teacher_id) :array{
    $todayName=self::DAY_MAP[date('w')];
    $today=date('Y-m-d');

    $sql="select c.*
    from courses c
    join course_teachers ct on ct.course_id=c.id
    where ct.teacher_id=:teacher_id
    and c.days like :day_pattern
    and :today between c.start_date and coalesce(c.postpone_date,c.end_date)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([
      ':teacher_id'=>$teacher_id,
      ':day_pattern'=>'%'.$todayName.'%',
      ':today'=>$today]);
    $courses= $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($courses as &$course){
      $course['checked_in_today']=$this->hasCheckedInToday($teacher_id,$course['id']);
    }
    unset($course);
    return $courses;
  }

  public function hasCheckedInToday(int $teacherId,int $courseId):bool{
    $sql="select id from teacher_attendance where teacher_id=:teacher_id and course_id=:course_id and session_date=curdate() limit 1";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([
      ':teacher_id'=>$teacherId,
      ':course_id'=>$courseId,
    ]);
    return (bool) $stmt->fetch(PDO::FETCH_ASSOC); 
  }

  public function checkIn(int $teacherId, int $courseId,int $userId){
    if($this->hasCheckedInToday($teacherId,$courseId)){
      return 0;
    }
    $courseStmt=$this->pdo->prepare('select start_time from courses where id=?');
    $courseStmt->execute([$courseId]);
    $course=$courseStmt->fetch(PDO::FETCH_ASSOC);

    $status='present';
    if($course && $course['start_time']){
      $startTime=strtotime($course['start_time']);
      $now=strtotime(date('H:i:s'));
      if($now >$startTime + (15*60)){
        $status='late';
      }
    }
    $stmt=$this->pdo->prepare(
      'insert into teacher_attendance(teacher_id,course_id,session_date,check_in_time,status,recorded_by)
      values(?,?,curdate(),CURTIME(),?,?)'
    );
    $stmt->execute([$teacherId,$courseId,$status,$userId]);
    return $this->pdo->lastInsertId();
  }
  public function getByTeacher($teacherId){
    $sql="select ta.*,c.name as course_name
    from teacher_attendance ta
    join courses c on c.id=ta.course_id
    where ta.teacher_id=?
    order by ta.session_date desc";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$teacherId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getAll($limit,$offset,$filters=[]){
    $sql="select ta.*, t.first_name,t.last_name,c.name as course_name
    from teacher_attendance ta
    join teachers t on t.id=ta.teacher_id
    join courses c on c.id=ta.course_id
    where 1=1 ";
    $params=[];
    if(!empty($filters['teacher_id'])){
      $sql .=" and ta.teacher_id=:teacher_id ";
      $params[':teacher_id']=$filters['teacher_id'];
    }
    if(!empty($filters['course_id'])){
      $sql .=" and ta.course_id=:course_id ";
      $params[':course_id']=$filters['course_id'];
    }
    if(!empty($filters['date_from'])){
      $sql .=" and ta.session_date >=:date_from ";
      $params[':date_from']=$filters['date_from'];
    }
    if(!empty($filters['date_to'])){
      $sql .=" and ta.session_date <=:date_to ";
      $params[':date_to']=$filters['date_to'];
    }
    $sql .="  order by ta.session_date desc , ta.check_in_time desc limit :limit  offset :offset  ";
    $stmt=$this->pdo->prepare($sql);

    foreach($params as $key=>$value){
      $stmt->bindValue($key,$value);
    }
    $stmt->bindValue(':limit',(int)$limit,PDO::PARAM_INT);
    $stmt->bindValue(':offset',(int)$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function attendanceCount($filters=[]){
    $sql="select count(*) from teacher_attendance ta 
    join teachers t on t.id=ta.teacher_id
    join courses c on c.id=ta.course_id
    where 1=1 ";
    $params=[];

    if(!empty($filters['teacher_id'])){
      $sql .=" and ta.teacher_id=:teacher_id";
      $params[':teacher_id'] =$filters['teacher_id'];
    }
    if (!empty($filters['course_id'])) {
        $sql .= " and ta.course_id = :course_id ";
        $params[':course_id'] = $filters['course_id'];
    }
    if (!empty($filters['date_from'])) {
        $sql .= " and ta.session_date >= :date_from ";
        $params[':date_from'] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
        $sql .= " and ta.session_date <= :date_to ";
        $params[':date_to'] = $filters['date_to'];
    }

    $stmt=$this->pdo->prepare($sql);
    foreach($params as $key=>$value){
      $stmt->bindValue($key,$value);
    }
    $stmt->execute();
    return (int)$stmt->fetchColumn();
  }

  public function getAllAttendance($filters=[]){
    $sql="select ta.*, t.first_name,t.last_name,c.name as course_name
    from teacher_attendance ta
    join teachers t on t.id=ta.teacher_id
    join courses c on c.id=ta.course_id
    where 1=1 ";
    $params=[];
    if(!empty($filters['teacher_id'])){
      $sql .=" and ta.teacher_id=:teacher_id ";
      $params[':teacher_id']=$filters['teacher_id'];
    }
    if(!empty($filters['course_id'])){
      $sql .=" and ta.course_id=:course_id ";
      $params[':course_id']=$filters['course_id'];
    }
    if(!empty($filters['date_from'])){
      $sql .=" and ta.session_date >=:date_from ";
      $params[':date_from']=$filters['date_from'];
    }
    if(!empty($filters['date_to'])){
      $sql .=" and ta.session_date <=:date_to ";
      $params[':date_to']=$filters['date_to'];
    }
    $sql .="  order by ta.session_date desc , ta.check_in_time desc ";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

?>




