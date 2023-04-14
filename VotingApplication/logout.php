<?php

require_once('storage.php');
require_once('userstorage.php');
require_once('auth.php');
require_once('functions.php');

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$auth->logout();
redirect("index.php");
