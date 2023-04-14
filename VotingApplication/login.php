<?php
require_once('storage.php');
require_once('userstorage.php');
require_once('auth.php');
require_once('functions.php');

function validate($post, &$data, &$errors) {
  if (!isset($post["username"])) {
    $errors["username"] = "Username not set!";
  }
  else if (trim($post["username"]) === "") {
    $errors["username"] = "Username is required!";
  }
  else {
    $data["username"] = $post["username"];
  }

  if (!isset($post["password"])) {
    $errors["password"] = "Password does not exist!";
  }
  else if (trim($post["password"]) === "") {
    $errors["password"] = "Password is required!";
  }else {
    $data["password"] = $post["password"];
  }

  return count($errors) === 0;
}

// main
session_start();
//print_r($_SESSION);
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Login error";
    } else {
      $auth->login($auth_user);
      redirect('index.php');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>
  <?php if (isset($errors['global'])) : ?>
    <span style="color: red;"><?= $errors['global'] ?></span>
    <a href="register.php"><button>Register</button></a> <br> <br>
  <?php endif; ?>
  <form action="" method="POST" novalidate>
    <div>
      <label for="username">Username: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
      <?php if (isset($errors['username'])) : ?>
        <span style="color: red;"><?= $errors['username'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="password">Password: </label><br>
      <input type="password" name="password" id="password" value="<?= $_POST['password'] ?? "" ?>">
      <?php if (isset($errors['password'])) : ?>
        <span style="color:red;"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <a href="index.php"><button type="submit">Login</button></a>
    </div>
  </form>
</body>
</html>