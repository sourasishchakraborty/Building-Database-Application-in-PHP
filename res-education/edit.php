<?php
session_start();


require_once "pdo.php";
require_once "util.php";


if (!isset($_SESSION['name'])) {
    die('Access Denied');
}

if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

if (!isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = 'Missing profile_id';
    header("Location: index.php");
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])) {

    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    }

    $msg = validatePos();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    }

    $msg = validateEdu();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    }

    $stmt = $pdo->prepare('UPDATE Profile SET first_name = :fn, last_name = :ln,
email=:em, headline=:he,summary=:su
WHERE profile_id = :pid AND user_id=:uid');
    $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
    );

    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    insertPosition($pdo, $_REQUEST['profile_id']);

    $stmt = $pdo->prepare('DELETE FROM education WHERE profile_id=:pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

    insertEducation($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = 'Profile updated';
    header('Location: index.php');
    return;
}

$profile = loadPro($pdo, $_REQUEST['profile_id']);
$positions = loadPos($pdo, $_REQUEST['profile_id']);
$schools = loadEdu($pdo, $_REQUEST['profile_id']);
$positionLen = count($positions);
$schoolLen = count($schools);
?>

<!DOCTYPE html>
<html>
<head>

    <?php require_once "head.php"; ?>
    <title>Sourasish Chakraborty</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
</head>
<body>
<div class="container">
    <h1>Edit profile for UMSI</h1>

    <?php
    flashMassage();


    ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60" value="<?php echo $profile['first_name'] ?>"></p>
        <p>Last Name: <input type="text" name="last_name" size="60" value="<?php echo $profile['last_name'] ?>"></p>
        <p>Email: <input type="text" name="email" size="40" value="<?php echo $profile['email'] ?>"></p>
        <p>Headline:<br><input type="text" name="headline" size="80" value="<?php echo $profile['headline'] ?>"></p>
        <p>Summary:<br><textarea name="summary" rows="8" cols="80"><?php echo $profile['summary'] ?></textarea></p>
        <?php

        $countEdu = 0;

        echo('<p>Education: <input type="submit" id="addEdu" value="+">' . "\n");
        echo('<div id="edu_fields">');
        if (count($schools) > 0) {
            foreach ($schools as $school) {
                $countEdu++;
                echo('<div id="edu' . $countEdu . '">');
                echo
                    '<p>Year: <input type="text" name="edu_year' . $countEdu . '" value="' . $school['year'] . '">
<input type="button" value="-" onclick="$(\'#edu' . $countEdu . '\').remove();return false;\"></p>
<p>School: <input type="text" size="80" name="edu_school' . $countEdu . '" class="school" 
value="' . htmlentities($school['name']) . '" />';
                echo "\n</div>\n";
            }

        }
        echo "</div></p>\n";

        $countPos = 0;

        echo('<p>Position: <input type="submit" id="addPos" value="+">' . "\n");
        echo('<div id="position_fields">');
        if (count($positions) > 0) {
            foreach ($positions as $position) {
                $countEdu++;
                echo('<div id="position id="position' . $countPos . '">');
                echo
                    '<br>Year: <input type="text" name="year' . $countPos . '" value="' . htmlentities($position['year']) . '">
<input type="button" value="-" onclick="$(\'#position' . $countPos . '\').remove();return false;"><br>';
                echo '<textarea name="desc' . $countPos . '"rows="8" cols="80">' . "\n";
                echo htmlentities($position['description']) . "\n";
                echo "</textarea></div></div>";

            }

        }
        ?>
        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="cancel">

    </form>

    <script>
        countPos = <?php echo $positionLen; ?>;
        countEdu = <?php echo $schoolLen; ?>;
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
            $('#addEdu').click(function (event) {
                // http://api.jquery.com/event.preventdefault/
                event.preventDefault();
                if (countEdu >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countEdu++;
                window.console && console.log("Adding education " + countEdu);
                $('#edu_fields').append(
                    '<div id="edu' + countEdu + '"> \
            <p>Year: <input type="text" name="edu_year' + countEdu + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#edu' + countEdu + '\').remove();return false;"></p> \
            <p>School: <input type="text" size="80" name="edu_school' + countEdu + '" class="school" value="" /> \
            </div>');
                $('.school').autocomplete({
                    source: "school.php"
                });
            });
        });
    </script>



</div>
</body>
</html>

