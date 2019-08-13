<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'login';
    $success = false;
    if(isset($_POST['email']) && isset($_POST['password']))
    {
        $password = $_POST['password'];
        $email = $_POST['email'];
        $user = findUserByEmail($email);
        
        if($user)
        {
            $check = password_verify($password, $user['password']);
            if($check)
            {
                $_SESSION['userId'] = $user['id'];
                header('Location: index.php');
                $success = true;
            }
            else $error='Thông tin đăng nhập không chính xác!';
           
        }
         else
        {
            if(empty($email) || empty($password))
            $error='Không được để trống email hoặc password !';  
        } 
    }
?>

<?php include 'header.php';?>
<h1>Đăng nhập</h1>
<?php if(!$success) :?>
<form action="login.php" method="POST">
     <?php if(isset($error)): ?>

        <div class ="alert alert-danger">
            <strong>Lỗi! </strong><?php echo $error ?>
        </div>
    <?php endif ?> 
  <div class="form-group">
    <label for="email">Địa chỉ mail</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
  </div>

  <div class="form-group">
    <label for="password">Mật khẩu</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
  </div>

  <button type="submit" class="btn btn-primary" name="login">Đăng nhập</button>
</form>
<?php endif; ?>

<?php include 'footer.php';?> 