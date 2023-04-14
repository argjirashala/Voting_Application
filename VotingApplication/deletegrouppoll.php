<?php
require_once 'storage.php';
require_once 'userstorage.php';
require_once 'auth.php';
require_once 'groupstorage.php';
require_once 'functions.php';


session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);


if (!$auth->is_authenticated()) {
  redirect("login.php");
}


$id = $_GET["id"];

$poll_storage = new GroupStorage();
$poll_storage->delete($id);

redirect("index.php");