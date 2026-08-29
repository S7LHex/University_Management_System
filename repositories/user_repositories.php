<?php
require_once __DIR__."/../config/db.php";

class User{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function register($name,$email,$password,$role){
    $sql="select id from users where email=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$email]);
    if($stmt->fetch(PDO::FETCH_ASSOC)){
      return "User already registered.";
    }

    $relatedId=null;

    if($role=='teacher'){
      $stmt=$this->pdo->prepare('select id from teachers where email=?');
      $stmt->execute([$email]);
      $teacher=$stmt->fetch(PDO::FETCH_ASSOC);
      if(!$teacher){
        return "Teacher not found";
      }
      $relatedId=$teacher['id'];
      
    }
    if($role=='student'){
        $stmt=$this->pdo->prepare('select id from students where email=?');
        $stmt->execute([$email]);
        $student=$stmt->fetch(PDO::FETCH_ASSOC);
        if(!$student){
          return "Student not found.";
        }
        $relatedId=$student['id'];
    }

    $hashedPassword=password_hash($password,PASSWORD_DEFAULT);
    $sql="insert into users(email,display_name,password,role,related_id) values (?,?,?,?,?)";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$email,$name,$hashedPassword,$role,$relatedId]);
    return "User has been added Successfully.";
  }
  
  public function editUser($userId,$displayName,$email,$role){
    $sql="select id from users where email=? and id != ?";
    $stmt=$this->pdo->prepare($sql); 
    $stmt->execute([$email,$userId]);

    if($stmt->fetch(PDO::FETCH_ASSOC)){
      return "Email already in use,enter another one!";
    }

    $relatedId=null;

    if($role=='teacher'){
      $sql='select id from teachers where email=?';
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$email]);
      $teacher=$stmt->fetch(PDO::FETCH_ASSOC);
      if(!$teacher){
        return "Teacher not found";
      }
      $relatedId=$teacher['id'];
    }

    if($role=='student'){
      $sql='select id from students where email=?';
      $stmt=$this->pdo->prepare($sql);
      $stmt->execute([$email]);
      $student=$stmt->fetch(PDO::FETCH_ASSOC);
      if(!$student){
        return "student not found";
      }
      $relatedId=$student['id'];
    }

    $sql='update users set display_name=?,email=?,role=?,related_id=? where id=?';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$displayName,$email,$role,$relatedId,$userId]);
    return 'User has been edited successfully.';
  }

  public function editUserPassword($userId,$newPassword){
    $sql="update users set password = ? where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$newPassword,$userId]);
  }  

  

  public function login($email,$password){
    $stmt=$this->pdo->prepare('select * from users where email=?');
    $stmt->execute([$email]);
    $user=$stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user){
      return "Email not found.";
    }

    if(password_verify($password,$user['password'])){
      session_start();
      $_SESSION['user_id']=$user['id'];
      // $_SESSION['name']=$user['user_name'];
      $_SESSION['role']=$user['role'];
      $_SESSION['related_id']=$user['related_id'];
      return "Login Successful.";
    }
    return "Incorrect Password.";
  }

  public function getUsersById($userId){

    $stmt=$this->pdo->prepare('select * from users where id=?');
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);

  }

  public function getUsers(array $filtters,int $limit,int $offset){
    $sql="select * from users where 1=1  ";
    $params=[];

    if(!empty($filtters['role'])){
      $sql .=" and role =:role ";

      $params[':role']= $filtters['role'];
    }

    $sql .=" limit :limit offset :offset ";

    $stmt=$this->pdo->prepare($sql);
    foreach($params as $key=>$value){
      $stmt->bindValue($key,$value);
    }
    $stmt->bindValue(':limit',$limit,PDO::PARAM_INT);
    $stmt->bindValue(':offset',$offset,PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countAllUsers(array $filtters){
    $sql="select count(*) from users where 1=1 ";
    $params=[];

    if(!empty($filtters['role'])){
      $sql .=" and role=:role ";
      $params[':role']=$filtters['role'];
    }

    $stmt=$this->pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
  }

  public function getAllUsers(){
    $sql="select * from users";
    $stmt=$this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function deleteUser(int $userId){
    $sql="delete from users where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$userId]);
  }


  public function logout(){
      session_start();
      session_unset();
      session_destroy();
      return "Logout Successful";
}
}














?>