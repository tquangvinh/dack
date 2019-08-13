<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'post';
    $listFriends = findAllFriendIds($currentUser['id']);
    $success = false;
    if(!$currentUser || $currentUser['Activated'] == 0)
    {
        header('Location: index.php');
        exit(0);
    }

    if(!empty($_POST['status']) || isset($_FILES['postPicture']))
    {
        $content = $_POST['status'];
        $userId = $currentUser['id'];
        $postMode = $_POST['mode'];
        if($postMode === 'friends')
            $friendId = $_POST['friends'];
        else $friendId = 0;
        $DangBai = addNewPost($content, $userId, $postMode, $friendId);
        if(isset($_FILES['postPicture']))
        {
            $fileTemp = $_FILES['postPicture']['tmp_name'];
            $fileName = 'post_picture/' . $DangBai .'.jpg';
            $result = move_uploaded_file($fileTemp, $fileName);
            if ($result)
            {
                resizeImage($fileName, 256, 256,false, $fileName);
            }
        }

        header('Location: index.php');
        $success = true;
    }
?>

<?php include 'header.php';?>
<h1>Cập nhật trạng thái</h1>
<?php if(!isset($_POST['post'])) :?>
<form action="post.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <textarea rows="5" cols="150" name="status" placeholder = "Bạn đang nghĩ gì ?"></textarea>
    </div>
    
    <div class="form-group">
        <label for="postPicture">Hình ảnh:</label>
        <input type="file" class="form-control-file" id="postPicture" name="postPicture">
    </div>

    <div class="form-group">
        <label>Chế độ đăng:</label>
        <select name="mode" id="cdd" onchange="showListFriends()">
        <option value="public">Công khai</option>
        <option value="private">Cá nhân</option>
        <option value="friends">Bạn bè</option>
        </select>
    </div>

    <div class="form-group" id="listFriends" style="display: none;">
        <?php
            echo '<label>Chọn một người bạn:</label>
            <select name="friends" id="lfs">';
           foreach($listFriends as $lfs)
           {
                $u = findUserById($lfs);
                echo '<option value="' .$u['id'] .'">' .$u['fullname'] .'</option>';
           }

            echo '</select>';
        ?>
    </div>

    <button type="submit" class="btn btn-primary" name="post">Đăng bài viết</button>
</form>
<?php endif; ?>
<!-- <?php if(isset($_POST['post']) && isset($_POST['status'])) :?>
<p>Bạn chưa nhập trạng thái !!! Hãy <strong><a href="post.php">Nhập lại.</a></strong></p>
<p>Trở về <strong><a href="index.php">Trang chủ.</a></strong></p>
<?php endif; ?> -->

<script>
function showListFriends() {
  var x = document.getElementById("cdd").value;
  if(x == "friends")
  {
    document.getElementById("listFriends").style.display = 'block';
  }
  else document.getElementById("listFriends").style.display = 'none';
}
</script>

<?php include 'footer.php';?>