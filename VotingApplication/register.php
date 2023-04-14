<?php

require_once 'pages.php';
require_once 'functions.php';
require_once 'userstorage.php';
require_once 'auth.php';


  function validate($post, &$data, &$errors) {
    // $data = $post;
    // Validation
    if (!isset($post["username"])) {
      $errors["username"] = "Username not set!";
    }
    else if (trim($post["username"]) === "") {
      $errors["username"] = "Username is required!";
    }
    else {
      $data["username"] = $post["username"];
    }
  
    if (!isset($post["email"])) {
      $errors["email"] = "Email does not exist!";
    }
    else if (trim($post["email"]) === "") {
      $errors["email"] = "Email is required!";
    }
    else {
      $data["email"] = $post["email"];
      if(filter_var($post["email"], FILTER_VALIDATE_EMAIL) === false) {
        $errors["email"] = "Wrong email format!";
      }
    }
  
    if (!isset($post["password"])) {
      $errors["password"] = "Password does not exist!";
    }
    else if (trim($post["password"]) === "") {
      $errors["password"] = "Password is required!";
    }else {
      $data["password"] = $post["password"];
    }

    if (!isset($post["confpassword"])) {
      $errors["confpassword"] = " Confirm Password does not exist!";
    }
    else if (trim($post["confpassword"]) === "") {
      $errors["confpassword"] = "Confirm Password is required!";
    }else {
      $data["confpassword"] = $post["confpassword"];
    }

    if($post["password"] !== $post["confpassword"]){
      $errors["notmatch"] = "Passwords do not match!";
    }
  
    return count($errors) === 0;
  }
  
  // main
  $user_storage = new UserStorage();
  $auth = new Auth($user_storage);
  $errors = [];
  $data = [];
  if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
      if ($auth->user_exists($data['username'])) {
        $errors['global'] = "User already exists";
      } else {
        $auth->register($data);
        redirect('login.php');
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
  <title>Registration</title>
</head>
<body>
  <h1>Registration</h1>

  <?php if (isset($errors['global'])) : ?>
    <span style="color: red;"><?= $errors['global'] ?></span>
  <?php endif; ?>
  <form action="" method="post">
    <div>
      <label for="username">Username: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
      <?php if (isset($errors['username'])) : ?>
        <span style="color: red;"><?= $errors['username'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="email">Email: </label><br>
      <input type="text" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>">
      <?php if (isset($errors['email'])) : ?>
        <span style="color: red;"><?= $errors['email'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="password">Password: </label><br>
      <input type="password" name="password" id="password1" value="<?= $_POST['password'] ?? "" ?>">
      <?php if (isset($errors['password'])) : ?>
        <span style="color: red;"><?= $errors['password'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <label for="confpassword">Confirm Password: </label><br>
      <input type="password" name="confpassword" id="password2" value="<?= $_POST['confpassword'] ?? "" ?>">
      <?php if (isset($errors['confpassword'])) : ?>
        <span style="color: red;"><?= $errors['confpassword'] ?></span>
      <?php endif; ?>
      <?php if (isset($errors['notmatch'])) : ?>
        <span style="color: red;"><?= $errors['notmatch'] ?></span>
      <?php endif; ?>
    </div>
    <div>
      <button type="submit">Register</button>
    </div>
  </form>
</body>
</html>