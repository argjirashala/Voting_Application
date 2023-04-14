<?php

require_once 'functions.php';
require_once 'pollstorage.php';
require_once 'groupstorage.php';
require_once 'userstorage.php';
require_once 'groupvotestorage.php';
require_once 'storage.php';
require_once 'auth.php';


session_start();
//print_r($_SESSION);

$user_storage = new UserStorage();
$auth = new Auth($user_storage);

if (!$auth->is_authenticated()) {
    redirect("login.php");
}

// if ($auth->user_exists($data['username'])) {
//     $errors['global'] = "You have already voted!";
// }



$votes_storage = new GoupVoteStorage();
$allvotes = $votes_storage->findAll();

$group_storage = new GroupStorage();
$getpolls = $group_storage->findAll();
$polls = json_read("grouppolls.json");


$pollId = $_GET['id'];
// $vote = $_POST['option'] ?? null;
// echo('<hr>');

// $VoteError = null;
// $urVote = null;
// if (isset($_POST['submit'])) {
//     if ($_POST['opt'] == '') {
//         $VoteError = "You have to choose an option!";
//     } else {
//         $urVote = $_POST['opt'];
//     }
// }


// foreach($polls as $poll){
//     if($poll->id == $pollId){
//         if($poll->multiple_options === "no"){
//         foreach($allvotes as $vote){
//             if($vote["pollId"] == $pollId){
//                 if($vote["who voted"] == $auth->authenticated_user()['username']){
//                     $votes_storage->update($vote['id'], $vote);
//                 }
//             }
//         }
//     }
// }

// }

// foreach($polls as $poll){
//     if($poll->id == $pollId){
//         if($poll->multiple_options === "yes"){
//         foreach($allvotes as $vote){
//             if($vote["pollId"] == $pollId){
//                 if($vote["who voted"] == $auth->authenticated_user()['username']){
//                     $votes_storage->update($vote['id'], $vote);
//                 }
//             }
//         }
//     }
// }

// }

// function changeVote(){
//     global $pollId;
//     global $auth;
//     global $allvotes;
//     global $votes_storage;
//     foreach($allvotes as $vote){
//         if($vote["pollId"] == $pollId){
//             if($vote["who voted"] == $auth->authenticated_user()['username']){
//                 $votes_storage->update($vote['id'], $vote);
//             }
//         }
//     }
// }

foreach($polls as $poll){
    if($poll->id == $pollId){
        foreach($allvotes as $vote){
            if($vote["pollId"] == $pollId){
                if($vote["who voted"] == $auth->authenticated_user()['username']){
                    $errors['global'] = "You have already voted for this poll! But you can edit it with Edit button on main page!";
                }
            }
        }
    }
}
foreach($polls as $poll){
    if($poll->id == $pollId){
        if($poll->multiple_options === "no"){
            if(post_exists('submit') && !post_exists('opt')){
                $output = 'You have to choose one option!';
            }else if(post_exists('submit') && post_exists('opt')){
                $output2 = 'Vote submitted successfully!';
                echo '<a href="index.php"><button>Go to Main Page</button></a>';
            }
        }
    }
}
// if(post_exists('submit') && !post_exists('opt')){
//     $output = 'You have to choose an option!';
// }else if(post_exists('submit') && post_exists('opt') && !post_exists('option')){
//     $output = 'Vote submitted successfully!';
// }

foreach($polls as $poll){
    if($poll->id == $pollId){
        if($poll->multiple_options === "yes"){
            if(post_exists('submit') && count($_POST['option'] ?? []) == 0){
                $output = 'You have to choose at least one option!';
            }else if(post_exists('submit') && count($_POST['option']) > 0){
                $output2 = 'Vote submitted successfully!';
                echo '<a href="index.php"><button>Go to Main Page</button></a>';
            }
        }
    }
}

// if(post_exists('submit') && !post_exists('option')){
//     $output2 = 'You have to choose at least one option!';
// }else if(post_exists('submit') && post_exists('opt') && !post_exists('opt')){
//     $output2 = 'Vote submitted successfully!';
// }



if(isset($_POST['option'])){
    $user = $auth->authenticated_user()['username'];
    $votes_storage->add(["who voted" => "${user}", "optionvotedmul" => $_POST['option'], "pollId" => "$_GET[id]"]);
    // redirect("voting.php?id=${pollId}");
}

// if(isset($_POST['opt'])){
//     $user = $auth->authenticated_user()['username'];
//     $votes_storage->add(["who voted" => "${user}", "optionvotedone" => $_POST['opt'], "pollId" => "$_GET[id]"]);
//     redirect("voting.php?id=${pollId}");
    
// }

if(post_exists('opt')){
    $user = $auth->authenticated_user()['username'];
    $votes_storage->add(["who voted" => "${user}", "optionvotedone" => $_POST['opt'], "pollId" => "$_GET[id]"]);
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Page</title>
</head>
<body>
    <h1>Voting Page</h1>
    <!-- <a href="create.php"><button>Create New Poll</button> </a> <br> -->
    <?php if(isset($errors['global'])) : ?>
        <span style="color:red;"><?= $errors['global'] ?></span>
        <a href="index.php"><button>Go back to main page</button> </a> <br>
        <?php else : ?>
            
    <?php foreach($polls as $poll) : ?>
      <form action="" method="POST">
      <?php if(($poll->deadline > date("Y-m-d")) && $_GET['id'] == $poll->id) : ?> 
        <br>
        Poll text: <?= $poll->polltext ?> <br>
        Choose options: <br>
        <?php if($poll->multiple_options === "yes") : ?>
            <?php $options = $poll->options?>
            <?php foreach($options as $option) : ?>
                <input type="checkbox" name="option[]" value="<?= $option ?>">
                <label for="option[]"><?= $option ?></label> <br>
            <?php endforeach ?>
        <?php endif ?>
        <?php if($poll->multiple_options === "no") : ?>
            <?php $options = $poll->options?>
            <?php foreach($options as $option) : ?>
                <input type="radio" name="opt" value="<?= $option ?>">
                <label for="opt"><?= $option ?></label> <br>
            <?php endforeach ?>
        <?php endif ?>
        <!-- <tr>Options: <?= implode(",", $poll->options) ?></tr> <br> -->
        Deadlinee:<?= $poll->deadline ?> <br>
        Time of creation: <?= $poll->creationtime ?> <br> 
        <input type="submit" name="submit" value="Save Vote">
        <br>
        <?php if (isset($output)) : ?>
                <span style="color: red;"><?= $output ?></span>
        <?php endif ?>
        <?php if (isset($output2)) : ?>
                <span style="color: green;"><?= $output2 ?></span>
                <!-- <a href="index.php"><button>Back to main page</button></a> -->
        <?php endif ?>
        <br>
        <!-- <?php if($poll->multiple_options === "no") : ?>
        <?php foreach($allvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $poll->id) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                <?= $currVote["optionvotedone"] ?? ""?><br>
                            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php if($poll->multiple_options === "yes") : ?>
            <hr>
        <?php foreach($allvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $poll->id) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                <?php foreach($currVote["optionvotedmul"] as $option) : ?>
                    <?= $option . " " ?>
                <?php endforeach ?>
                <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
         -->
        <?php endif ?>
        
        
        
        

      </form>
   
    <?php endforeach ?>
    <?php endif ?>
    
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>