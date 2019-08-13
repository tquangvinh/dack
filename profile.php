<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'profile';
    $user = findUserById($_GET['id']);
    if(!$currentUser|| $currentUser['Activated'] == 0)
    {
        header('Location: index.php');
        exit(0);
    }

    $posts = findAllPostsOfFriends($currentUser['id']);

    $relationship = findRelationship($currentUser['id'], $user['id']);
    $isFriend = count($relationship) === 2;
    $noRelationship = count($relationship) === 0;
    if(count($relationship) === 1)
    {
        $isRequesting = $relationship[0]['user1Id'] === $currentUser['id'];
    }

    $friendIds_currentUser = findAllFriendIds($currentUser['id']);
    $friendIds_User = findAllFriendIds($user['id']);
    $invitations = findInvitation($currentUser['id']);
?>

<?php include 'header.php';?>
<br/>
<p><img style=" width: 250px; height: 250px; "src="./profile_picture/<?php echo $user['id']; ?>.jpg"></p>
<p style="font-family: 'Times New Roman'; font-size: 50px;"><strong><?php echo $user['fullname']; ?></strong></p>
<br/>
<h1>DANH SÁCH LỜI MỜI KẾT BẠN</h1>
<?php if($currentUser['id'] == $user['id']):?>
<?php if(countInvitation($currentUser['id']) == 0):?>
    <h3><b><i>Bạn hiện không có lời mời kết bạn nào.</i></b></h3>
  <?php else:?>
  <?php foreach($invitations as $i):?>
    <?php $r = findRelationship($currentUser['id'], $i['user1Id']); ?>
    <?php if(count($r) == 1):?>
      <h3><p>Bạn có một lời mời từ <?php $u = findUserById($i['user1Id']); echo '<a href="./profile.php?id=' .$u['id'] .'">' .$u['fullname']  .'</a>'; ?></p><h3>
    <?php endif;?>
  <?php endforeach; ?>
  <?php endif;?>
<?php endif;?>
<br/>
<h1>DANH SÁCH BẠN BÈ</h1>
<?php if ($user['id'] !== $currentUser['id']) : ?>
<form action="friends.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>"/>
    <?php if($isFriend):?>
        <input type="submit" name="action" class="btn btn-danger" value="Xóa bạn bè">
    <?php elseif($noRelationship) :?>
        <input type="submit" name="action" class="btn btn-primary" value="Gửi yêu cầu kết bạn">
        <?php else:?>
        <?php if(!$isRequesting) :?>
        <input type="submit" name="action" class="btn btn-success" value="Đồng ý yêu cầu kết bạn">
    <?php endif;?>
        <input type="submit" name="action" class="btn btn-warning" value="Hủy yêu cầu kết bạn">
    <?php endif; ?>

</form>
<?php endif;?>
<h2>
<?php
if(($currentUser['id'] == $user['id']))
{
    foreach($friendIds_currentUser AS $f)
    {
        $name = findUserById($f);
        echo '<a href="./profile.php?id=' .$f .'">' .$name['fullname']  .'</a> <br/>';
    }
}
else
{
    foreach($friendIds_User AS $f)
    {
        $name = findUserById($f);
        echo '<a href="./profile.php?id=' .$f .'">' .$name['fullname']  .'</a> <br/>';
    }
}
?>
<h2>

<hr/>

<h1>BÀI VIẾT</h1>
<?php if($currentUser['id'] == $user['id']):?>

<?php foreach($posts as $post): ?>

<?php if($post['userId'] === $currentUser['id'] || $post['friendId'] === $currentUser['id']):?>
    <?php if($post['userId'] === $currentUser['id'] && $post['postMode'] !== 'friends'):?>

    <div class="card" style="border-style:inset; border-width:1px; border-color: silver;">
    <div class="card-body">
      <h5 class="card-title">

        <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
        <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $post['fullname']; ?></a>
        
        </h5>
      <h6 class="card-subtitle mb-2 text-muted"><?php echo $post['createdAt']; ?></h6>
      <p class="card-text"><?php echo $post['content']; ?></p>
      <?php
        $comments = findAllCommentsId($post['id']);
        $path = './post_picture/' .$post['id'] .'.jpg';
        if(file_exists($path))
        {
          echo '<p><img style="width: 256px; height: 256px;" src="' .$path .'"></p>';
        }
      ?>
    <form action="index.php" method="POST">
      <span class="form-group">
          <textarea rows="2" cols="138" name="cmns" placeholder = "Bình luận"></textarea>
      </span>
      <button type="submit" class="btn btn-primary" name="<?php echo $post['id']; ?>">Đăng bình luận</button>
      </form>
      <br/>
      <?php
        foreach($comments as $comment)
        {
          if($post['id'] == $comment['postId'])
          {
            echo '
                <img style="width: 25px; height: 25px;" src="./profile_picture/' .$comment['userId'] .'.jpg"> <b>'
                  .$comment['fullname'] .'</b><span class="card-subtitle mb-2 text-muted">  ' .$comment['createAt'] .'</span>
                <p class="card-text">' .$comment['content'] .'</p>
                
            ';
          }
        }
      ?>

    </div>
  </div>

