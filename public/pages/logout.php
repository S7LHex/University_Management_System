<?php
require_once __DIR__. "/../../repositories/user_repositories.php";
require_once __DIR__. "/../../config/db.php";

$pdo= new Database;
$conn= $pdo->connect();
$userRepo= new User($conn);

$userRepo->logout();

header('Location:login.php');
exit;

?>