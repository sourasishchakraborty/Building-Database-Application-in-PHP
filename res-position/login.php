<?php
session_start();
require_once "pdo.php";
if(isset($_SESSION['user_id'])){
    header("Location: index.php");
    return;
}
if(isset($_POST['cancel'])){
    header("Location: index.php");
    return;
}



$salt = 'XyZzy12*_';
if(isset($_POST['pass']) && isset($_POST['email'])){
    $check = hash('md5',$salt . $_POST['pass']);
    //echo $check;

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array(
            ':em' =>$_POST['email'],
            ':pw'=>$check
    ));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row!==false){
        //echo "hi";
        $_SESSION['name']=$row['name'];
        $_SESSION['user_id'] =$row['user_id'];
        error_log("Login success ".$_POST['name']);


        header("Location: index.php");
        return;
    }else{
        $_SESSION['error']="Incorrect Email ID or password. Try Again";
        error_log("Login fail ".$_POST['name']);
        header("Location: login.php");
        return;
    }
}
?>

<script>
    function doValidate() {

        console.log('Validating...');

        try {
            em =document.getElementById('nam').value;
            pw = document.getElementById('id_1723').value;

            //console.log("Validating pw="+pw);

            if(em == null || em ==""){
                alert("Both the fields must be filled out");

                return false;
            }
            if (pw == null || pw == "") {

                alert("Both the fields must be filled out");

                return false;

            }

            return true;

        } catch(e) {

            return false;

        }

        return false;

    }
</script>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
</head>
<body>
<div class="container">
    <h1>Please Log In</h1>
    <?php

    if(isset($_SESSION['error'])){
        echo ('<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>');
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <label for="nam">User Name</label>
        <input type="text" name="email" id="nam"><br/>
        <label for="id_1723">Password</label>
        <input type="text" name="pass" id="id_1723"><br/>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>
    <p>
        For a password hint, view source and find a password hint
        in the HTML comments.
        <!-- Hint: The password is the three character of the language we've learnt in this class (all lower case) followed by 123. -->
    </p>
</div>
</body>
</html>
