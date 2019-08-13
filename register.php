<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'register';
    $success = false;
   
    if(isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['password']))
    {
        $password = $_POST['password'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $user = findUserByEmail($email);
        if($success && empty($email) || empty($fullname) || empty($password))
        {
            $error = ' Không được để trống!';
        }
        
        else 
        {
            if(!$user)
            {
                $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $userId = createUser($email, $fullname, $passwordHash);
                $_SESSION['userId'] = $userId;
                sendEmail($email,$fullname, 'Yeu cau kich hoat tai khoan', 'Click <a href="http://localhost/DACK2/confirm_account.php?id=' .$userId .'">vào đây.</a>');
                $success = true;
            }
            else
            {
                $error= ' Tài khoản đã tồn tại!';
            }
        }
    }
?>
<?php include 'header.php';?>
<h1>Đăng ký</h1>
<?php if(!$success) :?>
<form action="register.php" method="POST">
    <?php if(isset($error)): ?>

        <div class ="alert alert-danger">
            <strong>Lỗi! </strong><?php echo $error ?>
        </div>
    <?php endif ?> 
    <div class="form-group">
        <label for="fullname">Họ và tên</label>
        <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Fullname">
    </div>

  <div class="form-group">
    <label for="email">Địa chỉ mail</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
  </div>

  <div class="form-group">
    <label for="password">Mật khẩu</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
  </div>

  <button type="submit" class="btn btn-primary" name="submit">Đăng ký</button>
</form>
<?php endif; ?>

<?php if($success && isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['password'])) :?>
<div class="alert alert-success" role="alert">
    Đã gửi link kích hoạt vào hộp thư email của bạn. Xin vui lòng kiểm tra email.!!!
</div>
<?php endif; ?>
<?php include 'footer.php';?> 