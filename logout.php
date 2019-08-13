<?php
require_once 'init.php';
require_once 'functions.php';
//Xử lý logic ở đây
unset($_SESSION['userId']);
header('Location: index.php');
?>