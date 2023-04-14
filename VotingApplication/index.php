<?php

require_once 'functions.php';
require_once 'pollstorage.php';
require_once 'votestorage.php';
require_once 'userstorage.php';
require_once 'groupstorage.php';
require_once 'storage.php';
require_once 'auth.php';
require_once 'groupvotestorage.php';



session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);



$poll_storage = new POllStorage();
$getpolls = $poll_storage->findAll();
$polls = json_read("polls.json");

$group_poll_storage = new GroupStorage();
$groupPolls = $group_poll_storage->findAll();

function sortByDate($a, $b){
    if ($a["creationtime"] > $b["creationtime"]) {
        return -1;
    } else if ($a["creationtime"] < $b["creationtime"]) {
        return 1;
    } else {
        return 0; 
    }
}

usort($getpolls,"sortByDate");
usort($groupPolls,"sortByDate");

$votes_storage = new VoteStorage();
$allvotes = $votes_storage->findAll();
// print_r($_SESSION["user"]["id"]);
$groupvotes_storage = new GoupVoteStorage();
$allgroupvotes = $groupvotes_storage->findAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
</head>
<body>
    <h1>Main Page</h1>
    <?php if (!$auth->is_authenticated()) : ?>
      <span>If you haven't registered yet: </span> <a href="register.php"><button>Register</button></a> <br>
       <span>If you already have an account: </span> <a href="login.php"><button>Login</button></a> <br> 
    <?php endif ?>
    <h2>Voting Web Application</h2>
    <p>
      This is a voting web application. Only the admin user can create a poll. <br>
      When creating a poll there are two possibilities: <br>
      1.Create a poll where every logged in user can vote. <br>
      2.Create a group poll where only the users in the group can vote. <br>
      Every user can edit his/her vote. <br>
      Only the admin user can delete and edit a poll. <br>
    </p>
    <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="create.php"><button>Create New Poll</button> </a> <br>
          <a href="grouppoll.php"><button>Create Group Poll</button></a>
      <?php endif ?>
    <?php endif ?>
    <section>
    <h2>Available polls</h2>
    <?php foreach($getpolls as  $poll) : ?>
      <br>
      <?php if($poll["deadline"] > date("Y-m-d")) : ?>  
        Poll text: <?= $poll["polltext"] ?> <br>
        Poll id: <?=$poll["id"] ?> <br>
        Deadline:<?= $poll["deadline"] ?> <br>
        Time of creation: <?= $poll["creationtime"] ?> <br>
      
          <a href="voting.php?id=<?=$poll["id"]?>"><button>Vote</button></a> 
          <a href="editvote.php?id=<?=$poll["id"]?>"><button>Edit Vote</button></a>
          <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="delete.php?id=<?= $poll["id"] ?>"><button>Delete Poll</button> </a>&nbsp;
      <?php endif ?>
      <?php endif ?>
      <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="editpoll.php?id=<?= $poll["id"]?>"><button>Edit Poll</button> </a> <br>
      <?php endif ?>
      <?php endif ?>
      
          
      <?php endif ?>
      <br>
    <?php endforeach ?>
    
    <?php if($auth->is_authenticated()) : ?>
      <h3>Group Polls</h3>
        <br>
          <?php foreach($groupPolls as  $gpoll) : ?>
            <?php if(in_array($auth->authenticated_user()['username'] , $gpoll["group"]) || $auth->authenticated_user()['username'] === 'admin') : ?>
              
            <?php if($gpoll["deadline"] > date("Y-m-d")) : ?>

          Poll text: <?= $gpoll["polltext"] ?> <br>
           Poll id: <?=$gpoll["id"] ?> <br>
        Deadline:<?= $gpoll["deadline"] ?> <br>
        Time of creation: <?= $gpoll["creationtime"] ?>
        <br>
        <a href="votegrop.php?id=<?=$gpoll["id"]?>"><button>Vote</button></a> 
          <a href="editgroupvote.php?id=<?=$gpoll["id"]?>"><button>Edit Vote</button></a>
          <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="deletegrouppoll.php?id=<?=$gpoll["id"] ?>"><button>Delete Poll</button> </a>&nbsp;
      <?php endif ?>
      <?php endif ?>
      <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="editgrouppoll.php?id=<?= $gpoll["id"]?>"><button>Edit Poll</button> </a> <br>
      <?php endif ?>
      <?php endif ?>
        <?php endif ?>
        <?php endif ?>
        <br>
        <?php endforeach ?>
      
          
      <?php endif ?>

    </section>
    <section>
    <h2>Expired polls</h2>
    <?php foreach($getpolls as $poll) : ?>
      <br>
      <?php if($poll["deadline"] < date("Y-m-d")) : ?>  
        Poll text: <?= $poll["polltext"] ?> <br>
        Poll id: <?=$poll["id"] ?> <br>
        Deadline:<?= $poll["deadline"] ?> <br>
        Time of creation: <?= $poll["creationtime"] ?> <br>
            <p> <b>The results for this poll</b> </p>
            <?php if($poll["multiple_options"] === "no") : ?>
              <hr>
        <?php foreach($allvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $poll["id"]) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                    <?= $currVote["optionvotedone"] ?? ""?><br>
                    <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php if($poll["multiple_options"] === "yes") : ?>
            <hr>
        <?php foreach($allvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $poll["id"]) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                <?php foreach($currVote["optionvotedmul"] as $option) : ?>
                            <?= $option . "  "?>
                <?php endforeach ?>
                <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="delete.php?id=<?= $poll["id"] ?>"><button>Delete Poll</button> <br></a>
      <?php endif ?>
      <?php endif ?>
      <?php endif ?>
    <?php endforeach ?>
    <h3>Group Polls</h3>

    <?php if($auth->is_authenticated()) : ?>
        <br>
          <?php foreach($groupPolls as  $gpoll) : ?>
            <?php if(in_array($auth->authenticated_user()['username'] , $gpoll["group"]) || $auth->authenticated_user()['username'] === 'admin') : ?>
              
            <?php if($gpoll["deadline"] < date("Y-m-d")) : ?>

          Poll text: <?= $gpoll["polltext"] ?> <br>
           Poll id: <?=$gpoll["id"] ?> <br>
        Deadline:<?= $gpoll["deadline"] ?> <br>
        Time of creation: <?= $gpoll["creationtime"] ?>
        <p> <b>The results for this poll</b> </p>
            <?php if($gpoll["multiple_options"] === "no") : ?>
              <hr>
        <?php foreach($allgroupvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $gpoll["id"]) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                    <?= $currVote["optionvotedone"] ?? ""?><br>
                    <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php if($gpoll["multiple_options"] === "yes") : ?>
            <hr>
        <?php foreach($allgroupvotes as $currVote) : ?>
            <?php if($currVote["pollId"] == $gpoll["id"]) : ?>
                Who voted: <?= $currVote["who voted"] ?? "" ?> <br>
                Option voted: 
                <?php foreach($currVote["optionvotedmul"] as $option) : ?>
                            <?= $option . "  "?>
                <?php endforeach ?>
                <hr>
            <?php endif ?>
        <?php endforeach ?>
        <?php endif ?>
        <?php if ($auth->is_authenticated()) : ?>
        <?php if ($auth->authenticated_user()['username'] === 'admin') : ?>
          <a href="deletegrouppoll.php?id=<?=$gpoll["id"] ?>"><button>Delete Poll</button> </a>&nbsp;
      <?php endif ?>
      <?php endif ?>
        <?php endif ?>
        <?php endif ?>
        <?php endforeach ?>
      <?php endif ?>
    </section>
    <br>
 
  <a href="logout.php"><button>Logout</button></a>
</body>
</html>