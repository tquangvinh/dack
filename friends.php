<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'profile';
    $user = findUserById($_POST['id']);
    if(!$currentUser)
    {
        header('Location: index.php');
        exit(0);
    }

    $relationship = findRelationship($currentUser['id'], $user['id']);
    $isFriend = count($relationship) === 2;
    $noRelationship = count($relationship) === 0;
    if(count($relationship === 1))
    {
        $isRequesting = $relationship[0]['user1Id'] === $currentUser['id'];
    }

    if($_POST['action'] === 'Gửi yêu cầu kết bạn' ||
    $_POST['action'] === 'Đồng ý yêu cầu kết bạn')
    {
        addRelationship($currentUser['id'], $user['id']);
    }

    if($_POST['action'] === 'Hủy yêu cầu kết bạn' ||
    $_POST['action'] === 'Xóa bạn bè')
    {
        removeRelationship($currentUser['id'], $user['id']);
    }
    header('Location: profile.php?id=' .$user['id']);
?>
