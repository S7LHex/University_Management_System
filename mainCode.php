<?php
class Person{
  protected $id;
  protected $name;
  protected $major;
  protected $email;

  public function __construct($id,$name,$major,$email){
    $this->id=$id;
    $this->name=$name;
    $this->major=$major;
    $this->email=$email;
  }
  public function getId(){
    return $this->id;
  }
  public function getName(){
    return $this->name;
  }
  public function getMajor(){
    return $this->major;
  }
  public function getEmail(){
    return $this->email;
  }

  public function showInfo(){
    echo "ID: {$this->id} <br>";
    echo "Name : {$this->name} <br>";
    echo "Major: {$this->major} <br>";
  }
}

class Course{
  protected $code;
  protected $name;
  protected $teacher;
  protected $students=[];
  protected $grades=[];
  protected $price;
  protected $maxStudents;

  public function __construct($code,$name,$price,$maxStudents){
  $this->code=$code;  
  $this->name=$name;
  $this->price=$price;
  $this->maxStudents=$maxStudents;
  }

  public function getCode(){
    return $this->code;
  }
  public function getname(){
    return $this->name;
  }
  public function getteacher(){
    return $this->teacher;
  }
  public function getStudents(){
    return $this->students;
  }
  public function getGrades(){
    return $this->grades;
  }
  public function getPrice(){
    return $this->price;
  }
  public function getMaxStudents(){
    return $this->maxStudents;
  }
  
  public function assignTeacher(Teacher $teacher){
    $this->teacher=$teacher;
  }

  public function enrollStudent(Student $student){
    if($this->isStudentEnrolled($student)){
      echo "Student is already enrolled in this course <br>";
    }
    elseif(count($this->students)>=$this->maxStudents){
      echo "Course is full";
    }else{
    $this->students[]=$student;
    $student->addCourse($this);
    }
  }
  public function isStudentEnrolled(Student $student){
    if (in_array($student,$this->students)){
      return true;
    }else{
      return false;
    }
  }
  public function assignGrades(Student $student,$grade){
    if($this->isStudentEnrolled($student)){
      $this->grades[$student->getId()]=$grade;
    }else{
      echo "Student {$student->getname()}  is not enrolled in  this Course <br>";
    }
  }
  public function getAverageGrades(){
    if (empty($this->grades)){
      echo "There are no grades <br>";
    }else{
    $gradesNumber=count($this->grades);
    $sumGrades=0;
    foreach($this->grades as $grade){
      $sumGrades+=$grade;
    }
    $averageGrades=$sumGrades / $gradesNumber;
    return $averageGrades;
    }
  }

  public function getTopStudent(){
    if(empty($this->grades)){
      return null;
    }
    $topGrade=max($this->grades);
    $topStudentId=array_search($topGrade,$this->grades);
    foreach($this->students as $student){
      if ($student->getId() == $topStudentId){
        return $student;       
          }
        return null;
      }
    }
  public function getStudentGrade(Student $student){
    if($this->isStudentEnrolled($student)){
      foreach($this->grades as $id=>$grade){
        if($student->getId()==$id){
          return $grade;
        }
      }
    }else{
      echo "Student {$student->getName()} is not enrolled in this course <br>";
    }
  }

  public function isPassed(Student $student){
    if($this->getStudentGrade($student)>=50){
      return "Passed";
    }
    else{
      return "Failed";
    }
  }

  public function removeStudent(Student $student){
    if($this->isStudentEnrolled($student)){
      $idToDelete=$student->getId();
      foreach($this->students as $key=>$stu){
        if($stu->getId()==$idToDelete){
          unset($this->students[$key]);
        }
      }
      foreach($this->grades as $key=>$grade){
      if ($key==$idToDelete){
        unset($this->grades[$key]);
        // unset($this->grades[$idToDelete]);
      }
      }
      echo "Student {$student->getName()} removed from course {$this->name} <br>";
    }else{
      echo "Student {$student->getName()} in not enrolled in this course <br>";
    }
  }

  public function showInfo(){
    echo "Code: {$this->code} <br>";
    echo "Name: {$this->name} <br>";
    echo "teacher: {$this->teacher->getName()} <br>";
    echo"<pre>";
    print_r($this->getstudents());
    echo"</pre>";

  }
  
}
class Student extends Person{
  protected $courses=[];
  public function getStudentCourses(){
    return $this->courses;
  }
  public function changeMajor($newMajor){
    $this->major=$newMajor;
  }
  public function addCourse(Course $course){
    $this->courses[]=$course;
  }

}
class Teacher extends Person{
}
class Enrollment{
  protected $student;
  protected $course;
  protected $grade=null;
  protected $enrollmentDate;
  protected $status;

