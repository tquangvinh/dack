<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'forgot';
    if(isset($_POST['email']))
    {
        $email = $_POST['email'];
        $user = findUserByEmail($email);
        if($user)
        {
            $secret = createResetPassword($user['id']);
            sendEmail($user['email'],$user['fullname'], 'Yeu cau doi mat khau', 'Click <a href="http://localhost/DACK/reset_password.php?secret=' .$secret .'">vào đây.</a>');
        }
        else $success = true;
    }
?>

<?php include 'header.php';?>
<h1>Quên mật khẩu</h1>
<?php if(!isset($_POST['email'])) :?>
<form action="forgot_password.php" method="POST">
  <div class="form-group">
    <label for="email">Địa chỉ mail</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
  </div>
  <button type="submit" class="btn btn-primary" name="forgot">Khôi phục mật khẩu</button>
</form>
  <?php else: ?>

<?php if(empty($_POST['email'])) :?>
<p>Bạn chưa nhập email !!! <br/>Bạn có muốn tiếp tục <strong><a href="forgot_password.php">Lấy lại mật khẩu.</a></strong></p>
<p>Trở về <strong><a href="index.php">Trang chủ.</a></strong></p>
<?php else: ?>

<div class="alert alert-success" role="alert">
  Đã gửi hướng dẫn khôi phục vào hộp thư email của bạn. Xin vui lòng kiểm tra mail.!!!
</div>
<?php endif; ?>
<?php endif; ?>

<?php include 'footer.php';?>