<?php
session_start();
require_once "pdo.php";

if(!isset($_GET['profile_id'])){
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(
    ":xyz" => $_GET['profile_id']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
    $_SESSION['error'] = "Bad value for profile_id";
    header('Location: index.php');
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sourasish Chakraborty</title>
    <?php require_once "bootstrap.php";?>
</head>
<body>
<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo $row['first_name']?></p>
    <p>Last Name: <?php echo $row['last_name']?></p>
    <p>Email: <?php echo $row['email']?></p>
    <p>Headline: <?php echo $row['headline']?></p>
    <p>Summary: <?php echo $row['summary']?></p>
    <a href="index.php">Done</a>
</div>
</body>
</html>
