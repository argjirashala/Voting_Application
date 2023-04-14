<?php
require_once 'functions.php';
require_once 'groupstorage.php';

function validate($post, &$data, &$errors) {
    // $data = $post;
    // Validation
    if (!isset($post["polltext"])) {
      $errors["polltext"] = "Poll text does not exist!";
    }
    else if (trim($post["polltext"]) === "") {
      $errors["polltext"] = "Poll text is required!";
    }
    else {
      $data["polltext"] = $post["polltext"];
    }
  
    if (!isset($post["options"])) {
      $errors["options"] = "Options do not exist!";
    }
    else if (trim($post["options"]) === "") {
      $errors["options"] = "Options are required!";
    }
    else {
      $options = explode("\n", $post["options"]);
      $data["options"] = $options;
    }
  
    if(!isset($post["multiple_options"])){
      $errors["multiple_options"] = "Multiple options does not exist!";
    }
    else if(trim($post["multiple_options"]) === ""){
      $errors["multiple_options"] = "Multiple options are required!";
    }
    else{
      $data["multiple_options"] = $post["multiple_options"];
    }
  
      if(!isset($post["deadline"])){
          $errors["deadline"] = "Deadline does not exist!";
      }
      else if(trim($post["deadline"]) === ""){
          $errors["deadline"] = "Deadline is required!";
      }
      else{
          $deadline = $post["deadline"];
          if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $deadline)) {
              $errors['deadline'] = "Invalid date format";
          }
          $data["deadline"] = $deadline;
        }
  
      if(!isset($post["creationtime"])){
          $errors["creationtime"] = "Creation time does not exist!";
      }
      else if(trim($post["creationtime"]) === ""){
          $data["creationtime"] = date("Y-m-d");
      }
      else{
          $creationtime = $post["creationtime"];
          if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $creationtime)) {
            $errors['creationtime'] = "Invalid date format";
          }
          $data["creationtime"] = $creationtime;
      }
  
      if($post["deadline"] < $post["creationtime"]){
          $errors["deadline"] = "Deadline must be after creation time!";
      }

      if (!isset($post["group"])) {
        $errors["group"] = "Group do not exist!";
      }
      else if (empty($post["group"])) {
        $errors["group"] = "Group is is required!";
      }
      else {
        $data["group"] = $post["group"];
      }
  
    return count($errors) === 0;
  }

$users = json_read('users.json');
$data = [];
$errors = [];
session_start();
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
      $data["user_id"] = $_SESSION["user"]["id"];
      $group_poll_storage = new GroupStorage();
      $group_poll_storage->add($data);
      echo '<span style="color: green;">Group Poll created successfully</span> <br>';
  }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form method="POST"  novalidate>
        Poll text: <input name="polltext"> <br> <br>
        Options(one per line): <br><textarea name="options" ></textarea> <br> 
        Multiple choices allowed: <br> <input type="radio" id="yes" name="multiple_options" value="yes">Yes<br>
                                  <input type="radio" id="no" name="multiple_options" value="no">No<br>
        Voting Deadline: <input type="date" name="deadline"> <br>
        Creation time: <input type="date" name="creationtime"> <br>
        Choose users that can vote this poll <br>
        <?php foreach($users as $user) : ?>
                <input type="checkbox" name="group[]" value="<?= $user->username ?>">
                <label for="group[]"><?= $user->username ?></label> <br>
            <?php endforeach ?>
        <input type="submit" value="Submit"><br>
    </form>
    <?php if (isset($errors["polltext"])) : ?>
      <span style="color: red;"><?= $errors["polltext"] ?></span> <br>
    <?php endif ?>
    <?php if (isset($errors["options"])) : ?>
      <span style="color: red;"><?= $errors["options"] ?></span> <br>
    <?php endif ?>
    <?php if (isset($errors["multiple_options"])) : ?>
      <span style="color: red;"><?= $errors["multiple_options"] ?></span> <br>
    <?php endif ?>
    <?php if (isset($errors["deadline"])) : ?>
      <span style="color: red;"><?= $errors["deadline"] ?></span> <br>
    <?php endif ?>
    <?php if (isset($errors["creationtime"])) : ?>
      <span style="color: red;"><?= $errors["creationtime"] ?></span> <br>
    <?php endif ?>
    <?php if (isset($errors["group"])) : ?>
      <span style="color: red;"><?= $errors["group"] ?></span> <br>
    <?php endif ?>

    <a href="index.php"><button>Go to Main Page</button></a> <br>

    <a href="logout.php"><button>Logout</button></a>
</body>
</html>