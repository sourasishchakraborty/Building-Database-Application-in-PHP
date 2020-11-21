<?php

session_start();

if(!isset($_SESSION['name'])){
    die('ACCESS DENIED');
}
require_once "pdo.php";

if (isset($_POST['cancel']) && $_POST['cancel']=='Cancel') {
    header('Location: index.php');
    return;
}

if(isset($_POST['delete']) && $_POST['autos_id']){
    $sql = "DELETE FROM autos WHERE autos_id = :zip";
    $stmt1 = $pdo->prepare($sql);
    $stmt1->execute(array(':zip' => $_POST['autos_id']));
    $_SESSION['success'] = "Record deleted";
    header("Location: index.php");
    return;
}


$stmt = $pdo->prepare("SELECT * FROM autos WHERE autos_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['autos_id']));
$rows = $stmt->fetch(PDO::FETCH_ASSOC);

if(!isset($_GET['autos_id'])){
    $_SESSION['error']="Missing autos_id";
    header("Location: index.php");
    return;
}
if($rows === false){
    $_SESSION['error']="Bad value for autos_id";
    header("Location: index.php");
    return;
}
$make = htmlentities($rows['make']);
$autos_id = htmlentities($rows['autos_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
</head>
<body>
<div class="container">

    <p>Confirm: Deleting <?= htmlentities($rows['make']) ?></p>

    <form method="post">
        <input type="hidden" name="autos_id" value="<?= $rows['autos_id'] ?>">
        <input type="submit" value="Delete" name="delete">
        <a href="index.php">Cancel</a>
    </form>



</div>
</body>
</html>

