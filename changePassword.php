<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'changePass';
    $success = false;
    if(!$currentUser || $currentUser['Activated'] == 0)
    {
        header('Location: index.php');
        exit(0);
    }
    if(isset($_POST['oldPass']) && isset($_POST['newPass']) && isset($_POST['renewPass']))
    {
        $oPass = $_POST['oldPass'];
        $nPass = $_POST['newPass'];
        $rPass = $_POST['renewPass'];
        $id = $user['id'];
        $check = password_verify($oPass, $user['password']);
        $success = true;
        if($check)
        {
            if($nPass === $rPass)
            {
                $passwordHash = password_hash($_POST['newPass'], PASSWORD_BCRYPT);
                updatePassword( $id,$passwordHash);
                unset($_SESSION['userId']);
                header('Location: login.php');
                
            } else $success = true;
        } else $success = true;
    }
?>

<?php include 'header.php';?>
<h1>Đổi mật khẩu</h1>
<?php if(!$success) :?>
<form action="changePassword.php" method="POST">
    <div class="form-group">
        <label for="oldPass">Mật khẩu cũ</label>
        <input type="password" class="form-control" id="oldPass" name="oldPass" placeholder="Điền mật khẩu cũ vào đây">
    </div>

  <div class="form-group">
    <label for="newPass">Mật khẩu mới</label>
    <input type="password" class="form-control" id="newPass" name="newPass" placeholder="Điền mật khẩu mới vào đây">
  </div>
  
  <div class="form-group">
    <label for="renewPass">Nhập lại mật khẩu mới</label>
    <input type="password" class="form-control" id="renewPass" name="renewPass" placeholder="Điền mật khẩu mới vào đây lần nữa">
  </div>
  
  <button type="submit" class="btn btn-primary" name="change">Đổi mật khẩu</button>
</form>
<?php endif; ?>

<?php if($success && (empty($_POST['oldPass']) || empty($_POST['newPass']) || empty($_POST['renewPass']))) :?>
<p>Các trường này không được để trống !!! Vui lòng kiểm tra lại <br/><strong><a href="changePassword.php">Tiếp tục đổi mật khẩu ?</a></strong></p>
<p>Trở về <strong><a href="index.php">Trang chủ.</a></strong></p>
<?php endif; ?>

<?php if($success && isset($_POST['oldPass']) && isset($_POST['newPass']) && isset($_POST['renewPass']) && (!password_verify($_POST['oldPass'], $user['password']))) :?>
<p>Mật khẩu cũ không chính xác !!!<br/><strong><a href="changePassword.php">Tiếp tục đổi mật khẩu ?</a></strong></p>
<p>Trở về <strong><a href="index.php">Trang chủ.</a></strong></p>
<?php endif; ?>


<?php if($success && isset($_POST['oldPass']) && isset($_POST['newPass']) && isset($_POST['renewPass']) && ($_POST['newPass'] != $_POST['renewPass'])) :?>
<p>Mật khẩu mới và nhập lại mật khẩu không khớp.!!!<br/><strong><a href="changePassword.php">Tiếp tục đổi mật khẩu ?</a></strong></p>
<p>Trở về <strong><a href="index.php">Trang chủ.</a></strong></p>
<?php endif; ?>
<?php include 'footer.php';?>