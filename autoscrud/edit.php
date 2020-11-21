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

if (isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year']) && isset($_POST['mileage'])){
    if (strlen($_POST['make']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['model']) < 1 || strlen($_POST['mileage']) < 1){
        //$failure = 'Make is required';
        $_SESSION['error']='All values are required';
        //header('Location: index.php');
        //return;
    }
    elseif(!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])){
        //$failure = 'Mileage and year must be numeric';
        $_SESSION['error']="Mileage and year must be numeric";
        //header('Location: index.php');
        //return;
    } else{

        $stmt = $pdo->prepare('UPDATE autos SET make = :make, model = :model, year = :year,mileage=:mileage
            WHERE autos_id = :autos_id');
        $stmt->execute(array(
                ':make' => $_POST['make'],
                ':model' => $_POST['model'],
                ':year' => $_POST['year'],
                ':mileage' => $_POST['mileage'],
                ':autos_id' => $_POST['autos_id'])
        );
        //$success = 'Record inserted';
        $_SESSION['success']="Record updated";
        header("Location: index.php");
        return;
    }
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
$model = htmlentities($rows['model']);
$year = htmlentities($rows['year']);
$mileage = htmlentities($rows['mileage']);
$autos_id = htmlentities($rows['autos_id'])

?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
</head>
<body>
<div class="container">
    <h1>Tracking Automobiles for <?php echo $_SESSION['name'] ?></h1>
    <?php
    if(isset($_SESSION['error'])){
        echo('<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>');
        unset($_SESSION['error']);
    }

    ?>
    <form method="post">
        <p>Make: <input type="text" name="make" size="40" value="<?php echo $make; ?>"></p>
        <p>Model: <input type="text" name="model" size="40" value="<?php echo $model; ?>"></p>
        <p>Year: <input type="text" name="year" value="<?php echo $year; ?>"></p>
        <p>Mileage: <input type="text" name="mileage" value="<?php echo $mileage; ?>"></p>
        <input type="hidden" name="autos_id" value="<?php $autos_id; ?>">
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">

    </form>


</div>
</body>
</html>
