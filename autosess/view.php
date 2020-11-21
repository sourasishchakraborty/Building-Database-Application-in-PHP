<?php
session_start();



if(!isset($_SESSION['name'])){
    die('Not logged in');
}
require_once "pdo.php";


$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
</head>
<body>
<div class="container">
    <h1>Tracking Autos for <?php echo $_SESSION['name'] ?></h1>
    <?php
    if(isset($_SESSION['success'])){
        echo('<p style="color: green">'. htmlentities($_SESSION['success']) .'</p>');
        unset($_SESSION['success']);

    }

    ?>

    <h2>Automobiles</h2>
    <?php
    foreach ($rows as $row){
        echo '<li>';
        echo htmlentities($row['make']. ' ' . $row['year'] .' / '. $row['mileage']);
        echo '</li><br>';
    };

    ?>
    <p>
        <a href="add.php">Add New</a> |
        <a href="logout.php">Logout</a>
    </p>


</div>
</body>
</html>