  public function __construct($student,$course,$date){
    $this->student=$student;
    $this->course=$course;
    $this->enrollmentDate=$date;
  }
  public function assignGrade($grade){
    $this->grade=$grade;
  }
  public function getGrade(){
    return $this->grade;
  }
  public function getStudent(){
    return $this->student;
  }
  public function getCourse(){
    return $this->course;
  }
  public function showEnrollmentInfo(){
    echo "Name: <br>";
    print_r($this->getStudent());
    echo "Course: {$this->getCourse()} <br>";
    echo "Grade: {$this->getGrade()} <br>";
    echo "Date: {$this->enrollmentDate} <br>";
  }

}
class University{
  protected $name;
  protected $students=[];
  protected $teachers=[];
  protected $courses=[];

  public function __construct($name)
  {
    $this->name=$name;
  }
  public function addStudent(Student $student){
    $this->students[]=$student;
  }
  public function addTeacher(Teacher $teacher){
    $this->teachers[]=$teacher;
  }
  public function addCourses(Course $course){
    $this->courses[]= $course;
  }

  public function showUniversityInfo(){
    echo "Name: {$this->name} <br>";
    echo "Teachers: <br>";
    foreach ($this->teachers as $teacher){
      echo "- {$teacher->getName()} <br>";
    }
    echo "<hr>";
    echo "Students: <br>";
    foreach ($this->students as $student){
      echo "- {$student->getName()} <br>";
    }
    echo "<hr>";
    echo "Courses: <br>";
    foreach ($this->courses as $course){
      echo "- {$course->getname()} <br>";
    }
  }
  public function findStudentById($id){
    $searchResult=null;
    foreach($this->students as $student){
      if($student->getId()==$id){
        $searchResult=$student;
        break;
      }
    }
    return $searchResult;

  }
  public function findCourseByCode($code){
    $searchResult=null;
    foreach($this->courses as $course){
      if ($course->getCode()==$code){
        $searchResult=$course;
      }
    }
    return $searchResult;
  }

  public function findTeacherById($id){
    $searchResult=null;
    foreach($this->teachers as $teacher){
      if ($teacher->getId()==$id){
        $searchResult=$teacher;
      }
    }
    return $searchResult;
  }
  public function getTotalStudents(){
    return count($this->students);
  }
}

$student1=new Student(1,"leno",'front-end','lolo@gmail.com');
$student2=new Student(2,"Ali",'Back-end','ali@gmail.com');
$student3=new Student(3,'rama','front-end','rama@gmail.com');
$student4=new Student(4,'sara','Back-end','sara@gmail.com');

$teacher1=new Teacher(1,'Kakashi','front-end','ka@gmail.com');
$teacher2=new Teacher(2,'Hisoka','Back-end','hi@gmail.com');

$course1=new Course('0100','HTML',200,2);
$course1->assignTeacher($teacher1);
$course1->enrollStudent($student1);
$course1->enrollStudent($student3);

$course1->showInfo();
echo "<hr>";
$course2=new Course('002','PHP',250,5);
$course2->assignTeacher($teacher2);
$course2->enrollStudent($student2);
$course2->enrollStudent($student4);
$course2->enrollStudent($student1);
$course2->showInfo();

$course=new Course('003','Paython',300,3);

$uni1=new University('S7L');

$uni1->addStudent($student1);
$uni1->addStudent($student2);
$uni1->addStudent($student3);
$uni1->addStudent($student4);


$uni1->addTeacher($teacher1);
$uni1->addTeacher($teacher2);

$uni1->addCourses($course1);
$uni1->addCourses($course2);

$uni1->showUniversityInfo();

echo "<hr>";
$course1->assignGrades($student1,100);
$course1->assignGrades($student3,40);
// print_r($course1->showGrades());
echo "Average Grades :". $course1->getAverageGrades() ."<br>";
echo "Top Student:".$course1->getTopStudent()."<br>";
echo "<hr>";
$course2->assignGrades($student2,86);
$course2->assignGrades($student4,49);
print_r($course2->getGrades());
echo "Average Grades :". $course2->getAverageGrades() ."<br>";
echo "Top Student:".$course2->getTopStudent()."<br>";
echo "<hr>";

$uni1->showUniversityInfo();

echo $course1->getStudentGrade($student1);
echo $course1->isPassed($student1);
echo "<br>";

echo $course2->getStudentGrade($student2);
echo $course2->isPassed($student2);

echo $course1->getStudentGrade($student3);
echo $course1->isPassed($student3);
echo "<br>";
echo $course2->getStudentGrade($student4);
echo $course2->isPassed($student4);
echo "<br>";

echo $uni1->getTotalStudents();
echo "<br>";
print_r($student1->getStudentCourses());
echo "<br>";
print_r($student2->getStudentCourses());
echo "<br>";
print_r($student3->getStudentCourses());
echo "<br>";
print_r($student4->getStudentCourses());
echo "<br>";

print_r($uni1->findCourseByCode('002'));
echo "<br>";

print_r($uni1->findStudentById(4));
echo "<br>";

print_r($uni1->findTeacherById(1));
echo "<br>";
print_r($uni1->findTeacherById(4));
echo "<br>";
$student4->changeMajor('front-end');
$student4->showInfo();
echo "<hr>";
$enrollment1= new Enrollment($student1,$course1,'5/6/2026');
$enrollment1->assignGrade(98);
$enrollment1->showEnrollmentInfo();


?>