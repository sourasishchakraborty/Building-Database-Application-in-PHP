<?php
    session_start();
    require_once "pdo.php";

    $stmt = $pdo->query("SELECT * FROM autos");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Sourasish Chakraborty</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to Automobiles Database</h1>
    <?php
        if(isset($_SESSION['success'])){
            echo '<p style="color: green">'. htmlentities($_SESSION['success']) .'</p>';
            unset($_SESSION['success']);
        }
        if(isset($_SESSION['error'])){
            echo '<p style="color: red">'. htmlentities($_SESSION['error']) .'</p>';
            unset($_SESSION['error']);
        }


    ?>
    <ul>
        <?php
        if(isset($_SESSION['name'])) {
            if (sizeof($rows) > 0) {
                echo '<table border="1" >
                      <thead>
                        <tr>
                            <th>Make</th>
                            <th>Model</th>
                            <th>Year</th>
                            <th>Mileage</th>
                            <th>Action</th>
                        </tr>
                      </thead>                
                
                ';
                foreach ($rows as $row){
                    echo '
                        <tr>
                            <td>'. $row['make'] .'</td>
                            <td>'. $row['model'] .'</td>
                            <td>'. $row['year'] .'</td>
                            <td>'. $row['mileage'] .'</td>
                            <td><a href="edit.php?autos_id='. $row['autos_id'] .'">Edit </a>/ <a href="delete.php?autos_id='. $row['autos_id'] .'"> Delete</a></td>
                        </tr>
                    ';
                }
            } else {
                echo 'No rows found';
            }

            echo '</table></ul>';
            echo '<a href="add.php">Add New Entry </a><br><br>
               <a href="logout.php">Logout</a>
               

                ';
        }else{
            echo '<p>
<a href="login.php">Please log in</a>
</p>
<p>
Attempt to go to
<a href="add.php">add data</a> without logging in - it should fail with an error message.
</p>';
        }
        ?>





</div>
</body>
</html>
