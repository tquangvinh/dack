<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'confirm';
    $userId = $_GET['id'];
    $user = findUserById($userId);
    if($user['Activated'] == 0)
    {
        ativatedAccount($userId);
    }
    header('Location: login.php');
?>
<?php include 'header.php';?>
<?php include 'footer.php';?>