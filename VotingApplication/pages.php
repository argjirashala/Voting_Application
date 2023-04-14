<?php
 function create_poll($come_back_here){ ?>
    <h1>Poll Creation</h1>
    <form method="POST" action="create.php" novalidate>
        Poll text: <input name="polltext"> <br> <br>
        Options(one per line): <br><textarea name="options" ></textarea> <br> 
        Multiple choices allowed: <br> <input type="radio" id="yes" name="multiple_options" value="yes">Yes<br>
                                  <input type="radio" id="no" name="multiple_options" value="no">No<br>
        Voting Deadline: <input type="date" name="deadline"> <br>
        Creation time: <input type="date" name="creationtime"> <br>
        <input type="hidden" name="come_back_here" value="<?=$come_back_here?>">
        <input type="submit" value="Submit"><br>
    </form>
<?php } //end create_poll ?>

<?php
 function register($come_back_here){ ?>
    <h1>Register</h1>
    <form method="POST" action="register.php" novalidate>
        Username: <input name="username"> <br> <br>
        Password: <input type="password" name="password1"> <br> <br>
        Confirm Password: <input type="password" name="password2"> <br> <br>
        <input type="hidden" name="come_back_here" value="<?=$come_back_here?>">
        <input type="submit" value="Register"><br>
    </form>
<?php } //end register ?>

<?php
 function edit_poll($come_back_here){ ?>
    <h1>Edit Poll</h1>
    <form method="POST" action="editpoll.php" novalidate>
        Poll text: <input name="polltext"> <br> <br>
        Options(one per line): <br><textarea name="options" ></textarea> <br> 
        Multiple choices allowed: <br> <input type="radio" id="yes" name="multiple_options" value="yes">Yes<br>
                                  <input type="radio" id="no" name="multiple_options" value="no">No<br>
        Voting Deadline: <input type="date" name="deadline"> <br>
        Creation time: <input type="date" name="creationtime"> <br>
        <input type="hidden" name="come_back_here" value="<?=$come_back_here?>">
        <input type="submit" value="Submit"><br>
    </form>
<?php } //end edit_poll ?>







