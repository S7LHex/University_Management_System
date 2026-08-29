<?php
require_once __DIR__."/../../repositories/notification.php";
require_once __DIR__."/../../config/db.php";

$pdo= new Database;
$conn=$pdo->connect();
$notificationRepo= new Notification($conn);

$unreadNotifications=$notificationRepo->getUnreadForUser($_SESSION['user_id']);
$unreadCount= count($unreadNotifications);

?>
<link rel="stylesheet" href="../assets/css/notification_bell.css">

<div class="notification-bell-wrapper">
  <button id="notifBellBtn" class="notif-bell-btn">
    <img src="../assets/imgs/bell-svgrepo-com.svg" alt="">
    <span id="notifBadge" class='notif-badge' style="<?= $unreadCount>0 ?'':'display:none;' ?>">
      <?= $unreadCount>9 ? '+9': $unreadCount ?>
    </span>
  </button>

  <div id="notifDropdown" class="notif-dropdown" style="display: none;">
    <div id="notifList">
      <?php if(empty($unreadNotifications)): ?>
        <p class="notif-empty">No Notifications</p>
      <?php else: ?>
        <?php foreach($unreadNotifications as $n):?>
          
          <div class="notif-item">
            <p><?= htmlspecialchars($n['message']) ?></p>
            <span class="notif-time"><?= htmlspecialchars($n['created_at']) ?></span>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
    </div>
  </div>
</div>
<script>
  const notifBellBtn = document.getElementById('notifBellBtn');
  const notifDropdown = document.getElementById('notifDropdown');
  const notifBadge=document.getElementById('notifBadge');

  let alreadyMarkedThisSession = false;

  notifBellBtn.addEventListener('click',function(){
    const isOpening=notifDropdown.style.display ==='none';
    notifDropdown.style.display= isOpening ? 'block' : 'none';

    if(isOpening && !alreadyMarkedThisSession){
      alreadyMarkedThisSession= true;
      fetch('mark_notifications_read.php',{
        method:'POST'
      })
      .then(res => res.json())
      .then(data =>{
        if(data.success){
          notifBadge.style.display='none';
        }
      })
      .catch(err => console.error('Error marking notification as read' , err));
    }
  });

  document.addEventListener('click',function(e){
    if(!notifBellBtn.contains(e.target) && !notifDropdown.contains(e.target)){
      notifDropdown.style.display = 'none';
    }
  });


</script>