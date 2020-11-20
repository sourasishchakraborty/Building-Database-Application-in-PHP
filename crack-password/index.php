<!DOCTYPE html>
<head><title>Sourasish Chakraborty MD5 Cracker</title></head>
<body>
<h1>MD5 cracker</h1>
<p>This application takes an MD5 hash
    of a four digit pin and check all 10,000 possible four digit PINs to determine the PIN.</p>
<pre>
Debug Output:
<?php
$goodtext = "Not found";
if ( isset($_GET['md5']) ) {
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];

    $txt = "0123456789";
    $show = 15;
    $count=0;

    for($i=0; $i<strlen($txt); $i++ ) {
        $ch1 = $txt[$i];   // The first of four characters

        for($j=0; $j<strlen($txt); $j++ ) {
            $ch2 = $txt[$j];  // Our second character

            for($k=0;$k<strlen($txt);$k++) {
                $ch3 = $txt[$k];      // Our third character

                for($l=0;$l<strlen($txt);$l++){
                    $ch4 = $txt[$l];    // Our fourth character
                    // Concatenate the four characters together to
                    // form the "possible" pre-hash text

                    $try1 = $ch1.$ch2;
                    $try2 = $ch3.$ch4;
                    $try = $try1.$try2;

                    // Run the hash and then check to see if we match
                    $check = hash('md5', $try);
                    if ( $check == $md5 ) {
                        $goodtext = $try;
                        break;   // Exit the inner loop
                    }

                    // Debug output until $show hits 0
                    if ( $show > 0 ) {
                        print "$check $try\n";
                        $show = $show - 1;
                    }

                    $count++;


                }

            }


        }
    }

    //Print Total checks
    print "Total checks: ";
    print $count;
    print "\n";
    // Compute elapsed time
    $time_post = microtime(true);
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
}
?>
</pre>
<!-- Use the very short syntax and call htmlentities() -->
<p>PIN: <?= htmlentities($goodtext); ?></p>
<form>
    <input type="text" name="md5" size="40" />
    <input type="submit" value="Crack MD5"/>
</form>

</body>
</html>

