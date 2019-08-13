<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'forgot';
    $success = false;
    if(isset($_POST['secret']) && isset($_POST['password']))
    {
        $password = $_POST['password'];
        $secret = $_POST['secret'];
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $reset= findResetPassword($secret);
        if($reset && !$reset['used'])
        {
            $userId = $reset['userId'];
            markResetPasswordUsed($secret);
            updatePassword($userId, $passwordHash);
            header('Location: login.php');
            $success = true;
        }
    }
?>

<?php include 'header.php';?>
<h1>Khôi phục mật khẩu</h1>
<?php if(!isset($_POST['secret'])) :?>
<form action="reset_password.php" method="POST">
   <input type="hidden" name = "secret" value="<?php echo $_GET['secret']; ?>">
  <div class="form-group">
    <label for="password">Mật khẩu</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
  </div>
  
  <button type="submit" class="btn btn-primary" name="submit">Khôi phục</button>
</form>
<?php endif; ?>
<?php include 'footer.php';?>