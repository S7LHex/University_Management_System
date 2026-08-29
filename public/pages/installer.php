<?php
require_once __DIR__."/../config/db.php";

class Installer{
  private $conn;

  public function __construct(){
    $db = new Database;
    $this->conn=$db->connect();
  }
  public function adminExists(){
    $stmt=$this->conn->query("select count(*) from users where role='admin' ");
    return $stmt->fetchColumn() > 0;
  }
  public function createAdmin($email,$password){
    $sql='insert into users (email,password,role) values(?,?,?) ';
    $stmt=$this->conn->prepare($sql);
    $stmt->execute([$email,$password,'admin']);
  }
}







?>