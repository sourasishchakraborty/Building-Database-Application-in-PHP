<?php
    session_start();
    if(!isset($_SESSION['name'])){
        die('Not logged in');
    }
    require_once "pdo.php";
    if(isset($_POST['cancel'])){
        header("Location: index.php");
        return;
    }

    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
        if(strlen($_POST['first_name'] )< 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary'])< 1){

            $_SESSION['error'] = "All values are required";
            header("Location: add.php");
            return;
        }
        elseif (strpos($_POST['email'], "@")===false){
            $_SESSION['error']="Email must have an at-sign (@)";
            header("Location: add.php");
            return;
        }else{
            $stmt = $pdo->prepare('INSERT INTO Profile
  (user_id, first_name, last_name, email, headline, summary)
  VALUES ( :uid, :fn, :ln, :em, :he, :su)');

            $stmt->execute(array(
                    ':uid' => $_SESSION['user_id'],
                    ':fn' => htmlentities($_POST['first_name']) ,
                    ':ln' => htmlentities($_POST['last_name']),
                    ':em' => htmlentities($_POST['email']),
                    ':he' => htmlentities($_POST['headline']),
                    ':su' => htmlentities($_POST['summary']))
            );

            $_SESSION['success'] = "Record added";
            header("Location: index.php");
            return;

        }
    }

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
</head>
<body>
<div class="container">
    <h1>Adding profile for UMSI</h1>

    <?php
        if(isset($_SESSION['error'])){
            echo '<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>';
            unset($_SESSION['error']);

        }


    ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60"></p>
        <p>Last Name: <input type="text" name="last_name" size="60"></p>
        <p>Email: <input type="text" name="email" size="40"></p>
        <p>Headline:<br><input type="text" name="headline" size="80"></p>
        <p>Summary:<br><textarea name="summary" rows="8" cols="80"></textarea></p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="cancel">

    </form>

</div>
</body>
</html>
