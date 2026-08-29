<?php
require_once __DIR__."/../config/db.php";

class Notification{
  private PDO $pdo;

  public function __construct(PDO $pdo){
    $this->pdo=$pdo;
  }

  public function createNotification(int $userId,string $message,string $type,?int $referencedId=null){
    $sql='insert into notifications (user_id,message,type,reference_id) values (?,?,?,?)';
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$userId,$message,$type,$referencedId]);
    return $this->pdo->lastInsertId();
  }

  public function notifyAllAdmins(string $message,string $type,?int $referencedId=null, int $excludeUserId=null) :void{
    $sql="select id from users where role='admin' ";
    if($excludeUserId !== null){
      $sql .=" and id != ?";
    }
    $stmt=$this->pdo->prepare($sql);
    if($excludeUserId!== null){
      $stmt->execute([$excludeUserId]);
    }else{
      $stmt->execute();
    }

    $admins=$stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($admins as $admin){
      $this->createNotification($admin['id'],$message,$type,$referencedId);
    }

  }
  public function getUnreadForUser(int $userId) :array{
    $sql="select * from notifications where  user_id=? and is_read= 0
    order by created_at desc ";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function markAsRead(int $notificationId){
    $sql="update notifications set is_read= 1 where id=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$notificationId]);
  }

  public function countUnread(int $userId){
    $sql="select count(*) from notifications where user_id=? and is_read=?";
    $stmt=$this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
  }
}



?>