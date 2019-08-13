<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require 'vendor/autoload.php';

function findUserById($id)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
    $stmt->execute(array($id));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function findAllUserByName($name)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE fullname LIKE '%$name%'");
    $stmt->execute(array());
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $users;
}

function findAllPostsOfFriendsByContent($userId, $keyword)
{
    global $db;
    $friendIds = findAllFriendIds($userId);
    $friendIds[] = $userId;
    $stmt = $db->prepare("  SELECT p.*, u.fullname 
                            FROM posts AS p LEFT JOIN users AS u 
                                ON u.id = p.userId
                            WHERE userId IN (".str_pad("", count($friendIds)*2-1,"?,").")
                             AND p.content LIKE '%$keyword%'
                            ORDER BY createdAt DESC");
    $stmt->execute($friendIds);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $posts;
}

function findUserByEmail($email)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute(array($email));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function findAllCommentsId($postId)
{
    global $db;
    $stmt = $db->prepare("  SELECT c.*, u.fullname
                            FROM comment AS c LEFT JOIN users AS u 
                            ON u.id = c.userId AND c.postId = ?");
    $stmt->execute(array($postId));
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $comments;
}


function createUser($email, $fullname, $passwordHash)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO users(email, fullname,sdt, password, Activated) VALUES (?,?,NULL,?,0)");
    $stmt->execute(array($email, $fullname, $passwordHash));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $db->lastInsertId();
}

function addNewPost($content, $userId, $postMode, $friendId)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO posts(content, userId, postMode, friendId, createdAt) VALUES (?,?,?,?,NOW())");
    $stmt->execute(array($content, $userId, $postMode, $friendId));
    $posts = $stmt->fetch(PDO::FETCH_ASSOC);
    return $db->lastInsertId();
}

function addNewComment($postId,$userId,$content)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO comment(postId, userId, content) VALUES (?, ?, ?)");
    $stmt->execute(array($postId,$userId,$content));
    $posts = $stmt->fetch(PDO::FETCH_ASSOC);
    return $db->lastInsertId();
}

function updateInfo($fullname, $sdt, $id)
{
     global $db;

    $data = [
             ':fullname' => $fullname,
             ':sdt' => $sdt,
             ':userId' => $id
            ];

    $sql = 'UPDATE users
            SET fullname = :fullname,
                sdt = :sdt
            WHERE id = :userId';

    $stmt = $db->prepare($sql);

    return $stmt->execute($data);
}

function ativatedAccount($userId)
{
    global $db;
    $stmt = $db->prepare("UPDATE users SET Activated = 1 WHERE id = ?");
    $stmt->execute(array($userId));
}

function updatePassword($userId,$password)
{
    global $db;
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute(array($password, $userId));
}

//Source: https://stackoverflow.com/questions/4356289/php-random-string-generator
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function createResetPassword($userID)
{
    global $db;
    $secret = generateRandomString();
    $stmt = $db->prepare("INSERT INTO reset_passwords(userID, secret, used) VALUES (?,?,0)");
    $stmt->execute(array($userID, $secret));
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $secret;
}

function findResetPassword($secret)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM reset_passwords WHERE secret=? LIMIT 1");
    $stmt->execute(array($secret));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function markResetPasswordUsed($secret)
{
    global $db;
    $stmt = $db->prepare("UPDATE reset_passwords SET used = 1 WHERE secret = ?");
    $stmt->execute(array($secret));
}

function sendEmail($email, $receiver, $subject, $content)
{
    $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ltweb1.cd2018@gmail.com';
        $mail->Password = 'abc123XYZ~';
        $mail->SMTPSecure = 'TLS';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('ltweb1.cd2018@gmail.com', 'LTWeb1');
        $mail->addAddress($email, $receiver);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;
}

//Source: https://stackoverflow.com/questions/14649645/resize-image-in-php
function resizeImage($file, $w, $h, $crop=FALSE, $output) {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagejpeg($dst, $output);
}

function findRelationship($userId1, $userId2)
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM friends WHERE user1Id = ? AND user2Id = ? OR user1Id = ? AND user2Id = ?");
    $stmt->execute(array($userId1, $userId2, $userId2, $userId1));
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $friends;
}

function findInvitation($userId)
{
    global $db;
    $stmt = $db->prepare("SELECT DISTINCT user1Id FROM friends WHERE user2Id = ?");
    $stmt->execute(array($userId));
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $friends;
}

function countInvitation($userId)
{
    $f = findInvitation($userId);
    $c = 0;
    foreach($f as $i)
    {
        $r = findRelationship($userId, $i['user1Id']);
        if(count($r) == 1)
        {
            $c += 1;
        }
    }
    return $c;
}

