<?php
session_start();
if(!isset($_SESSION['name'])){
    die('Not logged in');
}

require_once "pdo.php";

if (isset($_POST['cancel']) && $_POST['cancel']=='Cancel') {
    header('Location: view.php');
    return;
}
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])){
    if(!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])){
        //$failure = 'Mileage and year must be numeric';
        $_SESSION['error']="Mileage and year must be numeric";
        header('Location: add.php');
        return;
    } elseif (strlen($_POST['make']) < 1){
        //$failure = 'Make is required';
        $_SESSION['error']='Make is required';
        header('Location: add.php');
        return;
    } else{

        $stmt = $pdo->prepare('INSERT INTO autos
  (make, year, mileage) VALUES ( :mk, :yr, :mi)');
        $stmt->execute(array(
                ':mk' => $_POST['make'],
                ':yr' => $_POST['year'],
                ':mi' => $_POST['mileage'])
        );
        //$success = 'Record inserted';
        $_SESSION['success']="Record inserted";
        header("Location: view.php");
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
    <h1>Tracking Autos for <?php echo $_SESSION['name'] ?></h1>
    <?php
    if(isset($_SESSION['error'])){
        echo('<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>');
        unset($_SESSION['error']);
    }

    ?>
    <form method="post">
        <p>Make: <input type="text" name="make" size="60"></p>
        <p>Year: <input type="text" name="year"></p>
        <p>Mileage: <input type="text" name="mileage"></p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">

    </form>


</div>
</body>
</html>
