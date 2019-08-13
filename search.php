<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'search';

    if(!$currentUser || $currentUser['Activated'] == 0)
    {
        header('Location: index.php');
        exit(0);
    }

    if(isset($_POST['keyword']))
    {
        $keywork = $_POST['keyword'];
    }
    else $keywork = '';
    $listUsers = findAllUserByName($keywork);
    $listPosts = findAllPostsOfFriendsByContent($currentUser['id'], $keywork);
    $soLuong = count($listUsers) + count($listPosts);
    if(!$currentUser)
    {
        header('Location: index.php');
        exit(0);
    }
?>

<?php include 'header.php';?>
<h1>Tìm kiếm</h1>

<?php
    echo 'Tìm thấy ' .$soLuong .' kết quả. Với từ khóa <b><i>"' .$keywork .'"</i></b><br/><br/>';
    echo '<u><i>DANH SÁCH THÀNH VIÊN</i></u><br/>';
    foreach($listUsers as $lU)
    {
        echo '<a href="./profile.php?id=' .$lU['id'] .'">' .$lU['fullname']  .'</a> <br/>';
    }

    echo '<br/><br/><u><i>DANH SÁCH BÀI VIẾT</i></u><br/><br/>';
?>


<?php foreach($listPosts as $post): ?>
<div class="card" style="border: 2px solid black;">
  <div class="card-body">
    <h5 class="card-title">
    <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
    <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $post['fullname']; ?></a>
    </h5>
    <h6 class="card-subtitle mb-2 text-muted"><?php echo $post['createdAt']; ?></h6>
    <p class="card-text"><?php echo $post['content']; ?></p>
    
<?php
    $path = './post_picture/' .$post['id'] .'.jpg';
    if(file_exists($path))
    {
      echo '<p><img style="width: 256px; height: 256px;" src="' .$path .'"></p>';
    }
?>
</div>
</div>
<br/>
<?php endforeach; ?>

<?php include 'footer.php';?>