function addRelationship($user1Id, $user2Id)
{
    global $db;
    $stmt = $db->prepare("INSERT INTO friends(user1Id,user2Id) VALUE (?,?)");
    $stmt->execute(array($user1Id, $user2Id));
}

function removeRelationship($user1Id, $user2Id)
{
    global $db;
    $stmt = $db->prepare("DELETE FROM friends WHERE (user1Id = ? AND user2Id = ?) OR (user2Id = ? AND user1Id = ?)");
    $stmt->execute(array($user1Id, $user2Id,$user1Id, $user2Id));
}

function findAllFriendIds($userId)
{
    global $db;
    $stmt = $db->prepare("SELECT f1.user1Id
                        FROM FRIENDS f1 JOIN FRIENDS f2 ON f1.user1Id = f2.user1Id AND f1.user2Id = f2.user2Id
                        WHERE f1.user2Id = ? AND 2 = (SELECT COUNT(*)
                                                    FROM friends 
                                                    WHERE user1Id = f1.user1Id AND user2Id = f1.user2Id 
                                                     OR user1Id = f1.user2Id AND user2Id = f1.user1Id)");
    $stmt->execute(array($userId));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = array();
    foreach($rows AS $row)
    {
        $result[] = $row['user1Id'];
    }
    return $result;
}

function findAllPostsOfFriends($userId)
{
    global $db;
    $friendIds = findAllFriendIds($userId);
    $friendIds[] = $userId;
    $stmt = $db->prepare("  SELECT p.*, u.fullname 
                            FROM posts AS p LEFT JOIN users AS u 
                                ON u.id = p.userId
                                WHERE userId IN (".str_pad("", count($friendIds)*2-1,"?,").")
                            ORDER BY createdAt DESC");
    
    $stmt->execute($friendIds);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $posts;
}

function findAllPostsUserId($userId)
{
    global $db;
    $stmt = $db->prepare('  SELECT * 
                            FROM posts
                            WHERE userId= ? OR friendId = ?');
    $stmt->execute(array($userId, $userId));
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $posts;
}

function getLikes($id)
    {
        global $db;
        $sql =$db->prepare("SELECT COUNT(*) FROM rating_info
        WHERE post_id = :post_id AND rating_action=:rating_action") ;
        $sql->execute(array(
                           'post_id'=>$id,
                          'rating_action'=>'like' ));
        $result=$sql->fetchColumn();
        return $result;
    }
// Get total number of dislikes for a particular post
function getDislikes($id)
{
  global $db;
        $sql =$db->prepare("SELECT COUNT(*) FROM rating_info
        WHERE post_id = :post_id AND rating_action=:rating_action") ;
         $sql->execute(array(
                           'post_id'=>$id,
                          'rating_action'=>'dislike' ));
        $result=$sql->fetchColumn();
        return $result;
}
// // Get total number of likes and dislikes for a particular post
function getRating($id)
{
  global $db;
  
  $likes_query = $db->prepare("SELECT COUNT(*) FROM rating_info 
                                 WHERE post_id = :post_id AND rating_action=:rating_action");
  $dislikes_query = $db->prepare("SELECT COUNT(*) FROM rating_info 
                                 WHERE post_id = :post_id AND rating_action=:rating_action");
  $dislikes_query->execute(array(
    'post_id'=>$id,
    'rating_action'=>'dislike'
  ));
  $likes_query->execute(array(
    'post_id'=>$id,
    'rating_action'=>'like'
  ));
  $likes = $likes_query->fetchColumn();
  $dislikes = $dislikes_query->fetchColumn();
  $rating=array(
    'likes' => $likes,
    'dislikes' => $dislikes
  );
  return json_encode($rating);
}
// Check if user already likes post or not
function userLiked($post_id)
{
  global $db;
  $user_id=$_SESSION['userId'];
  $sql = $db->prepare("SELECT * FROM rating_info WHERE user_id=:user_id
          AND post_id=:post_id AND rating_action=:rating_action");
  $sql->execute( 
    array('user_id' => $user_id,
          'post_id' => $post_id,
          'rating_action'=>'like'     )
    );
   $count = $sql->rowCount();  
  if ($count> 0) 
  {
    return true;
  }
  else
  {
    return false;
  }
}
// Check if user already dislikes post or not
function userDisliked($post_id)
{
  global $db;
  $user_id=$_SESSION['userId'];
  $sql = $db->prepare("SELECT * FROM rating_info WHERE user_id=:user_id
          AND post_id=:post_id AND rating_action=:rating_action");
  $sql->execute( 
    array('user_id' => $user_id,
          'post_id' => $post_id,
          'rating_action'=>'dislike')
    );
   $count = $sql->rowCount();  
  if ($count> 0) 
  {
    return true;
  }
  else
  {
    return false;
  }
}
