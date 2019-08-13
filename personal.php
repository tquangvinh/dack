<?php
    require_once 'init.php';
    require_once 'functions.php';
    //Xử lý logic ở đây
    $page = 'personal';
    if(!$currentUser || $currentUser['Activated'] == 0)
    {
        header('Location: index.php');
        exit(0);
    }

    if(isset($_POST['fullname']) && isset($_POST['sdt']))
    {
      $fullname = $_POST['fullname'];
      $sdt = $_POST['sdt'];
      $id = $currentUser['id'];
      updateInfo($fullname, $sdt, $id);
      //Xử lý upload file
      if(isset($_FILES['profilePicture']))
      {
        $fileTemp = $_FILES['profilePicture']['tmp_name'];
        $fileName = 'profile_picture/' . $currentUser['id'] .'.jpg';
        $result = move_uploaded_file($fileTemp, $fileName);
        if ($result)
        {
          resizeImage($fileName, 1000, 500,false, $fileName);
        }
      }
      header('Location: personal.php');
    }
?>

<?php include 'header.php';?>
<h1>Quản lý thông tin cá nhân</h1>
<?php if(!isset($_POST['update'])) :?>
<form action="personal.php" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label for="fullname">Họ và tên</label>
    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Họ và tên" value="<?php echo $user['fullname']?>">
  </div>
  
  <div class="form-group">
    <label for="sdt">Số điện thoại</label>
    <input type="text" class="form-control" id="sdt" name="sdt" placeholder="Số điện thoại" value="<?php echo $user['sdt']?>">
  </div>

  <div class="form-group">
    <label for="profilePicture">Ảnh đại diện</label>
    <input type="file" class="form-control-file" id="profilePicture" name="profilePicture">
  </div>
  
  <button type="submit" class="btn btn-primary" name="update">Cập nhật</button>
</form>
<?php endif; ?>
<?php include 'footer.php';?>