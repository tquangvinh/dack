<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đồ án cuối kỳ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- link like -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
    i
    {
      color: blue;
    }
    h1,a
    {
      font-weight: bold;
    }

    #cdd,#lfs
    {
      width: 115px;
      height: 35px;
      border-radius: 2px;
      text-align: center;
      font-weight: bold;
      font-size: 18px;
    }
    </style>
</head>
<body>

<div class="container">
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Đồ án cuối kỳ</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item <?php echo $page === 'index' ? 'active' : ''?>">
        <a class="nav-link" href="index.php">Trang chủ</a>
      </li>
      <?php if(!$currentUser): ?>
      <li class="nav-item <?php echo $page === 'register' ? 'active' : ''?>">
        <a class="nav-link" href="register.php">Đăng ký</a>
      </li>
      <li class="nav-item <?php echo $page === 'login' ? 'active' : ''?>">
        <a class="nav-link" href="login.php">Đăng nhập</a>
      </li>
      <li class="nav-item <?php echo $page === 'forgot' ? 'active' : ''?>">
        <a class="nav-link" href="forgot_password.php">Khôi phục mật khẩu</a>
      </li>
<?php else:?>
      <li class="nav-item <?php echo $page === 'post' ? 'active' : ''?>">
        <a class="nav-link" href="post.php">Đăng trạng thái</a>
      </li>

      <li class="nav-item <?php echo $page === 'personal' ? 'active' : ''?>">
        <a class="nav-link" href="personal.php">Thông tin cá nhân</a>
      </li>

      <li class="nav-item <?php echo $page === 'profile' ? 'active' : ''?>">
        <a class="nav-link" href="profile.php?id=<?php echo $currentUser['id'] ?>"><?php echo $currentUser['fullname'] ?></a>
      </li>

      <li class="nav-item <?php echo $page === 'changePass' ? 'active' : ''?>">
        <a class="nav-link" href="changePassword.php">Đổi mật khẩu</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="logout.php">Đăng xuất</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" action="search.php" method="POST">
      <input class="form-control mr-sm-2" type="search" placeholder="Tìm kiếm..." name="keyword">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Tìm kiếm</button>
    </form>
      </li>
<?php endif;?>
  </div>
</nav>
