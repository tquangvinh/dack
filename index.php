<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'index';
    $posts = findAllPostsOfFriends($currentUser['id']);

    foreach($posts as $p)
    {
      $postId = $p['id'];
      if(isset($_POST[$postId]))
      {
        $content = $_POST['cmns'];
        addNewComment($postId,$currentUser['id'],$content);
      }
    }
   

?>

<?php include 'header.php';?>

<h1>Trang chủ</h1>

<p>
<?php if($currentUser):?>
<?php if(countInvitation($currentUser['id']) != 0):?>
   <b style=" color: blue"> Bạn có  <?php echo countInvitation($currentUser['id']); ?> lời mời kết bạn. </b>
  <?php endif;?>
<?php if($currentUser['Activated'] == 0):?>
<br/> <font style="font-size: 40px;"><b>Tài khoản chưa kích hoạt. Vui lòng kiểm tra lại email.</b></font>
<?php endif;?>
<?php else :?>
  Chào mừng bạn đến với mạng xã hội của chúng tôi.
<?php endif;?>
</p>

<?php if($currentUser['Activated'] == 1):?>
<?php foreach($posts as $post): ?>

<?php if($post['postMode'] !== 'private' || $post['postMode'] === 'private' && $post['userId'] === $currentUser['id']):?>

  <div class="card" style="border-style:inset; border-width:1px; border-color: silver;">
    <div class="card-body">
      <h5 class="card-title">

      <?php if($post['postMode'] === 'public' || $post['postMode'] === 'private' && $post['userId'] === $currentUser['id']):?>
      <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
      <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $post['fullname']; ?></a>
    <?php endif;?>

    <?php if($post['postMode'] === 'friends'):?>
    <?php $friendID = findUserById($post['friendId']); ?>
    <img style="width: 50px; height: 50px;" src="./profile_picture/<?php echo $post['userId'];?>.jpg">
    <a href="profile.php?id= <?php echo $post['userId']; ?>"><?php echo $post['fullname']; ?></a> >
    <a href="profile.php?id= <?php echo $friendID['id']; ?>"><?php echo $friendID['fullname']; ?></a>
      
    <?php endif;?>

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
        <i <?php if (userLiked($post['id'])): ?>
            class="fa fa-thumbs-up like-btn"
          <?php else: ?>
            class="fa fa-thumbs-o-up like-btn"
          <?php endif ?>
          data-id="<?php echo $post['id'] ?>"> Like </i>
        <span class="likes"><?php echo getLikes($post['id']); ?></span>
        &nbsp;&nbsp;&nbsp;&nbsp;
  <script src="scripts.js"></script>
    <form action="index.php" method="POST">
      <span class="form-group">
          <textarea rows="2" cols="134" name="cmns" placeholder = "Bình luận"></textarea>
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
<?php endif;?> <!-- $post['postMode'] !== 'private')  -->
<?php endforeach; ?>
<?php endif;?> <!-- if($currentUser['Activated'] == 1): -->
<?php include 'footer.php';?>
