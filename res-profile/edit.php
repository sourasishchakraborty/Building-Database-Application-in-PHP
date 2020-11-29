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
if(isset($_POST['save']) && isset($_GET['profile_id'])){

    if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
        if(strlen($_POST['first_name'] )< 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary'])< 1){

            $_SESSION['error'] = "All values are required";
            header("Location: index.php");
            return;
        }
        elseif (strpos($_POST['email'], "@")===false){
            $_SESSION['error']="Bad Email Address";

            //return;
        }else{
            $stmt = $pdo->prepare('UPDATE Profile SET first_name = :first_name, last_name = :last_name,email=:email,headline=:headline,summary=:summary
            WHERE profile_id = :profile_id');

            $stmt->execute(array(
                    ':first_name' => htmlentities($_POST['first_name']),
                    ':last_name' => htmlentities($_POST['last_name']),
                    ':email' => htmlentities($_POST['email']),
                    ':headline' => htmlentities($_POST['headline']),
                    ':summary' => htmlentities($_POST['summary']),
                    ':profile_id' => htmlentities($_GET['profile_id']))
            );

            $_SESSION['success']="Record Updated";
            header('Location: index.php');
            return;
        }
    }

}




if(!isset($_GET['profile_id'])){
    $_SESSION['error'] = "Missing user_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(
    ":xyz" => $_GET['profile_id']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
    $_SESSION['error'] = "Bad value for profile id";
    header('Location: index.php');
    return;
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
    <h1>Editing profile for UMSI</h1>

    <?php
    if(isset($_SESSION['error'])){
        echo '<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>';
        unset($_SESSION['error']);

    }


    ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60" value="<?php echo $row['first_name']?>"></p>
        <p>Last Name: <input type="text" name="last_name" size="60" value="<?php echo $row['last_name']?>"></p>
        <p>Email: <input type="text" name="email" size="40" value="<?php echo $row['email']?>"></p>
        <p>Headline:<br><input type="text" name="headline" size="80" value="<?php echo $row['headline']?>"></p>
        <p>Summary:<br><textarea name="summary" rows="8" cols="80" ><?php echo $row['summary']?></textarea></p>
        <input type="submit" value="Save" name="save">
        <input type="submit" name="cancel" value="Cancel">

    </form>

</div>
</body>
</html>

