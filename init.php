<?php
session_start();
require_once 'functions.php';
$db = new PDO('mysql:host=localhost;dbname=dack;charset=utf8', 'root', '');

$currentUser = null;

if(isset($_SESSION['userId']))
{
    $user = findUserById($_SESSION['userId']);
    if($user)
    {
        $currentUser = $user;
    }
}
if (isset($_POST['actions'])) {
  $user_id=$_SESSION['userId'];
  $post_id = $_POST['post_id'];
  $action = $_POST['actions'];
  switch ($action) {
    case 'like':
        $sql=$db->prepare("INSERT INTO rating_info (post_id,user_id, rating_action) 
             VALUES (:post_id,:user_id,:rating_action)");
        $sql->bindParam(':post_id',$post_id);
        $sql->bindParam(':user_id',$user_id);
        $sql->bindValue(':rating_action','like',PDO::PARAM_STR);
        $sql->execute();
         break;
    case 'dislike':
          $sql=$db->prepare("INSERT INTO rating_info (post_id,user_id, rating_action) 
             VALUES (:post_id,:user_id,:rating_action)");
          $sql->bindParam(':post_id',$post_id);
          $sql->bindParam(':user_id',$user_id);
          $sql->bindValue(':rating_action','dislike',PDO::PARAM_STR);
          $sql->execute();
         break;
    case 'unlike':
        $sql=$db->prepare("DELETE FROM rating_info WHERE user_id=:user_id AND post_id=:post_id");
        $sql->execute(
            array(
              'user_id'=>$user_id,
              'post_id'=>$post_id
            ));
        break;
    case 'undislike':
          $sql=$db->prepare("DELETE FROM rating_info  WHERE user_id=:user_id AND post_id=:post_id");
          $sql->execute(
            array(
              'user_id'=>$user_id,
              'post_id'=>$post_id
            ));
      break;
    default:
      break;
  }
  echo getRating($post_id);
  exit(0);
}
?>