<br/>
    <?php endif;?>

    <?php if($post['friendId'] === $currentUser['id']):?>
            <?php $friendID = findUserById($post['friendId']); ?>
            <div class="card" style="border-style:inset; border-width:1px; border-color: silver;">
    <div class="card-body">
      <h5 class="card-title">
            <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['id'];?>.jpg">
            <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $post['fullname']; ?></a> >
            <a href="profile.php?id= <?php echo $friendID['id']; ?>"><?php echo $friendID['fullname']; ?></a>

            </h5>
      <h6 class="card-subtitle mb-2 text-muted"><?php echo $post['createdAt']; ?></h6>
      <p class="card-text"><?php echo $post['content']; ?></p>
      <?php
        $comments = findAllCommentsId($post['id']);
        $path = './post_picture/' .$post['id'] .'.jpg';
        if(file_exists($path))
        {
          echo '<p><img style="width: 256px; height: 256px;" src="' .$path .'"></p>';
        }
      ?>
    <form action="index.php" method="POST">
      <span class="form-group">
          <textarea rows="2" cols="138" name="cmns" placeholder = "Bình luận"></textarea>
      </span>
      <button type="submit" class="btn btn-primary" name="<?php echo $post['id']; ?>">Đăng bình luận</button>
      </form>
      <br/>
      <?php
        foreach($comments as $comment)
        {
          if($post['id'] == $comment['postId'])
          {
            echo '
                <img style="width: 25px; height: 25px;" src="./profile_picture/' .$comment['userId'] .'.jpg"> <b>'
                  .$comment['fullname'] .'</b><span class="card-subtitle mb-2 text-muted">  ' .$comment['createAt'] .'</span>
                <p class="card-text">' .$comment['content'] .'</p>
                
            ';
          }
        }
      ?>

    </div>
  </div>

  <br/>
    <?php endif;?>

     
<?php endif;?> <!-- if($post['userId'] === $currentUser['id'] || $post['friendId'] === $currentUser['id']) -->

<?php endforeach; ?>
      <?php else: ?>
        <?php $posts = findAllPostsUserId($user['id']);?>
        <?php foreach($posts as $post): ?>
    <?php if($post['userId'] === $user['id'] && $post['postMode'] === 'public'):?>

    <div class="card" style="border-style:inset; border-width:1px; border-color: silver;">
    <div class="card-body">
      <h5 class="card-title">

        <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
        <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $user['fullname']; ?></a>
        
        </h5>
      <h6 class="card-subtitle mb-2 text-muted"><?php echo $post['createdAt']; ?></h6>
      <p class="card-text"><?php echo $post['content']; ?></p>
      <?php
        $comments = findAllCommentsId($post['id']);
        $path = './post_picture/' .$post['id'] .'.jpg';
        if(file_exists($path))
        {
          echo '<p><img style="width: 256px; height: 256px;" src="' .$path .'"></p>';
        }
      ?>
    <form action="index.php" method="POST">
      <span class="form-group">
          <textarea rows="2" cols="138" name="cmns" placeholder = "Bình luận"></textarea>
      </span>
      <button type="submit" class="btn btn-primary" name="<?php echo $post['id']; ?>">Đăng bình luận</button>
      </form>
      <br/>
      <?php
        foreach($comments as $comment)
        {
          if($post['id'] == $comment['postId'])
          {
            echo '
                <img style="width: 25px; height: 25px;" src="./profile_picture/' .$comment['userId'] .'.jpg"> <b>'
                  .$comment['fullname'] .'</b><span class="card-subtitle mb-2 text-muted">  ' .$comment['createAt'] .'</span>
                <p class="card-text">' .$comment['content'] .'</p>
                
            ';
          }
        }
      ?>

    </div>
  </div>

  <br/>
    <?php endif;?>

    <?php 
      $ff = findUserById($post['userId']);
     if($post['friendId'] === $user['id']):
     ?>
            <div class="card" style="border-style:inset; border-width:1px; border-color: silver;">
    <div class="card-body">
      <h5 class="card-title">
            <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
            <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $ff['fullname']; ?></a> >
            <a href="profile.php?id= <?php echo $post['friendId']; ?>"><?php echo $user['fullname']; ?></a>

            </h5>
      <h6 class="card-subtitle mb-2 text-muted"><?php echo $post['createdAt']; ?></h6>
      <p class="card-text"><?php echo $post['content']; ?></p>
      <?php
        $comments = findAllCommentsId($post['id']);
        $path = './post_picture/' .$post['id'] .'.jpg';
        if(file_exists($path))
        {
          echo '<p><img style="width: 256px; height: 256px;" src="' .$path .'"></p>';
        }
      ?>
    <form action="index.php" method="POST">
      <span class="form-group">
          <textarea rows="2" cols="138" name="cmns" placeholder = "Bình luận"></textarea>
      </span>
      <button type="submit" class="btn btn-primary" name="<?php echo $post['id']; ?>">Đăng bình luận</button>
      </form>
      <br/>
      <?php
        foreach($comments as $comment)
        {
          if($post['id'] == $comment['postId'])
          {
            echo '
                <img style="width: 25px; height: 25px;" src="./profile_picture/' .$comment['userId'] .'.jpg"> <b>'
                  .$comment['fullname'] .'</b><span class="card-subtitle mb-2 text-muted">  ' .$comment['createAt'] .'</span>
                <p class="card-text">' .$comment['content'] .'</p>
                
            ';
          }
        }
      ?>

    </div>
  </div>


  <br/>
<?php endif;?>

<?php endforeach; ?>


      <?php endif; ?>
<?php include 'footer.php';?>
