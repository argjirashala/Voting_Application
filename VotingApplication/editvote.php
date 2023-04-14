<?php

require_once 'functions.php';
require_once 'pollstorage.php';
require_once 'userstorage.php';
require_once 'votestorage.php';
require_once 'storage.php';
require_once 'auth.php';


session_start();

$user_storage = new UserStorage();
$auth = new Auth($user_storage);

if (!$auth->is_authenticated()) {
    redirect("login.php");
}


$votes_storage = new VoteStorage();
$allvotes = $votes_storage->findAll();

$poll_storage = new POllStorage();
$getpolls = $poll_storage->findAll();
$polls = json_read("polls.json");
$getVotes = json_read("votes.json");


$pollId = $_GET['id'];

if(post_exists('submit')){
        foreach($allvotes as $vote){
            if($vote['pollId'] == $pollId){
                if($vote['who voted'] === $auth->authenticated_user()['username']){
                    if(isset($_POST['option'])){
                        $votes_storage->delete($vote['id']);
                        $user = $auth->authenticated_user()['username'];
                        $votes_storage->add(["who voted" => "${user}", "optionvotedmul" => $_POST['option'], "pollId" => "$_GET[id]"]);
                    }
                    
                    if(post_exists('opt')){
                        $votes_storage->delete($vote['id']);
                        $user = $auth->authenticated_user()['username'];
                        $votes_storage->add(["who voted" => "${user}", "optionvotedone" => $_POST['opt'], "pollId" => "$_GET[id]"]);
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
                $output2 = 'Vote edited successfully!';
                echo '<a href="index.php"><button>Go to Main Page</button></a>';
            }
        }
    }
}




foreach($polls as $poll){
    if($poll->id == $pollId){
        if($poll->multiple_options === "yes"){
            if(post_exists('submit') && count($_POST['option'] ?? []) == 0){
                $output = 'You have to choose at least one option!';
            }else if(post_exists('submit') && count($_POST['option']) > 0){
                $output2 = 'Vote edited successfully!';
                echo '<a href="index.php"><button>Go to Main Page</button></a>';
            }
        }
    }
}



// if(isset($_POST['option'])){
//     $user = $auth->authenticated_user()['username'];
//     $votes_storage->add(["who voted" => "${user}", "optionvotedmul" => $_POST['option'], "pollId" => "$_GET[id]"]);
// }


// if(post_exists('opt')){
//     $user = $auth->authenticated_user()['username'];
//     $votes_storage->add(["who voted" => "${user}", "optionvotedone" => $_POST['opt'], "pollId" => "$_GET[id]"]);
// }







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
        <!-- <?php if(($poll->deadline < date("Y-m-d")) && $_GET['id'] == $poll->id) : ?>
            <h2>The poll is expired here are the results</h2>
            <?php if($poll->multiple_options === "no") : ?>
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
                            <?= $option . " "?>
                <?php endforeach ?>
                <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php endif ?> -->
        
        
        

      </form>
   
    <?php endforeach ?>
    <?php endif ?>
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>