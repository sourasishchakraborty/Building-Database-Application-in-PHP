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
function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( !is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

$rank = 1;

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])){
    if(strlen($_POST['first_name'] )< 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary'])< 1){

        $_SESSION['error'] = "All values are required";
        header("Location: add.php");
        return;
    }
    elseif (strpos($_POST['email'], "@")===false){
        $_SESSION['error']="Email must have an at-sign (@)";


    }elseif (validatePos() !== true){
        $_SESSION['error'] = validatePos();
        //header("Location: add.php");
        //return;
    }
    else{
        $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :pid');

        $stmt->execute(array(
                ':pid' => $_GET['profile_id'],
                ':fn' => htmlentities($_POST['first_name']) ,
                ':ln' => htmlentities($_POST['last_name']),
                ':em' => htmlentities($_POST['email']),
                ':he' => htmlentities($_POST['headline']),
                ':su' => htmlentities($_POST['summary']))
        );
        $_SESSION['success'] = 'Record updated';

        $stmt2 = $pdo->prepare('DELETE FROM position WHERE profile_id =:pid');
        $stmt2->execute(array(
             ':pid'=>$_GET['profile_id']
        ));

        $profile_id = $_GET['profile_id'];
        print_r($profile_id);

        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            print_r($rank);
            $year = $_POST['year'.$i];
            print_r($year);

            $desc = $_POST['desc'.$i];
            print_r($desc);
            $stmt3 = $pdo->prepare('INSERT INTO position
    (profile_id, rank, year, description)
    VALUES ( :pid, :rank, :year, :desc)');

            $stmt3->execute(array(
                    ':pid' => $profile_id,
                    ':rank' => $rank,
                    ':year' => $year,
                    ':desc' => $desc)
            );

            $rank++;

        }

        $rank++;



        $_SESSION['success'] = "Record updated";
        header("Location: index.php");
        return;

    }
}


if (!isset($_GET['profile_id'])){
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(
        ":xyz" => $_GET['profile_id']
));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt1 = $pdo->prepare("SELECT * FROM position WHERE profile_id = :xyz");
$stmt1->execute(array(
        ":xyz" => $_GET['profile_id']
));
$row1 = $stmt1->fetchAll();
$positionLen = count($row1);
//print_r($positionLen);

if($row === false){
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <title>Sourasish Chakraborty</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
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
        <p>Last Name: <input type="text" name="last_name" size="60" value="<?php echo $row['last_name'] ?>"></p>
        <p>Email: <input type="text" name="email" size="40" value="<?php  echo $row['email'] ?>"></p>
        <p>Headline:<br><input type="text" name="headline" size="80" value="<?php echo $row['headline'] ?>"></p>
        <p>Summary:<br><textarea name="summary" rows="8" cols="80"> <?php echo $row['summary'] ?></textarea></p>

        <p>Position: <input type="submit" id="addPos" value="+"></p>
        <div id="position_fields">
            <?php
                $rank = 1;
                foreach ($row1 as $r){
                    echo '<div id="position' . $rank . '"> 
            <p>Year: <input type="text" name="year' . $rank . '" value="'. $r['year'] .'"/> 
            <input type="button" value="-" 
                onclick="$(\'#position' . $rank . '\').remove();return false;"></p> 
            <textarea name="desc' . $rank . '" rows="8" cols="80">'. $r['description'] .'</textarea>
            </div>';
                    $rank++;
                }



            ?>
        </div>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="cancel">

    </form>

    <script>
        countPos = <?php echo $positionLen; ?>;
        $(document).ready(function () {
            window.console && console.log('Document ready called');
            $('#addPos').click(function (event) {
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();
                if (countPos >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position " + countPos);
                $('#position_fields').append(
                    '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
            });
        });
    </script>



</div>
</body>
</html>


