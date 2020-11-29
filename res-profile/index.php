<?php
    session_start();
    require_once "pdo.php";
    $stmt = $pdo->query("SELECT profile_id, first_name, last_name, headline from users join Profile on users.user_id = Profile.user_id");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //print_r($rows);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sourasish Chakraborty</title>
    <?php require_once "bootstrap.php";?>
</head>
<body>

    <div class="container">
        <h2>Sourasish Chakraborty's Resume Registry</h2>
        <?php
        if(isset($_SESSION['success'])){
            echo '<p style="color: green">'. htmlentities($_SESSION['success']) .'</p>';
            unset($_SESSION['success']);

        }
        if(isset($_SESSION['error'])){
            echo '<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>';
            unset($_SESSION['error']);

        }
            if(isset($_SESSION['name'])){
                echo '<p><a href="logout.php">Logout</a></p>';
            }

        ?>
        <?php
            if(!isset($_SESSION['name'])){
                echo '<p><a href="login.php">Please log in</a></p>';
            }

            if(sizeof($rows)!==0){
                echo '
               <table border="1">
                <tr>
                    <th>Name</th>
                    <th>Headline</th>
                    ';
                if(isset($_SESSION['name'])){
                    echo '<th>Action</th>';
                }
                echo '
                </tr>';

                foreach ($rows as $row){
                    echo '<tr>';
                    echo("<td><a href='view.php?profile_id=" . $row['profile_id'] . "'>" . $row['first_name'] . ' ' . $row['last_name']  . "</a></td>");
                    echo '<td>'. $row['headline'] .'</td>';
                    if (isset($_SESSION['name'])){
                        echo '
                            <td><a href="edit.php?profile_id='. $row['profile_id'] .'">Edit</a> / <a href="delete.php?profile_id='. $row['profile_id'] .'">Delete</a></td>
                        ';
                    }
                    echo '</tr>
                
            ';

                }
                echo '</table>';

            }
            else {
                echo '<p>No rows found</p>';
            }



            if(isset($_SESSION['name'])){
                echo '<p><a href="add.php">Add New Entry</a></p>';
            }
        ?>


    </div>
</body>
</html>

