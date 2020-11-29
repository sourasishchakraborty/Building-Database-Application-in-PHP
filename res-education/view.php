<?php
session_start();
require_once "pdo.php";

if(!isset($_GET['profile_id'])){
    $_SESSION['error'] = "Missing profile_id";
    header("Location: index.php");
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(
    ":xyz" => $_GET['profile_id']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false){
    $_SESSION['error'] = "Bad value for profile_id";
    header('Location: index.php');
    return;
}

$stmt1 = $pdo->prepare("SELECT * FROM position where profile_id = :xyz");
$stmt1->execute(array(
    ":xyz" => $_GET['profile_id']
));
$row1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);


$stmt2 = $pdo->prepare("SELECT * FROM education JOIN institution ON education.institution_id = institution.institution_id WHERE profile_id = :xyz");
$stmt2->execute(array(
    ":xyz" => $_GET['profile_id']
));
$row2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Sourasish Chakraborty</title>
    <?php require_once "bootstrap.php";?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
    <h1>Profile information</h1>
    <p>First Name: <?php echo $row['first_name']?></p>
    <p>Last Name: <?php echo $row['last_name']?></p>
    <p>Email: <?php echo $row['email']?></p>
    <p>Headline: <?php echo $row['headline']?></p>
    <p>Summary: <?php echo $row['summary']?></p>
    <p>Education: <br> <ul>
        <?php
        foreach ($row2 as $r){
            echo '<li>'. $r['year'] .' : ' . $r['name'] .'</li>';
        }
        ?>
    </ul></p>
    <p>Position: <br> <ul>
        <?php
        foreach ($row1 as $r){
            echo '<li>'. $r['year'] .' : ' . $r['description'] .'</li>';
        }
        ?>
    </ul></p>
    <a href="index.php">Done</a>
</div>
</body>
</html>

